<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable;

    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'avatar_path', 'phone',
        'last_login_at', 'system_role', 'account_status',
        'suspension_reason', 'suspended_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'suspended_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
        ];
    }

    protected $attributes = [
        'system_role'        => 'user',
        'account_status'     => 'active',
        'suspension_reason'  => null,
        'suspended_at'       => null,
    ];

    public function isSuperAdmin(): bool { return $this->system_role === 'superadmin'; }
    public function isAdmin(): bool      { return in_array($this->system_role, ['admin','superadmin'], true); }
    public function isSuspended(): bool  { return $this->account_status === 'suspended'; }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class, 'recipient_id')
            ->where('recipient_type', 'user')
            ->latest();
    }

    public function workspaces(): BelongsToMany
    {
        return $this->belongsToMany(Workspace::class, 'workspace_users')
            ->withPivot('role', 'invited_at', 'accepted_at')
            ->withTimestamps();
    }

    public function ownedAgencies()
    {
        return $this->hasMany(Agency::class, 'owner_id');
    }

    public function creator(): HasOne
    {
        return $this->hasOne(Creator::class);
    }

    public function currentWorkspaceRole(Workspace $workspace): ?string
    {
        return $this->workspaces()->where('workspaces.id', $workspace->id)->first()?->pivot->role;
    }
}
