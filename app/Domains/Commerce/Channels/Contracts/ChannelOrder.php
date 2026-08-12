<?php

namespace App\Domains\Commerce\Channels\Contracts;

/**
 * Immutable result of creating a creator order through a channel.
 */
class ChannelOrder
{
    public function __construct(
        public string $externalId,
        public string $orderNumber,
        public string $status,
        public int $totalCents,
        public string $currency = 'INR',
        public ?string $trackingNumber = null,
        public array $raw = [],
    ) {}

    public static function fake(int $totalCents = 0, string $currency = 'INR'): self
    {
        $id = 'fake_'.bin2hex(random_bytes(6));

        return new self(
            externalId: $id,
            orderNumber: '#CF-'.strtoupper(substr($id, -8)),
            status: 'open',
            totalCents: $totalCents,
            currency: $currency,
            raw: ['source' => 'fake-driver'],
        );
    }
}
