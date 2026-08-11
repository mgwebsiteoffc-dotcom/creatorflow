# CreatorPlex

CreatorPlex is a unified, AI-powered **creator-commerce platform** for brands and creators. Two entry points — a **Shopify App** and a standalone **Web Platform** — share one backend, one creator marketplace, one campaign engine, one messaging system and one database.

This is a real, runnable Laravel implementation (not a prototype): migrations, models, a `CommerceChannel` contract with a Shopify adapter, AI campaign/matching/content flows, queued jobs, events, a full demo seeder, and mobile-first PWA panels for both brands and creators.

> Design docs live in [`docs/`](docs/). Start with [`docs/EXECUTIVE-SUMMARY.md`](docs/EXECUTIVE-SUMMARY.md).

## Stack

- **Backend:** Laravel (PHP 8.3)
- **Database:** MySQL 8 (SQLite works for zero-setup local)
- **Frontend:** Blade + Tailwind, mobile-first **PWA** (service worker, manifest, web push)
- **Queue/Cache:** Redis/Horizon in production (database driver by default)
- **Storage:** S3/R2-compatible (local by default)
- **Payments:** Stripe Connect (fake/log driver in local)
- **AI:** pluggable gateway with a deterministic `fake` driver (OpenAI-compatible driver included)

## Quick start

```bash
composer install
npm install && npm run build
cp .env.example .env
php artisan key:generate

# SQLite (zero setup) — switch the DB line in .env, then:
touch database/database.sqlite
# OR create a MySQL `creatorplex` database and keep DB_CONNECTION=mysql

php artisan migrate
php artisan db:seed          # full demo dataset
php artisan serve
```

Open <http://localhost:8000>.

### Demo logins (after seeding)

| Role | Email | Password |
|------|-------|----------|
| Brand (Pro, Shopify-connected) | `brand@creatorplex.test` | `password` |
| Creator | `creator@creatorplex.test` | `password` |

See [SETUP.md](SETUP.md) for the full code map, environment variables and testing.

## What's implemented

**Channels & catalog**
- `CommerceChannel` contract + `ChannelRegistry`
- Shopify adapter: OAuth install, webhook ingestion, product/variant/image sync, unique discount codes, draft-order gifting, order attribution
- Manual channel + CSV product importer for non-Shopify brands
- Products, variants, images, collections, sync jobs

**Campaigns & bulk seeding**
- Campaign types: barter, paid, affiliate, hybrid
- Bulk seed: `Product → N creators` with invite-pool math from assumed acceptance rate + waitlists
- AI campaign suggestions, briefs, product/hero analysis
- AI creator matching (niche, engagement, performance, fraud, semantic similarity)
- Invitations, applications, accept/decline, assignments lifecycle

**Operations**
- Contracts + e-sign record, unique discount codes, auto order creation
- Content upload (direct-to-storage ready), AI content review, approve/request-changes
- Stripe Connect payouts (platform fee by plan, instant-pay capable)
- Multi-source attribution (discount code first, then referral metadata) + daily rollups
- 1:1 brand↔creator messaging

**Creator portal (PWA)**
- Onboarding, profile, niches, rates, social links, portfolio
- Marketplace, applications, invitations
- Assignments: contract sign, tracking, content upload from phone camera
- Earnings/payout history

**Brand dashboard (PWA)**
- Home with AI campaign suggestion card
- Campaigns (wizard + bulk-seed targets), products, creator marketplace, assignments/content inbox, analytics
- Shopify install + onboarding for manual/CSV products

**Platform**
- Logical multi-tenancy (`workspace_id` + global creators)
- Events + queued jobs (invitations, order creation, AI content review, sync, webhooks)
- Scheduled nightly Shopify reconcile + inventory sync
- PWA manifest, service worker and generated app icons
- 4 Feature test files covering the core flows

## Project map

```
app/
  Domains/
    AI/            # gateway, fake + OpenAI providers, AnalyzeStore, GenerateCampaignSuggestion
    Analytics/     # AttributeOrder
    Billing/       # ReleasePayout
    Campaigns/     # CreateCampaign, LaunchCampaign, AcceptInvitation
    Commerce/      # ChannelRegistry, Contracts, Shopify/ + ManualChannel
    Content/       # SubmitContent, ApproveContent
    Contracts/     # GenerateContract
    Matching/      # MatchCreatorForCampaign, GenerateMatches
  Http/Controllers/{Auth,Brand,Creator,Shopify}
  Jobs/            # SendCampaignInvitations, CreateCreatorOrder, RunAiContentReview,
                   # HandleShopifyWebhook, SyncChannel
  Models/          # 40+ Eloquent models
  Support/         # TenantContext, DemoCreatorFactory
database/
  migrations/      # 8 grouped migrations (identity, commerce, creators, campaigns,
                   #   operations, messaging, billing, analytics/AI)
  seeders/         # DatabaseSeeder builds a full demo workspace
resources/views/   # 41 Blade views (layouts, brand/*, creator/*, auth/*, messages/*)
public/            # manifest.webmanifest, sw.js, icons, built assets
routes/web.php     # 61 routes across brand, creator, auth, Shopify, webhooks, messaging
tests/Feature/     # BulkSeed, Acceptance/Content flow, Shopify webhooks, AI gateway
docs/              # full product/architecture/GTM blueprint (10 docs)
```

## Testing

```bash
php artisan test
```

Tests use an in-memory SQLite DB and cover bulk-seed math, launch matching, the acceptance→order→payout lifecycle, discount-code attribution, Shopify webhook verification/product upsert, and the AI gateway.

## License

MIT.
