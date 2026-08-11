<?php

namespace App\Domains\Commerce\Channels\Shopify;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin, testable wrapper around the Shopify Admin REST/GraphQL API.
 *
 * When config('creatorflow.demo.fake_external_calls') is true (the default in
 * local/dev), no HTTP call is made and a stub payload is returned. Seeders
 * and tests rely on this; production sets FAKE_EXTERNAL_CALLS=false.
 */
class ShopifyApiClient
{
    public function __construct(
        public string $shopDomain,
        protected string $accessToken,
        public string $apiVersion,
        protected bool $fake = true,
    ) {}

    public function graphql(string $query, array $variables = []): array
    {
        if ($this->fake) {
            return ['data' => [], 'fake' => true];
        }

        return $this->request('POST', "/admin/api/{$this->apiVersion}/graphql.json", [
            'query' => $query,
            'variables' => $variables,
        ]);
    }

    public function get(string $path, array $query = []): array
    {
        return $this->request('GET', $path, [], $query);
    }

    public function post(string $path, array $body = []): array
    {
        return $this->request('POST', $path, $body);
    }

    public function put(string $path, array $body = []): array
    {
        return $this->request('PUT', $path, $body);
    }

    public function delete(string $path): array
    {
        return $this->request('DELETE', $path);
    }

    protected function request(string $method, string $path, array $body = [], array $query = []): array
    {
        if ($this->fake) {
            Log::debug('Shopify API (fake)', compact('method', 'path', 'body'));

            return ['fake' => true, 'path' => $path];
        }

        $url = "https://{$this->shopDomain}{$path}";

        /** @var Response $response */
        $response = Http::withToken($this->accessToken)
            ->accept('application/json')
            ->asJson()
            ->send($method, $url, ['query' => $query, 'json' => $body]);

        if ($response->failed()) {
            throw new RuntimeException(
                "Shopify API error {$response->status()} on {$method} {$path}: ".$response->body()
            );
        }

        return $response->json() ?? [];
    }
}
