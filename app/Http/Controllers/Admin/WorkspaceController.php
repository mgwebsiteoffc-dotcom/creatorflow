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
            ->paginate((int) min(200, max(10, $request->integer('per_page') ?: 25)))
            ->withQueryString();

        return view('admin.workspaces.index', compact('workspaces'));
    }

    public function show(Workspace $workspace)
    {
        $workspace->load(['users', 'channels', 'subscription']);

        $campaigns = $workspace->campaigns()->withCount(['assignments','products'])->latest()->take(20)->get();
        $products  = $workspace->products()->latest()->take(12)->get();
        $invoices  = \App\Support\SchemaCheck::has('invoices')
            ? $workspace->invoices()->latest()->take(20)->get()
            : collect();
        $payments  = \App\Support\SchemaCheck::has('payment_records')
            ? $workspace->paymentRecords()->latest()->take(20)->get()
            : collect();

        $stats = [
            'campaigns'   => $workspace->campaigns()->count(),
            'products'    => $workspace->products()->count(),
            'team'        => $workspace->users()->count(),
            'gmv'         => (int) \App\Models\Attribution::where('workspace_id', $workspace->id)->sum('revenue_cents'),
            'paid'        => \App\Support\SchemaCheck::has('payment_records')
                ? (int) $workspace->paymentRecords()->where('direction','outflow')->where('status','succeeded')->sum('amount_cents')
                : 0,
        ];

        return view('admin.workspaces.show', compact('workspace','campaigns','products','invoices','payments','stats'));
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
