<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\CreatorImport;
use App\Models\CreatorNiche;
use App\Models\CreatorSocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreatorController extends Controller
{
    public function index(Request $request)
    {
        $creators = Creator::query()
            ->when($request->get('q'), fn ($q, $term) => $q->where('display_name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%"))
            ->when($request->get('status'), fn ($q, $s) => $q->where('status', $s))
            ->withCount(['assignments'])
            ->latest()
            ->paginate((int) min(200, max(10, $request->integer('per_page') ?: 25)))
            ->withQueryString();

        return view('admin.creators.index', compact('creators'));
    }

    public function show(Creator $creator)
    {
        $creator->load(['user', 'nicheRows', 'socialAccounts', 'portfolioItems']);

        $assignments = $creator->assignments()
            ->with(['campaign:id,title,workspace_id', 'campaign.workspace:id,name', 'campaignProduct.product:id,title'])
            ->latest()->take(30)->get();

        $applications = \App\Support\SchemaCheck::has('applications')
            ? $creator->applications()->with('campaign:id,title,workspace_id', 'campaign.workspace:id,name')->latest()->take(20)->get()
            : collect();

        $payouts = \App\Models\Payout::where('creator_id', $creator->id)
            ->with('assignment:id,campaign_id')->latest()->take(20)->get();

        $stats = [
            'assignments_total' => $creator->assignments()->count(),
            'assignments_done'  => $creator->assignments()->whereIn('status', ['approved','completed'])->count(),
            'earnings_paid'     => (int) $creator->payouts()->where('status', 'paid')->sum('net_cents'),
            'earnings_pending'  => (int) $creator->payouts()->where('status', 'pending')->sum('net_cents'),
        ];

        return view('admin.creators.show', compact('creator', 'assignments', 'applications', 'payouts', 'stats'));
    }

    public function suspend(Creator $creator, Request $request)
    {
        $creator->update([
            'status'            => 'suspended',
            'suspension_reason' => $request->input('reason'),
            'suspended_at'      => now(),
        ]);

        return back()->with('status', "{$creator->display_name} has been banned.");
    }

    public function reinstate(Creator $creator)
    {
        $creator->update([
            'status'            => 'active',
            'suspension_reason' => null,
            'suspended_at'      => null,
        ]);

        return back()->with('status', "{$creator->display_name} has been reinstated.");
    }

    /**
     * Bulk action on a set of creator IDs — approve / suspend / delete / tag.
     * All operations are guarded by "no-op if same-state" so accidental
     * double-clicks are safe.
     */
    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action'       => ['required', 'in:approve,suspend,delete,tag_verified,tag_pending'],
            'creator_ids'  => ['required', 'array', 'min:1'],
            'creator_ids.*'=> ['integer'],
            'reason'       => ['nullable', 'string', 'max:190'],
        ]);

        $ids = array_values(array_unique(array_map('intval', $data['creator_ids'])));
        $count = 0;

        switch ($data['action']) {
            case 'approve':
                $count = Creator::whereIn('id', $ids)->update([
                    'status'            => 'active',
                    'suspension_reason' => null,
                    'suspended_at'      => null,
                ]);
                $msg = "Approved {$count} creator(s).";
                break;

            case 'suspend':
                $count = Creator::whereIn('id', $ids)->update([
                    'status'            => 'suspended',
                    'suspension_reason' => $data['reason'] ?? 'Bulk action by admin',
                    'suspended_at'      => now(),
                ]);
                $msg = "Suspended {$count} creator(s).";
                break;

            case 'tag_verified':
                // No 'is_verified' column yet — 'active' is the current
                // marker for a vetted creator. Once migration adds the
                // column this becomes a real toggle.
                $count = Creator::whereIn('id', $ids)->update(['status' => 'active']);
                $msg = "Marked {$count} creator(s) as verified (active).";
                break;

            case 'tag_pending':
                $count = Creator::whereIn('id', $ids)->update(['status' => 'pending']);
                $msg = "Moved {$count} creator(s) back to pending review.";
                break;

            case 'delete':
                // Soft-guard: refuse if any of the selected creators have an
                // active or completed assignment — they're historical records now.
                $blocked = Creator::whereIn('id', $ids)
                    ->has('assignments')
                    ->pluck('display_name')
                    ->take(3)
                    ->all();
                if (! empty($blocked)) {
                    return back()->with('error',
                        'Cannot delete: '.implode(', ', $blocked).' have historical assignments. Suspend instead.'
                    );
                }
                $count = Creator::whereIn('id', $ids)->delete();
                $msg = "Deleted {$count} creator(s).";
                break;
        }

        return back()->with('status', $msg ?? "Applied to {$count} creator(s).");
    }

    public function importForm()
    {
        return view('admin.creators.import');
    }

    public function importStore(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = null;
        $rows = 0; $imported = 0; $failed = 0; $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (! $header) {
                $header = array_map(fn ($h) => strtolower(trim($h)), $row);
                continue;
            }
            $rows++;
            $data = @array_combine($header, $row) ?: [];
            try {
                $email = $data['email'] ?? null;
                $name  = $data['name'] ?? $data['display_name'] ?? null;
                if (! $name) throw new \RuntimeException('Missing name');

                $user = $email
                    ? User::firstOrCreate(
                        ['email' => $email],
                        [
                            'uuid' => (string) Str::uuid(),
                            'name' => $name,
                            'password' => Hash::make(Str::random(24)),
                        ],
                    )
                    : null;

                $followers = (int) ($data['followers'] ?? 0);
                $creator = Creator::create([
                    'uuid'         => (string) Str::uuid(),
                    'user_id'      => $user?->id,
                    'display_name' => $name,
                    'slug'         => $this->uniqueSlug($name),
                    'bio'          => $data['bio'] ?? null,
                    'email'        => $email,
                    'country'      => strtoupper((string) ($data['country'] ?? '')) ?: null,
                    'city'         => $data['city'] ?? null,
                    'state'        => $data['state'] ?? null,
                    'gender'       => in_array(strtolower((string) ($data['gender'] ?? '')), ['female','male','non_binary','other']) ? strtolower($data['gender']) : null,
                    'age_range'    => in_array((string) ($data['age_range'] ?? ''), ['13-17','18-24','25-34','35-44','45-54','55+']) ? $data['age_range'] : null,
                    'tier'         => \App\Support\CreatorTaxonomy::tierFromFollowers($followers),
                    'languages'    => array_filter(array_map('trim', explode(',', $data['languages'] ?? ''))),
                    'niches'       => array_filter(array_map('trim', explode(',', $data['niches'] ?? ''))),
                    'status'       => 'active',
                    'open_to_work' => true,
                    'accepts_barter' => filter_var($data['accepts_barter'] ?? true, FILTER_VALIDATE_BOOLEAN),
                    'accepts_paid'   => filter_var($data['accepts_paid']   ?? true, FILTER_VALIDATE_BOOLEAN),
                    'rate_ugc_cents' => isset($data['rate_ugc_cents']) ? (int) $data['rate_ugc_cents'] : null,
                    'follower_count_total' => $followers,
                    'engagement_rate' => (float) ($data['engagement_rate'] ?? 0),
                    'audience_female_pct' => isset($data['audience_female_pct']) ? (int) $data['audience_female_pct'] : null,
                    'audience_male_pct'   => isset($data['audience_male_pct'])   ? (int) $data['audience_male_pct']   : null,
                    'performance_score' => 60,
                    'fraud_risk' => 5,
                ]);

                foreach ($creator->niches ?? [] as $niche) {
                    CreatorNiche::create(['creator_id' => $creator->id, 'niche' => $niche]);
                }

                foreach (['instagram','tiktok','youtube'] as $platform) {
                    $key = "{$platform}_handle";
                    if (! empty($data[$key])) {
                        CreatorSocialAccount::create([
                            'creator_id'     => $creator->id,
                            'platform'       => $platform,
                            'handle'         => ltrim($data[$key], '@'),
                            'follower_count' => (int) ($data['followers'] ?? 0),
                            'engagement_rate' => (float) ($data['engagement_rate'] ?? 0),
                        ]);
                    }
                }

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "Row {$rows}: ".$e->getMessage();
            }
        }
        fclose($handle);

        if (\App\Support\SchemaCheck::has('creator_imports')) {
            CreatorImport::create([
                'user_id'       => $request->user()->id,
                'source'        => 'csv',
                'filename'      => $file->getClientOriginalName(),
                'total_rows'    => $rows,
                'imported_rows' => $imported,
                'failed_rows'   => $failed,
                'errors'        => array_slice($errors, 0, 100),
                'status'        => $failed > 0 ? 'partial' : 'completed',
            ]);
        }

        return redirect()->route('admin.creators.index')
            ->with('status', "Imported {$imported} creator(s). {$failed} failed.");
    }

    protected function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'creator';
        $slug = $base; $i = 1;
        while (Creator::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
