# CreatorPlex — Product Design & Validation

**CreatorPlex** is a unified creator-marketing platform for brands and creators. Two onboarding channels (Shopify App and a standalone Web Platform) feed **one** backend, one creator marketplace, one campaign engine, one messaging system, and one database.

This document set is the product, UX, technical, and GTM blueprint. It is written specifically for the chosen stack:

- **Backend:** Laravel 11 (PHP 8.3)
- **Database:** MySQL 8
- **Frontend:** Laravel Blade + Livewire/Alpine, **mobile-first PWA** for both Brand and Creator panels
- **Queue:** Laravel Horizon (Redis)
- **File storage:** S3-compatible (S3 / R2 / Spaces)
- **Realtime:** Laravel Reverb (WebSockets) or Pusher
- **Search:** Meilisearch / OpenSearch
- **Payments:** Stripe Connect (billing + marketplace payouts)
- **AI:** Pluggable LLM gateway (OpenAI/Anthropic) + embeddings + queues

## Document Index

| # | Document | What it covers |
|---|----------|----------------|
| 1 | [01-product-vision.md](./01-product-vision.md) | Vision, positioning, two entry points, unifying principles, assumptions challenged |
| 2 | [02-architecture.md](./02-architecture.md) | System architecture, multi-tenancy, Shopify integration, event-driven design, queues, storage, security |
| 3 | [03-database-schema.md](./03-database-schema.md) | Full MySQL schema: tenants, users, products, creators, campaigns, applications, orders, payouts, messages |
| 4 | [04-api-and-modules.md](./04-api-and-modules.md) | Domain modules, API design, webhooks, key Laravel structures |
| 5 | [05-user-journeys-ux.md](./05-user-journeys-ux.md) | User journeys, UX flows, screen-by-screen design for Brand + Creator panels (PWA) |
| 6 | [06-ai-features.md](./06-ai-features.md) | AI workflows, campaign generation, matching, fraud, ROI, differentiators |
| 7 | [07-competitive-analysis.md](./07-competitive-analysis.md) | InfluCollabs, Shopify Collabs, Aspire, Grin, Upfluence, Modash, CreatorIQ — gaps & opportunities |
| 8 | [08-monetization.md](./08-monetization.md) | Pricing tiers, marketplace commissions, add-ons, unit economics |
| 9 | [09-mvp-roadmap.md](./09-mvp-roadmap.md) | 8–12 week MVP: Must/Nice/Future, week-by-week plan |
| 10 | [10-gtm-strategy.md](./10-gtm-strategy.md) | ICP, positioning, launch, channels, Shopify app store strategy |
| 11 | [11-validation-risks.md](./11-validation-risks.md) | Validation plan, risk register, metrics, open decisions |

## The One-Page Thesis

CreatorPlex is **the operating system for creator commerce**, not another influencer-search tool.

1. **One product, two doors.** Shopify is a distribution channel and an integration, not a separate codebase. A `channels` abstraction means WooCommerce, Amazon, and a generic CSV/API importer plug into the same product/campaign/order model.
2. **Products are the atomic unit.** Every campaign is anchored to real products with inventory, pricing, images, and fulfillment. This turns "influencer marketing" into **measurable commerce**.
3. **AI removes the blank page.** From the moment a store connects, AI produces a ready-to-launch campaign: niche, hero products, brief, creator count, budget, and predicted ROI — one click to launch.
4. **Bulk seeding is a first-class primitive.** "Product A → 100 creators, Product B → 50" is a native workflow, not a spreadsheet hack, with waitlists, auto-orders, and delivery tracking.
5. **Both sides are mobile-first PWA.** Creators live on their phones; brands manage on the go. No separate native app tax.

## The Single Most Important Architectural Decision

> **Treat Shopify as a channel adapter behind a unified `Commerce` domain, not as the product itself.**

Every competitor that started as a Shopify app trapped their data model in Shopify's shape and struggled to serve non-Shopify brands. CreatorPlex inverts that: the core domain is **channel-agnostic**, and Shopify is the richest adapter (with webhooks, discount codes, orders, and Draft Orders). This makes "same backend" non-negotiable and testable.
