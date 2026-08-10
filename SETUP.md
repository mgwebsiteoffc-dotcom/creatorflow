# CreatorFlow — Setup & Code Map

A production-shaped Laravel 11/13 + MySQL implementation of the CreatorFlow
platform. Both the **brand** and **creator** panels are server-rendered Blade
with a mobile-first PWA shell, sharing one backend, one database and one
creator marketplace.

## Requirements

- PHP 8.3+ with extensions: `pdo_mysql` (or `pdo_sqlite` for zero-setup local),
  `mbstring`, `openssl`, `tokenizer`, `xml`, `curl`, `bcmath`, `gd`, `intl`
- Composer 2
- MySQL 8 (or SQLite for a quick local spin-up)
- Node 20+ / npm

## First run

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate

# MySQL (production target):
#   create database creatorflow; set DB_* in .env
# Zero-setup local using SQLite:
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
touch database/database.sqlite

php artisan migrate
php artisan db:seed                 # full demo dataset (see below)
npm run dev                         # or: npm run build
php artisan serve
```

Visit <http://localhost:8000>.

### Demo logins (after `db:seed`)

| Role | Email | Password |
|------|-------|----------|
| Brand (Glow & Co., Pro plan, Shopify-connected) | `brand@creatorflow.test` | `password` |
| Creator (Jamie Rivera) | `creator@creatorflow.test` | `password` |

The seeder creates: a workspace with a Shopify + manual channel, 6 products
with variants/images and AI hero scores, 20 creators with socials/portfolio,
a launched **bulk-seed** campaign (Product A → 8, B → 5, C → 4 creators),
17 accepted assignments across every lifecycle stage, signed contracts,
unique Shopify-style discount codes, orders (gifted + attributed), content
submissions with AI scores, payouts and analytics rollups.

## AI, Shopify & Stripe in local

Defaults are **offline-safe** so the full flow runs without keys:

- `AI_DRIVER=fake` (`.env.example`) — deterministic structured AI output for
  analysis, campaign generation, briefs, outreach, content review, fraud and
  ROI. Set `AI_DRIVER=openai` + `OPENAI_API_KEY` to use the real provider.
- `FAKE_EXTERNAL_CALLS=true` — the Shopify API client logs instead of making
  HTTP calls, and the install flow fabricates an access token. Set to `false`
  with `SHOPIFY_CLIENT_ID/SECRET/WEBHOOK_SECRET` for a real store.
- Stripe payouts log when `STRIPE_SECRET` is empty; the demo marks payouts
  paid immediately so dashboards populate.

## Architecture in one paragraph

`app/Domains` holds the business logic; `app/Models` are Eloquent entities.
The **Commerce** domain defines a `CommerceChannel` contract implemented by
`ShopifyChannel` (products/inventory/discounts/draft-order gifting/webhooks)
and `ManualChannel` (CSV/manual brands). The **Campaigns** domain creates and
launches campaigns; **Matching** ranks creators; **Content** handles
submissions and AI review; **Billing** releases Stripe Connect payouts;
**Analytics** attributes orders. Heavy work runs as queued jobs under
`app/Jobs`; `app/Events` carries domain events. The HTTP layer is thin
controllers in `app/Http/Controllers/{Brand,Creator,Auth,Shopify}`.

## Key flows → code

| Flow | Entry point |
|------|-------------|
| Shopify install + OAuth | `App\Http\Controllers\Shopify\ShopifyInstallController` |
| Shopify webhooks | `ShopifyWebhookController` → `Jobs\HandleShopifyWebhook` |
| Product sync (Shopify/manual) | `Domains\Commerce\Channels\Shopify\ShopifyChannel` |
| AI store analysis | `Domains\AI\Actions\AnalyzeStore` |
| AI campaign suggestion | `Domains\AI\Actions\GenerateCampaignSuggestion` |
| Create campaign (bulk seed) | `Domains\Campaigns\Actions\CreateCampaign` |
| Launch + AI matching + invites | `Domains\Campaigns\Actions\LaunchCampaign`, `Jobs\SendCampaignInvitations` |
| Creator accepts → contract + code + order | `Domains\Campaigns\Actions\AcceptInvitation`, `Jobs\CreateCreatorOrder` |
| Creator submits content | `Domains\Content\Actions\SubmitContent`, `Jobs\RunAiContentReview` |
| Brand approves → payout | `Domains\Content\Actions\ApproveContent`, `Domains\Billing\Actions\ReleasePayout` |
| Sales attribution | `Domains\Analytics\Actions\AttributeOrder` |
| CSV import | `Http\Controllers\Brand\CsvImportController` |

## Testing

```bash
php artisan test
```

Tests use an in-memory SQLite DB and cover: bulk-seed target/invite-pool math,
launch matching, the acceptance→order→discount→payout lifecycle, discount-code
attribution, Shopify webhook verification/product upsert, and the AI gateway.

## Queues & production

- Set `QUEUE_CONNECTION=redis` and run `php artisan horizon` in production.
- Run the scheduler for nightly full reconciles, waitlist rebalancing and
  payout clawback windows (`routes/console.php`).
- File storage: set `FILESYSTEM_DISK=s3` (S3/R2/Spaces); uploads go direct
  via presigned POST.
- MySQL is the target DB; the migrations are also SQLite-compatible.
