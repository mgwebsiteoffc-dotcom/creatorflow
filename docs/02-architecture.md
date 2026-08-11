# 2 — System Architecture

Stack: **Laravel 11 · PHP 8.3 · MySQL 8 · Redis · Horizon · Blade + Livewire/Alpine · Reverb · S3/R2 · Meilisearch · Stripe Connect**.

## 2.1 High-Level Architecture

```
┌──────────────────────────────────────────────────────────────────────┐
│                            EDGE / CLIENTS                             │
│  Brand PWA (Blade+Livewire)   Creator PWA (Blade+Livewire)            │
│  Shopify App (embedded, same)  Public API / Webhooks (Shopify, etc.)  │
└───────────────┬──────────────────────────────┬────────────────────────┘
                │ HTTPS                         │
        ┌───────▼────────┐              ┌───────▼────────┐
        │  Nginx / TLS   │              │  Webhook ingress│
        └───────┬────────┘              └───────┬────────┘
                │                               │
        ┌───────▼───────────────────────────────▼────────┐
        │              Laravel Application                │
        │  ┌──────────────────────────────────────────┐   │
        │  │  Web (Blade/Livewire)  ·  API (Sanctum)  │   │
        │  └──────────────────────────────────────────┘   │
        │  Domain Services (Product, Campaign, Creator,   │
        │  Order, Matching, Messaging, Billing, AI)       │
        │  ┌──────────┐ ┌──────────┐ ┌──────────────────┐ │
        │  │ Shopify  │ │ Woo/CSV  │ │  AI Gateway      │ │
        │  │ Adapter  │ │ Adapters│ │ (LLM+embeddings) │ │
        │  └────┬─────┘ └────┬─────┘ └────────┬─────────┘ │
        └───────┼────────────┼────────────────┼───────────┘
                │            │                │
   ┌────────────▼────────────▼────────────────▼───────────────┐
   │  Redis (cache, queues, Horizon)  │  MySQL 8  │  Reverb   │
   └────────────┬─────────────────────┴────┬──────┴───────────┘
                │                          │
        ┌───────▼────────┐         ┌───────▼────────┐
        │ S3 / R2 / Spaces│        │ Meilisearch    │
        │ (assets, UGC)  │         │ (creator/product search)
        └────────────────┘         └───────┬────────┘
                                           │
                                    ┌──────▼─────┐
                                    │ Stripe     │
                                    │ Connect    │
                                    └────────────┘
```

## 2.2 Multi-Tenancy

**Recommendation: single database, shared schema, with `workspace_id` on every tenant-owned row** (logical multi-tenancy using a single MySQL instance). Reasons: simpler ops, easier cross-workspace analytics, fast onboarding, lower cost at MVP scale. Add a robust global scope and per-workspace query guards.

- A **Workspace** is the brand account (e.g., "Acme Coffee").
- An **Agency** can own many workspaces (future-proof column now).
- **Creators are global**, not per-tenant: a creator record belongs to the marketplace, not a brand. This is critical — brands access the same creator pool.
- Pivot tables (`campaign_creator`, `applications`, `messages`) join global creators to tenant campaigns.

Use [stancl/tenancy](https://tenancyforlaravel.com/) only if you later need isolated databases per enterprise client; for now, a `BelongsToWorkspace` trait + global scope is enough and far simpler.

**Enforcement:**
- `WorkspaceScope` global scope on all tenant models.
- Middleware resolves `workspace_id` from the authenticated user's active session/team.
- Policies (`CampaignPolicy`, `ProductPolicy`) double-check ownership at the action level.
- Queue jobs carry `workspace_id` and re-hydrate the context on the worker.

## 2.3 Channel / Integration Architecture

Define a **Commerce channel contract** so Shopify is just one implementation.

```php
interface CommerceChannel
{
    public function syncProducts(Workspace $ws): SyncResult;
    public function syncInventory(Workspace $ws): SyncResult;
    public function syncOrders(Workspace $ws, Carbon $since): SyncResult;
    public function createDiscountCode(Product $product, Creator $creator, array $opts): DiscountCode;
    public function createOrder(CampaignAssignment $assignment): ChannelOrder;
    public function refreshWebhooks(Workspace $ws): void;
}
```

Implementations: `ShopifyChannel`, `WooCommerceChannel`, `AmazonChannel` (read-heavy), `CsvChannel`, `ManualChannel`.

### Shopify specifics
- **OAuth install** via `laravel/shopify` or a custom controller; store `shopify_domain`, `access_token` (encrypted), scopes.
- **Webhooks** (mandatory, registered on install):
  - `products/create`, `products/update`, `products/delete`
  - `inventory_levels/update`
  - `collections/update`
  - `orders/create`, `orders/updated` (for attribution + fulfillment tracking)
  - `app/uninstalled` (deactivate workspace, retain data per GDPR)
  - `shop/update`
- **Sync strategy:** webhook-driven incremental + nightly full reconcile (handles missed events).
- **Discount codes:** Shopify PriceRule/DiscountCode API — one unique code per creator per campaign (e.g., `CREATOR-JANE-10`).
- **Auto-orders:** on creator acceptance, create a **Draft Order** with the code and 100% discount (or a tagged paid order), then mark paid to trigger fulfillment. This keeps inventory and shipping in Shopify. For gifts/barter, use a `$0` tagged order so it flows through normal fulfillment.
- **Embedded app:** use Shopify App Bridge for the embedded admin experience; the same Blade/Livewire views render inside Shopify's iframe. Detect `?shop=` and embed mode via middleware.
- **Billing:** Shopify Billing API for app charges on Shopify channel; Stripe for web-channel brands (abstraction behind a `BillingProvider` interface).

### GDPR / Shopify compliance
Implement mandatory webhooks: `customers/data_request`, `customers/redact`, `shop/redact`.

## 2.4 Event-Driven Workflow

Use Laravel events/listeners + queued jobs. Domain events (examples):

| Event | Listeners / Reactions |
|---|---|
| `WorkspaceInstalled` | Trigger full product sync, enqueue `AnalyzeStore` AI job, create default team |
| `ProductsSynced` | Re-embed product catalog for search/AI, refresh hero-product scoring |
| `CampaignCreated` | Enqueue `GenerateCreatorMatches`, notify account manager |
| `CampaignLaunched` | Create discount codes, enqueue `SendInvitations`, create payout records |
| `CreatorAccepted` | Create channel order, update waitlist, send welcome message |
| `CreatorDeclined` | Invite next from waitlist |
| `ContentSubmitted` | Run `AIContentReview`, notify brand, start approval clock |
| `ContentApproved` | Release payout (or schedule), request posting links, update asset library |
| `OrderFulfilled` | Notify creator, start content-deadline timer |
| `OrderAttributed` | Update campaign analytics, recalc creator performance score |
| `InvoicePaid` | Extend subscription, send receipt |

All events publish to a **queued broadcast** for realtime UI updates via Reverb, and persist an immutable `event_log` for debugging/auditing.

## 2.5 Background Jobs (Horizon queues)

Split queues by priority:
- `high` — webhooks, OAuth, order creation, payouts
- `default` — invitations, messages, notifications
- `low` — bulk imports, CSV processing, AI analysis, nightly sync, report generation
- `ai` — LLM calls (separate so timeouts don't block)

Use **job batching** for bulk seeding (e.g., 100-creator invite batch) with progress tracking and **idempotency keys** to prevent double-invites/orders.

**Scheduling (Laravel Scheduler / cron):**
- Nightly catalog reconcile per channel
- Acceptance-rate / waitlist rebalancing
- Late-deliverable nudges
- ROI recomputation (7/30/60 day windows)
- Subscription renewal/dunning checks
- Fraud score refresh
- Analytics rollups

## 2.6 File Storage

- S3-compatible object storage (**Cloudflare R2** recommended at start: zero egress, ideal for UGC video/images; S3 for production maturity).
- Buckets: `creatorplex-uploads`, `creatorplex-ugc`, `creatorplex-exports`, `creatorplex-ai`.
- Uploads via **S3 presigned URLs / direct-to-storage** to avoid app-server bandwidth; Laravel handles authorization.
- Image variants via Glide/Imgix; video thumbnails via FFmpeg on a worker.
- Virus-scan uploads (ClamAV) and moderate images (AI safety + Rekognition optional).
- Signed, time-limited URLs for private assets (contracts, unreleased content).

## 2.7 Messaging Architecture

- **Threads** per (campaign, brand, creator) with 1:1 messaging; later group threads.
- Persisted in MySQL (`messages`, `message_participants`); cached in Redis; broadcast via Reverb.
- Notifications fan out via in-app, email, push (PWA web push), and SMS (critical).
- AI assistant can draft/suggest replies (but never send without human send, except opt-in auto-nudges).
- Email via Postmark/Resmith; transactional templates version-controlled.

## 2.8 Notification System

A `notifications` table + Laravel's notification system, with user-controlled channels per category:
- Campaign updates (acceptances, submissions, approvals)
- Messages
- Payments
- Marketing/product (opt-in)
- Critical (billing, compliance) — cannot disable

Push via web push (PWA) using the VAPID protocol; no FCM/APNs dependency needed.

## 2.9 Search

- **Meilisearch** for creator discovery (filters: niche, location, audience size, engagement, rate, barter, rating) and product search.
- Use **Laravel Scout** with the Meilisearch driver.
- Embed creator bios and product descriptions for **semantic search** (pgvector not in MySQL — use a separate vector store or Meilisearch's vector support; alternatively store embeddings in a small dedicated table or Redis Vector). At MVP, keyword + faceted search + AI ranking is enough.

## 2.10 Security & Compliance

- Encrypt Shopify access tokens and Stripe details at rest (`encrypted` cast).
- Personal data: encryption for sensitive creator PII; GDPR delete/export endpoints.
- RBAC: roles `owner`, `admin`, `manager`, `analyst`, `creator`.
- Audit log for all state-changing actions.
- Rate-limit API and webhook endpoints; verify Shopify HMAC signatures.
- Contracts e-sign via DocuSign/Dropbox Sign or a built-in lightweight consent record.
- Payout compliance via Stripe Connect (KYC on creators).
- Content moderation on uploads.
- Backup: automated MySQL snapshots + point-in-time recovery; tested restore.

## 2.11 Environments & DevOps (MVP)

- Laravel Forge / Ploi for provisioning; or Docker + an autoscaling group later.
- Separate `web` and `worker` processes; Horizon for workers.
- Staging environment with Shopify development stores.
- Sentry for errors; Laravel Pulse/Telescope for performance.
- CI: GitHub Actions (Pint, PHPStan, Pest tests) — required before merge.
- Feature flags via Laravel Pennant for gradual AI rollout.
