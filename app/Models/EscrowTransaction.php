<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EscrowTransaction extends Model
{
    protected $fillable = [
        'workspace_id', 'creator_id', 'assignment_id', 'payout_id',
        'kind', 'amount_cents', 'currency', 'reference', 'note', 'performed_by',
    ];

    protected $casts = [
        'amount_cents' => 'integer',
    ];

    public function workspace(): BelongsTo    { return $this->belongsTo(Workspace::class); }
    public function creator(): BelongsTo      { return $this->belongsTo(Creator::class); }
    public function assignment(): BelongsTo   { return $this->belongsTo(CampaignAssignment::class, 'assignment_id'); }
    public function payout(): BelongsTo       { return $this->belongsTo(Payout::class); }
    public function performer(): BelongsTo    { return $this->belongsTo(User::class, 'performed_by'); }
}
