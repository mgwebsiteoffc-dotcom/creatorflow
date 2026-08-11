<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Message;
use App\Models\MessageThread;
use App\Models\MessageThreadParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isCreator = (bool) $user->creator;

        $threads = MessageThread::whereHas('participants', function ($q) use ($user, $isCreator) {
            $q->where('participant_type', $isCreator ? 'creator' : 'user')
                ->where('participant_id', $isCreator ? $user->creator->id : $user->id);
        })->with('campaign:id,title')->withCount('messages')->latest()->paginate(20);

        return view('messages.index', compact('threads', 'isCreator'));
    }

    public function show(MessageThread $thread, Request $request)
    {
        $participant = $this->participantFor($request);

        abort_unless(
            $thread->participants()
                ->where('participant_type', $participant['type'])
                ->where('participant_id', $participant['id'])
                ->exists(),
            403
        );

        $thread->participants()
            ->where('participant_type', $participant['type'])
            ->where('participant_id', $participant['id'])
            ->update(['last_read_at' => now()]);

        $messages = $thread->messages()->oldest()->paginate(50);

        return view('messages.show', compact('thread', 'messages'));
    }

    public function store(MessageThread $thread, Request $request)
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:4000']]);
        $participant = $this->participantFor($request);

        Message::create([
            'thread_id' => $thread->id,
            'sender_type' => $participant['type'] === 'creator' ? 'creator' : 'user',
            'sender_id' => $participant['id'],
            'body' => $data['body'],
            'delivered_at' => now(),
        ]);

        return back();
    }

    public function startWithCreator(Campaign $campaign, int $creatorId, Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $workspaceId = $campaign->workspace_id;

        $isBrandMember = $user->workspaces->contains('id', $workspaceId);
        $isTheCreator  = $user->creator && (int) $user->creator->id === (int) $creatorId;
        abort_unless($isBrandMember || $isTheCreator, 403);

        $thread = MessageThread::firstOrCreate(
            ['workspace_id' => $workspaceId, 'campaign_id' => $campaign->id, 'subject' => 'Campaign chat'],
            ['uuid' => (string) Str::uuid()]
        );

        // Always ensure both sides are participants.
        // Brand: pick the workspace's owner as the brand-side participant when the creator initiated.
        $brandUserId = $isBrandMember
            ? $user->id
            : optional($campaign->workspace?->users()->first())->id;

        if ($brandUserId) $this->addParticipantIfMissing($thread, 'user',    $brandUserId);
        $this->addParticipantIfMissing($thread, 'creator', $creatorId);

        return redirect()->route('messages.show', $thread);
    }

    protected function addParticipantIfMissing(MessageThread $thread, string $type, int $id): void
    {
        MessageThreadParticipant::firstOrCreate([
            'thread_id' => $thread->id,
            'participant_type' => $type,
            'participant_id' => $id,
        ]);
    }

    /**
     * @return array{type: string, id: int}
     */
    protected function participantFor(Request $request): array
    {
        $user = $request->user();

        if ($user->creator) {
            return ['type' => 'creator', 'id' => $user->creator->id];
        }

        return ['type' => 'user', 'id' => $user->id];
    }
}
