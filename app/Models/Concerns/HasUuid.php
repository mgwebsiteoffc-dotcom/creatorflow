<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait HasUuid
{
    public static function bootHasUuid(): void
    {
        static::creating(function (Model $model): void {
            if (empty($model->uuid) && Schema::hasColumn($model->getTable(), 'uuid')) {
                $model->uuid = (string) Str::uuid();
            }
        });

        // Backfill uuid on retrieval for rows created before the uuid column
        // was added or by raw SQL. Prevents 'Missing parameter' when a link
        // like route('admin.workspaces.show', $w) is generated.
        static::retrieved(function (Model $model): void {
            if (empty($model->uuid) && Schema::hasColumn($model->getTable(), 'uuid') && $model->exists) {
                $model->uuid = (string) Str::uuid();
                try {
                    $model->saveQuietly();
                } catch (\Throwable) {
                    // Column may be strictly nullable + writable, ignore if not.
                }
            }
        });
    }

    /**
     * Prefer uuid as the URL key when it's actually populated; fall back to
     * the primary key so route(...) never blows up with a missing parameter.
     */
    public function getRouteKeyName(): string
    {
        try {
            return (! empty($this->uuid) || Schema::hasColumn($this->getTable(), 'uuid'))
                ? 'uuid'
                : $this->getKeyName();
        } catch (\Throwable) {
            return $this->getKeyName();
        }
    }

    /**
     * Guarantee `route(..., $model)` always has *something* to interpolate —
     * even if uuid is null on this instance, use the id.
     */
    public function getRouteKey(): mixed
    {
        return $this->uuid ?: $this->getKey();
    }

    /**
     * Route-model bind by uuid OR numeric id — matches whatever the URL has.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value) && Schema::hasColumn($this->getTable(), 'id')) {
            return $this->where($this->getKeyName(), $value)->first();
        }
        if (Schema::hasColumn($this->getTable(), 'uuid')) {
            return $this->where('uuid', $value)->first();
        }
        return parent::resolveRouteBinding($value, $field);
    }
}
