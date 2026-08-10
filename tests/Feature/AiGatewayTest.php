<?php

namespace Tests\Feature;

use App\Domains\AI\AiGateway;
use App\Domains\AI\Providers\FakeAiProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_fake_provider_produces_structured_campaign(): void
    {
        $gateway = new AiGateway(new FakeAiProvider());

        $response = $gateway->complete(
            task: 'generate_campaign',
            messages: [['role' => 'user', 'content' => 'beauty brand']],
            options: [
                'task' => 'generate_campaign',
                'seed' => ['niche' => 'Beauty', 'target_creators' => 30],
            ]
        );

        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertSame('Beauty', $json['niche']);
        $this->assertSame(100, $json['invite_pool_size']); // 30 / 0.30
    }

    public function test_cosine_similarity_is_one_for_identical_vectors(): void
    {
        $gateway = new AiGateway(new FakeAiProvider());
        $a = array_fill(0, 64, 1.0);
        $b = $a;

        $this->assertEqualsWithDelta(1.0, $gateway->cosineSimilarity($a, $b), 0.001);
    }

    public function test_embeddings_are_stable_and_normalized(): void
    {
        $gateway = new AiGateway(new FakeAiProvider());
        [$e1, $e2] = $gateway->embed(['serum skincare', 'serum skincare']);
        $this->assertSame($e1, $e2);

        $norm = sqrt(array_sum(array_map(fn ($x) => $x * $x, $e1)));
        $this->assertEqualsWithDelta(1.0, $norm, 0.001);
    }
}
