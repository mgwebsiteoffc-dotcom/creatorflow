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
use App\Support\SchemaCheck;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $hasLeads         = SchemaCheck::has('leads');
        $hasBlog          = SchemaCheck::has('blog_posts');
        $hasEscrow        = SchemaCheck::has('escrow_transactions');
        $hasApplications  = SchemaCheck::has('applications');

        $stats = [
            'users'         => User::count(),
            'creators'      => Creator::count(),
            'workspaces'    => Workspace::count(),
            'campaigns'     => Campaign::count(),
            'applications'  => $hasApplications ? Application::count() : 0,
            'leads_new'     => $hasLeads ? Lead::where('status', 'new')->count() : 0,
            'blog_posts'    => $hasBlog ? BlogPost::count() : 0,
            'gmv_cents'     => (int) Payout::where('status', 'paid')->sum('amount_cents'),
            'escrow_held'   => $hasEscrow
                ? (int) EscrowTransaction::where('kind', 'hold')->sum('amount_cents')
                  - (int) EscrowTransaction::whereIn('kind', ['release','refund'])->sum('amount_cents')
                : 0,
        ];

        $missingTables = collect([
            'leads'                => $hasLeads,
            'blog_posts'           => $hasBlog,
            'escrow_transactions'  => $hasEscrow,
            'payment_records'      => SchemaCheck::has('payment_records'),
            'platform_settings'    => SchemaCheck::has('platform_settings'),
            'campaign_references'  => SchemaCheck::has('campaign_references'),
            'creator_imports'      => SchemaCheck::has('creator_imports'),
            'notifications'        => SchemaCheck::has('notifications'),
        ])->filter(fn ($present) => ! $present)->keys()->all();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentLeads'    => $hasLeads ? Lead::latest()->take(6)->get() : collect(),
            'recentUsers'    => User::latest()->take(6)->get(),
            'recentCreators' => Creator::latest()->take(6)->get(),
            'missingTables'  => $missingTables,
        ]);
    }
}
