<?php

namespace App\Domains\AI\Contracts;

class AiResponse
{
    public function __construct(
        public string $text,
        public int $tokensIn = 0,
        public int $tokensOut = 0,
        public string $model = '',
        public ?array $structured = null,
    ) {}

    public function json(): ?array
    {
        if ($this->structured !== null) {
            return $this->structured;
        }

        $json = json_decode($this->text, true);

        return is_array($json) ? $json : null;
    }
}
