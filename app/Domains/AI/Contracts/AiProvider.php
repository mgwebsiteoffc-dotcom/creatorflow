<?php

namespace App\Domains\AI\Contracts;

interface AiProvider
{
    /**
     * Run a chat completion given a list of messages.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function complete(array $messages, array $options = []): AiResponse;

    /**
     * Generate embeddings for one or more text inputs.
     *
     * @param  array<int, string>  $inputs
     * @return array<int, array<int, float>>
     */
    public function embed(array $inputs): array;
}
