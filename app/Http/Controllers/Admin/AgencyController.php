<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\PlatformSetting;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404, 'Agency mode is disabled. Turn it on in Integrations → Feature flags.');

        $agencies = Agency::query()
            ->when($request->get('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->with('owner:id,name,email')
            ->withCount('workspaces')
            ->latest()
            ->paginate((int) min(200, max(10, $request->integer('per_page') ?: 25)))
            ->withQueryString();

        return view('admin.agencies.index', compact('agencies'));
    }

    public function create()
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404);
        return view('admin.agencies.create', [
            'unassignedWorkspaces' => Workspace::whereNull('agency_id')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404);
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:190'],
            'plan'          => ['nullable', 'string', 'max:40'],
            'owner_email'   => ['nullable', 'email'],
            'workspace_ids' => ['nullable', 'array'],
            'workspace_ids.*' => ['exists:workspaces,id'],
        ]);

        $owner = ! empty($data['owner_email']) ? User::firstOrCreate(
            ['email' => $data['owner_email']],
            ['name' => $data['name'].' Owner', 'password' => bcrypt(Str::random(24)), 'uuid' => (string) Str::uuid()]
        ) : null;

        $agency = Agency::create([
            'uuid'     => (string) Str::uuid(),
            'name'     => $data['name'],
            'plan'     => $data['plan'] ?? 'agency',
            'owner_id' => $owner?->id,
        ]);

        if (! empty($data['workspace_ids'])) {
            Workspace::whereIn('id', $data['workspace_ids'])->update(['agency_id' => $agency->id]);
        }

        return redirect()->route('admin.agencies.show', $agency)->with('status', "Agency '{$agency->name}' created.");
    }

    public function show(Agency $agency)
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404);
        $agency->load(['owner', 'workspaces.users']);
        $unassignedWorkspaces = Workspace::whereNull('agency_id')->orderBy('name')->get();
        return view('admin.agencies.show', compact('agency', 'unassignedWorkspaces'));
    }

    public function attachWorkspace(Agency $agency, Request $request)
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404);
        $data = $request->validate(['workspace_id' => ['required', 'exists:workspaces,id']]);
        Workspace::where('id', $data['workspace_id'])->update(['agency_id' => $agency->id]);
        return back()->with('status', 'Workspace attached to agency.');
    }

    public function detachWorkspace(Agency $agency, Workspace $workspace)
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404);
        abort_unless($workspace->agency_id === $agency->id, 403);
        $workspace->update(['agency_id' => null]);
        return back()->with('status', 'Workspace detached.');
    }

    public function destroy(Agency $agency)
    {
        abort_unless(PlatformSetting::feature('agency_mode'), 404);
        Workspace::where('agency_id', $agency->id)->update(['agency_id' => null]);
        $agency->delete();
        return redirect()->route('admin.agencies.index')->with('status', 'Agency deleted; workspaces detached (kept).');
    }
}
