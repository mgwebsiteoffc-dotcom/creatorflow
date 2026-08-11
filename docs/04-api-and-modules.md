# 4 — Domain Modules, API Design & Laravel Structure

## 4.1 Recommended Laravel Module Layout

Use **domain-oriented service structure** (not strictly `app/Models` flat). A pragmatic setup:

```
app/
  Domains/
    Commerce/
      Channels/
        Contracts/CommerceChannel.php
        Shopify/ShopifyChannel.php
        WooCommerce/WooChannel.php
        Csv/CsvChannel.php
        Manual/ManualChannel.php
      Actions/
        SyncProducts.php
        CreateDiscountCode.php
        CreateCreatorOrder.php
      Models/Product, ProductVariant, ProductImage, Collection, Order, OrderItem, DiscountCode
    Campaigns/
      Actions/
        CreateCampaign.php
        LaunchCampaign.php
        BulkSeedCampaign.php
        RecalculateWaitlist.php
      Models/Campaign, CampaignProduct, CampaignAssignment, Invitation, WaitlistEntry, Application
    Creators/
      Actions/
        UpdateProfile.php
        RefreshSocialStats.php
        CalculatePerformanceScore.php
      Models/Creator, SocialAccount, PortfolioItem, Preference, AudienceSnapshot
    Matching/
      Actions/
        GenerateMatches.php
        ScoreCreatorForCampaign.php
      MatchRanker.php
    Content/
      Actions/
        SubmitContent.php
        ReviewContent.php
        RunAIContentReview.php
      Models/ContentSubmission, ContentReview
    Messaging/
      Actions/
        SendMessage.php
        StartThread.php
      Models/MessageThread, Message
    Billing/
      Providers/StripeBillingProvider.php, ShopifyBillingProvider.php
      Actions/Subscribe.php, CancelSubscription.php, CreatePayout.php
      Models/Subscription, Invoice, Payout, Commission
    AI/
      Gateway/LlmGateway.php (OpenAI/Anthropic pluggable)
      Actions/AnalyzeStore.php, GenerateBrief.php, GenerateCampaignSuggestions.php,
              ScoreProductSuitability.php, PredictRoi.php, DraftMessage.php,
              DetectFraud.php, ReviewContent.php
      Prompts/ (version-controlled prompt templates)
    Analytics/
      Actions/AttributeOrder.php, RollupDailyStats.php
  Http/
    Controllers/
      Brand/...
      Creator/...
      Api/V1/...
      Webhooks/ShopifyWebhookController.php
    Middleware/ResolveWorkspace.php, EnsureEmbeddedShopify.php
    Livewire/ (Brand + Creator components)
  Jobs/, Events/, Listeners/, Policies/, Providers/
```

**Why this layout:** keeps "Shopify" as an adapter inside `Commerce/Channels`, not as a top-level concern. Feature work happens in domains and is channel-agnostic.

## 4.2 API Design Principles

- **Two API surfaces:**
  1. **Web/Blade** for the app (Livewire components + small JSON endpoints).
  2. **Public JSON API** (`/api/v1`) for integrations/partners and the embedded Shopify app, authenticated with **Laravel Sanctum** tokens (personal access + creator app tokens).
- Resource-oriented nouns, plural: `/api/v1/campaigns`, `/api/v1/creators`, `/api/v1/products`.
- Standard HTTP verbs; consistent error envelope.
- Pagination (`?page=&per_page=`), sorting (`?sort=-created_at`), sparse fields, and filtering (`filter[status]=active`).
- Versioned via URL; version only breaking changes.
- Idempotency on writes (`Idempotency-Key` header) for order/discount/payout creation.
- Rate limiting per token and per workspace.
- Webhooks both **incoming** (Shopify, Stripe) and **outgoing** (CreatorPlex events to brand endpoints).

### Example endpoints (Brand API)

```
POST   /api/v1/channels/shopify/install          # begin OAuth
GET    /api/v1/channels                          # list connected channels
POST   /api/v1/channels/{id}/sync                # trigger sync

GET    /api/v1/products
POST   /api/v1/products/csv                      # CSV import (presigned upload)
GET    /api/v1/products/{id}

GET    /api/v1/campaigns
POST   /api/v1/campaigns                          # create (can be AI-suggested)
POST   /api/v1/campaigns/{id}/launch
POST   /api/v1/campaigns/{id}/bulk-seed           # { products: [{id, target_creators}] }
GET    /api/v1/campaigns/{id}/matches
POST   /api/v1/campaigns/{id}/invitations/bulk
POST   /api/v1/campaigns/{id}/applications/{applicationId}/approve

GET    /api/v1/creators?filter[niches][]=beauty&filter[followers_min]=10000
GET    /api/v1/creators/{id}
POST   /api/v1/creators/{id}/invite

GET    /api/v1/assignments
POST   /api/v1/assignments/{id}/order             # manual channel order creation
POST   /api/v1/assignments/{id}/approve-content

GET    /api/v1/analytics/campaigns/{id}
GET    /api/v1/analytics/attribution
```

### Example endpoints (Creator API / PWA)

```
GET    /api/v1/me/profile
PATCH  /api/v1/me/profile
POST   /api/v1/me/social-accounts
GET    /api/v1/me/portfolio
POST   /api/v1/me/portfolio
GET    /api/v1/me/campaigns                       # discover marketplace
POST   /api/v1/me/campaigns/{id}/apply
GET    /api/v1/me/assignments
POST   /api/v1/me/assignments/{id}/submit-content
GET    /api/v1/me/payouts
POST   /api/v1/me/onboard-stripe                  # Stripe Connect Express link
GET    /api/v1/me/threads
POST   /api/v1/me/threads/{id}/messages
```

## 4.3 Shopify Webhook Handling

- Single controller verifies **HMAC signature**, normalizes payload, dispatches a queued job per topic (never do synchronous work in the webhook request — Shopify timeouts are tight).
- Use a `webhook_id` idempotency key to skip retries.
- Respond `200` immediately.
- Topics → jobs:
  - `products/*` → `UpsertProductFromShopify`
  - `inventory_levels/update` → `UpdateInventory`
  - `orders/create` / `orders/updated` → `UpsertOrderFromShopify` (runs attribution)
  - `app/uninstalled` → `DeactivateWorkspace`
  - GDPR topics → `ProcessGdprRequest`

## 4.4 Event & Webhook Outbound

Allow brands to subscribe to CreatorPlex events (`campaign.launched`, `content.submitted`, `assignment.completed`) via a signed webhook URL. Useful for agencies and custom-stack D2C brands. Store endpoints + signing secret in `webhook_endpoints`, deliver with retries + backoff.

## 4.5 PWA / Frontend Approach

- **Blade + Livewire 3 + Alpine.js** for interactive UIs (no separate SPA needed — fastest path for a small team, SEO-friendly public pages, shared auth).
- **Tailwind CSS** for mobile-first responsive design.
- **PWA:** Service worker (Workbox/Vite PWA plugin), web manifest, install prompt, offline shell for read-only screens, web push notifications, camera capture for content uploads.
- Two route groups with their own layouts/nav:
  - `/brand/*` — brand dashboard
  - `/creator/*` — creator portal
- Shared component library (buttons, cards, modals, tables with responsive "card on mobile" pattern).
- Use **Laravel Reverb** for realtime messaging/typing indicators/live campaign progress.
- File uploads direct to S3/R2 using presigned POST; chunked/Resumable.js for large video from phones.

## 4.6 Key Actions (the "use case" layer)

Business logic lives in Action classes, callable from controllers, Livewire, jobs, and AI/tinker. Examples:

- `LaunchCampaign` — validates, sets status, creates discount codes per matched creator, enqueues invitations, opens waitlist, emits `CampaignLaunched`.
- `BulkSeedCampaign` — accepts `[{product_id, variant_id, target_creators, fee}]`, creates `campaign_products`, computes invite pools from acceptance rates, kicks off matching.
- `HandleCreatorAcceptance` — signs/requests contract, creates channel order/discount, updates counts, promotes next waitlist entry if over-subscribed? actually decrements open slots, sends tracking.
- `AttributeOrder` — on incoming Shopify order, match by discount code / referral link / survey; write `attributions` across models; update rollups.
- `ProcessContentSubmission` — persist asset, enqueue AI review, notify brand.
- `ReleasePayout` — after approval + (optional) clawback window, create Stripe transfer.

## 4.7 Permissions / Policies

- Policies per domain model (`CampaignPolicy`, `ProductPolicy`, `AssignmentPolicy`, `PayoutPolicy`).
- Brand roles: `owner`, `admin`, `manager`, `analyst` (analyst read-only).
- Creator can only see their own assignments/messages/payouts.
- API token scopes: `campaigns:write`, `products:read`, `content:write`, etc.

## 4.8 Testing Strategy

- **Pest/PHPUnit** unit tests for Actions and domain services.
- Feature tests for API endpoints and webhook signature verification.
- A Shopify stub/fake for the channel contract (contract tests: every channel adapter passes the same suite).
- AI gateway faked in tests (no live LLM calls in CI); snapshot-test prompt construction.
- Horizon-tested job chains for bulk seeding.
