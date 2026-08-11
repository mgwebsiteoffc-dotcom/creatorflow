<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\BlogPost;
use App\Models\Campaign;
use App\Models\Creator;
use App\Models\EscrowTransaction;
use App\Models\Lead;
use App\Models\Payout;
use App\Models\User;
use App\Models\Workspace;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('admin.dashboard', [
            'stats' => [
                'users'          => User::count(),
                'creators'       => Creator::count(),
                'workspaces'     => Workspace::count(),
                'campaigns'      => Campaign::count(),
                'applications'   => Application::count(),
                'leads_new'      => Lead::where('status', 'new')->count(),
                'blog_posts'     => BlogPost::count(),
                'gmv_cents'      => (int) Payout::where('status', 'paid')->sum('amount_cents'),
                'escrow_held'    => (int) EscrowTransaction::where('kind', 'hold')->sum('amount_cents')
                                     - (int) EscrowTransaction::whereIn('kind', ['release','refund'])->sum('amount_cents'),
            ],
            'recentLeads'    => Lead::latest()->take(6)->get(),
            'recentUsers'    => User::latest()->take(6)->get(),
            'recentCreators' => Creator::latest()->take(6)->get(),
        ]);
    }
}
