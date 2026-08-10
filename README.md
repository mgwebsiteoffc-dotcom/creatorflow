# CreatorFlow

CreatorFlow is a unified, AI-powered creator-commerce platform for brands and creators. Two entry points — a **Shopify App** and a **standalone Web Platform** — share one backend, one creator marketplace, one campaign engine, one messaging system, and one database.

This repository currently contains the product design and validation blueprint. Implementation is Laravel 11 + MySQL 8 + Blade/Livewire, with mobile-first PWA panels for both brands and creators.

## Documentation

Start here → **[docs/EXECUTIVE-SUMMARY.md](docs/EXECUTIVE-SUMMARY.md)**

Full document set:

1. [Product Vision & Positioning](docs/01-product-vision.md)
2. [System Architecture](docs/02-architecture.md)
3. [Database Design (MySQL)](docs/03-database-schema.md)
4. [Domain Modules & API Design](docs/04-api-and-modules.md)
5. [User Journeys & UX Flows](docs/05-user-journeys-ux.md)
6. [AI Features & Differentiators](docs/06-ai-features.md)
7. [Competitive Analysis](docs/07-competitive-analysis.md)
8. [Monetization & Pricing](docs/08-monetization.md)
9. [MVP & 8–12 Week Roadmap](docs/09-mvp-roadmap.md)
10. [Go-to-Market Strategy](docs/10-gtm-strategy.md)
11. [Validation, Risks & Open Decisions](docs/11-validation-risks.md)

## Stack

- **Backend:** Laravel 11 (PHP 8.3)
- **Database:** MySQL 8
- **Frontend:** Blade + Livewire/Alpine, Tailwind, mobile-first PWA
- **Queue/Realtime:** Redis + Horizon, Reverb (WebSockets)
- **Storage:** S3 / Cloudflare R2
- **Search:** Meilisearch + Laravel Scout
- **Payments:** Stripe Connect (+ Shopify Billing for Shopify-installed brands)
- **AI:** Pluggable LLM gateway (OpenAI/Anthropic), embeddings, async queue

## Core Thesis

One product, two doors. Shopify is a **distribution channel and an integration**, not a separate codebase. Products are the atomic unit, AI removes the blank page, bulk seeding is a first-class primitive, and both brand and creator experiences are mobile-first PWA.
