<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workspace;
use Illuminate\Http\Request;

class WorkspaceController extends Controller
{
    public function index(Request $request)
    {
        $workspaces = Workspace::query()
            ->when($request->get('q'), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->when($request->get('status') === 'suspended', fn ($q) => $q->where('account_status', 'suspended'))
            ->withCount(['campaigns', 'products', 'users'])
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.workspaces.index', compact('workspaces'));
    }

    public function suspend(Workspace $workspace, Request $request)
    {
        $workspace->update([
            'account_status'    => 'suspended',
            'suspension_reason' => $request->input('reason'),
            'suspended_at'      => now(),
        ]);
        return back()->with('status', "{$workspace->name} has been suspended.");
    }

    public function reinstate(Workspace $workspace)
    {
        $workspace->update([
            'account_status'    => 'active',
            'suspension_reason' => null,
            'suspended_at'      => null,
        ]);
        return back()->with('status', "{$workspace->name} has been reinstated.");
    }
}
