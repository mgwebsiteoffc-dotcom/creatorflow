<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasUuid, Notifiable;

    protected $fillable = [
        'uuid', 'name', 'email', 'password', 'avatar_path', 'phone',
        'last_login_at',
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_secret' => 'encrypted',
        ];
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
