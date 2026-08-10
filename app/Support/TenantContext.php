<?php

namespace App\Support;

use App\Models\Workspace;

/**
 * Holds the active workspace for the current request/job lifecycle.
 *
 * Resolved from the authenticated user's active workspace on web requests
 * (via the ResolveWorkspace middleware) and re-hydrated on queued jobs that
 * implement TenantAware. This keeps domain services workspace-agnostic while
 * ensuring every query is scoped correctly.
 */
class TenantContext
{
    protected ?Workspace $workspace = null;

    public function activate(Workspace $workspace): void
    {
        $this->workspace = $workspace;
    }

    public function active(): ?Workspace
    {
        return $this->workspace;
    }

    public function id(): ?int
    {
        return $this->workspace?->id;
    }

    public function clear(): void
    {
        $this->workspace = null;
    }

    public function check(): Workspace
    {
        return $this->workspace ?? throw new \RuntimeException(
            'No active workspace. Activate a workspace via TenantContext first.'
        );
    }
}
