<?php

namespace App\Domains\Commerce\Channels\Contracts;

class SyncResult
{
    /**
     * @param  array<int, array<string, mixed>>  $created
     * @param  array<int, array<string, mixed>>  $updated
     * @param  array<int, string>  $errors
     */
    public function __construct(
        public array $created = [],
        public array $updated = [],
        public array $errors = [],
    ) {}

    public function processed(): int
    {
        return count($this->created) + count($this->updated);
    }

    public function errorCount(): int
    {
        return count($this->errors);
    }

    public function merge(self $other): self
    {
        return new self(
            created: array_merge($this->created, $other->created),
            updated: array_merge($this->updated, $other->updated),
            errors: array_merge($this->errors, $other->errors),
        );
    }
}
