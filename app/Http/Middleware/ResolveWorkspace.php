<?php

namespace App\Http\Middleware;

use App\Models\Workspace;
use App\Support\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the active workspace from the authenticated user and activates it
 * in TenantContext for the rest of the request.
 *
 * The workspace is determined by:
 *   1. A route parameter ({workspace})
 *   2. The X-Workspace-Id header
 *   3. The user's first workspace
 */
class ResolveWorkspace
{
    public function __construct(protected TenantContext $tenant) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $workspaceId = $request->route('workspace')
            ?? $request->header('X-Workspace-Id')
            ?? $request->session()->get('active_workspace_id');

        $workspace = $workspaceId
            ? $user->workspaces()->where('workspaces.id', $workspaceId)->first()
            : $user->workspaces()->first();

        if (! $workspace instanceof Workspace) {
            abort(403, 'No workspace is available for this account.');
        }

        $this->tenant->activate($workspace);
        $request->setUserResolver(fn () => $user->setRelation('activeWorkspace', $workspace));
        view()->share('currentWorkspace', $workspace);

        return $next($request);
    }
}
