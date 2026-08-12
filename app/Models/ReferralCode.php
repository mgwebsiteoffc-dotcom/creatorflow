<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid','owner_type','owner_id','code','label','kind',
        'commission_rate','commission_fixed_cents',
        'signup_count','conversion_count','lifetime_commission_cents',
        'status',
    ];

    protected $casts = [
        'commission_rate' => 'float',
    ];
}
