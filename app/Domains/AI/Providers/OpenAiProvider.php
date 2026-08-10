<?php

namespace App\Domains\AI\Providers;

use App\Domains\AI\Contracts\AiProvider;
use App\Domains\AI\Contracts\AiResponse;
use Illuminate\Support\Facades\Http;

/**
 * OpenAI-compatible chat + embeddings provider. Used in production when
 * AI_DRIVER=openai and OPENAI_API_KEY is set.
 */
class OpenAiProvider implements AiProvider
{
    public function __construct(
        protected string $apiKey,
        protected string $model,
        protected string $embeddingModel,
        protected int $timeout = 30,
    ) {}

    public function complete(array $messages, array $options = []): AiResponse
    {
        $json = [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'temperature' => $options['temperature'] ?? 0.4,
        ];

        if (! empty($options['json'])) {
            $json['response_format'] = ['type' => 'json_object'];
        }

        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->asJson()
            ->post('https://api.openai.com/v1/chat/completions', $json)
            ->throw()
            ->json();

        return new AiResponse(
            text: $response['choices'][0]['message']['content'] ?? '',
            tokensIn: (int) ($response['usage']['prompt_tokens'] ?? 0),
            tokensOut: (int) ($response['usage']['completion_tokens'] ?? 0),
            model: (string) ($response['model'] ?? $this->model),
        );
    }

    public function embed(array $inputs): array
    {
        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->asJson()
            ->post('https://api.openai.com/v1/embeddings', [
                'model' => $this->embeddingModel,
                'input' => array_values($inputs),
            ])
            ->throw()
            ->json();

        return array_map(
            fn (array $item) => $item['embedding'],
            $response['data'] ?? []
        );
    }
}
