<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CreatorPlex platform configuration
    |--------------------------------------------------------------------------
    |
    | These values drive the core domain: plans, AI drivers, marketplace
    | commission defaults, and the Shopify adapter. Everything here can be
    | overridden via environment variables.
    |
    */

    'ai' => [
        'driver' => env('AI_DRIVER', 'fake'),
        'openai' => [
            'key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'embedding_model' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-3-small'),
            'timeout' => 30,
        ],
        // Caps per workspace per billing period (used by the entitlement gate).
        'credits' => [
            'free' => 10,
            'starter' => 50,
            'pro' => 500,
            'growth' => 5000,
            'enterprise' => 1_000_000,
        ],
    ],

    'plans' => [
        'free' => [
            'name' => 'Free',
            'price' => 0,
            'seats' => 1,
            'products' => 25,
            'active_campaigns' => 1,
            'monthly_invites' => 50,
            'channels' => 1,
            'bulk_seed' => false,
            'ai_content_review' => false,
            'fraud_detection' => false,
            'roi_prediction' => true,
            'affiliate' => false,
            'api_access' => false,
        ],
        'starter' => [
            'name' => 'Starter',
            'price' => 49,
            'seats' => 2,
            'products' => 250,
            'active_campaigns' => 5,
            'monthly_invites' => 500,
            'channels' => 1,
            'bulk_seed' => false,
            'ai_content_review' => true,
            'fraud_detection' => false,
            'roi_prediction' => true,
            'affiliate' => false,
            'api_access' => false,
        ],
        'pro' => [
            'name' => 'Pro',
            'price' => 199,
            'seats' => 5,
            'products' => 2500,
            'active_campaigns' => 25,
            'monthly_invites' => 5000,
            'channels' => 3,
            'bulk_seed' => true,
            'ai_content_review' => true,
            'fraud_detection' => true,
            'roi_prediction' => true,
            'affiliate' => true,
            'api_access' => true,
        ],
        'growth' => [
            'name' => 'Growth',
            'price' => 499,
            'seats' => 10,
            'products' => 25000,
            'active_campaigns' => 100,
            'monthly_invites' => 25000,
            'channels' => 10,
            'bulk_seed' => true,
            'ai_content_review' => true,
            'fraud_detection' => true,
            'roi_prediction' => true,
            'affiliate' => true,
            'api_access' => true,
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => null,
            'seats' => 1_000_000,
            'products' => 1_000_000,
            'active_campaigns' => 1_000_000,
            'monthly_invites' => 1_000_000,
            'channels' => 1_000_000,
            'bulk_seed' => true,
            'ai_content_review' => true,
            'fraud_detection' => true,
            'roi_prediction' => true,
            'affiliate' => true,
            'api_access' => true,
        ],
    ],

    'marketplace' => [
        // Platform fee on the creator fee for paid campaigns (waived on growth/enterprise).
        'paid_platform_fee_rate' => 0.10,
        'affiliate_commission_rate' => 0.02,
        'payout_clawback_days' => 7,
    ],

    'shopify' => [
        'client_id' => env('SHOPIFY_CLIENT_ID'),
        'client_secret' => env('SHOPIFY_CLIENT_SECRET'),
        'api_version' => env('SHOPIFY_API_VERSION', '2025-01'),
        'webhook_secret' => env('SHOPIFY_WEBHOOK_SECRET'),
        'app_bridge' => env('SHOPIFY_APP_BRIDGE', true),
        'scopes' => [
            'read_products',
            'write_products',
            'read_inventory',
            'read_orders',
            'write_orders',
            'read_discounts',
            'write_discounts',
            'read_customers',
            'read_content',
        ],
    ],

    'demo' => [
        // When true, the Shopify API client logs calls instead of making them.
        'fake_external_calls' => env('FAKE_EXTERNAL_CALLS', true),
    ],
];
