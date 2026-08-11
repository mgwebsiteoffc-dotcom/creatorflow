# CreatorPlex — Executive Summary (Quick Reference)

**One product, two doors, one backend.** CreatorPlex is an AI-powered creator-commerce platform. Shopify and the web are *onboarding channels* into the same products, campaigns, creator marketplace, messaging, orders, payouts, analytics, and AI.

**Stack:** Laravel 11 · MySQL 8 · Redis/Horizon · Blade + Livewire/Alpine · mobile-first PWA · Reverb · S3/R2 · Meilisearch · Stripe Connect · pluggable AI gateway.

---

## The 10 Deliverables at a Glance

| # | Deliverable | Answer in one line | Doc |
|---|---|---|---|
| 1 | Product architecture | Channel adapters feed a channel-agnostic core; Shopify ≠ separate product | [01](./01-product-vision.md), [02](./02-architecture.md) |
| 2 | User journeys | Shopify: install→AI campaign→launch in <10 min; Web: add products→same flow; Creator: apply→ship→content→paid | [05](./05-user-journeys-ux.md) |
| 3 | System architecture | Laravel monolith, logical multi-tenancy (`workspace_id`), global creators, event/Horizon jobs, Reverb, S3 | [02](./02-architecture.md) |
| 4 | Database design | Full MySQL schema: workspaces, channels, products, creators, campaigns/assignments, orders, payouts, analytics, AI | [03](./03-database-schema.md) |
| 5 | API recommendations | REST `/api/v1` (Sanctum), channel contract, Shopify webhooks, outbound webhooks, idempotency | [04](./04-api-and-modules.md) |
| 6 | Feature prioritization | MVP must/nice/future + 12-week plan | [09](./09-mvp-roadmap.md) |
| 7 | UX flows | Mobile-first PWA Brand + Creator panels, bulk-seed UX, content inbox, AI campaign wizard | [05](./05-user-journeys-ux.md) |
| 8 | AI opportunities | One-click campaign gen, matching, ROI, content review, fraud, messaging, autopilot, UGC repurposing | [06](./06-ai-features.md) |
| 9 | Competitive positioning | Commerce-native, AI-native, unified, mobile-first vs InfluCollabs/Collabs/Aspire/Grin/Upfluence/Modash/CreatorIQ | [07](./07-competitive-analysis.md) |
| 10 | Go-to-market | Shopify App Store + web PLG, pre-seed creators in 2–3 niches, content/SEO, founder-led | [10](./10-gtm-strategy.md) |

Also: [Monetization](./08-monetization.md) and [Validation & Risks](./11-validation-risks.md).

---

## The 7 Decisions That Matter Most

1. **Treat Shopify as a channel adapter behind a `CommerceChannel` contract** — never fork features for Shopify vs web. This is the architectural soul of the product.
2. **Products are the atomic unit.** Every campaign, assignment, order, and payout ties back to a product. This makes ROI measurable.
3. **Bulk seeding is a first-class primitive:** `Product → N creators`, with invite pools modeled off acceptance rates, waitlists, and auto-order creation.
4. **Auto-create Shopify orders/discounts on acceptance** (draft/$0/tagged orders) so inventory and fulfillment stay in Shopify — a real differentiator.
5. **Global creator marketplace** (creators are not per-tenant); workspaces are tenants. This enables liquidity and inbound discovery.
6. **AI starts as suggestions humans approve**, then becomes "autopilot" (invites, waitlists, nudges, routing) with guardrails and an eval set.
7. **Mobile-first PWA for both panels; no native apps at MVP.** Saves months; covers install, push, camera, offline.

## MVP in One Paragraph

In 8–12 weeks, ship: Shopify install + product/inventory sync; web signup with manual + CSV products; AI store analysis + one-click AI campaign (brief, hero products, creator count, ROI); creator profiles/marketplace/apply; bulk-seed campaign with invites, accept/decline, waitlist; unique Shopify discount codes + auto-orders; content upload + basic AI review/approval; messaging; Stripe Connect payouts; discount-code + referral-link attribution; campaign analytics dashboard. Free/Starter/Pro plans with entitlements. Pre-seed 300–500 vetted creators in 2–3 niches before beta.

## Recommended Pricing (starting point)

Free ($0) · Starter ($49) · Pro ($199 — flagship, bulk seeding + AI content review + fraud + ROI) · Growth ($499 — multi-seat/multi-brand) · Enterprise (custom). SaaS subscription primary; 8–10% on paid campaign fees or 1–2% affiliate GMV, waived/included on higher tiers; 0% on barter; optional AI add-ons and instant creator payouts. **Never charge creators for access.**

## Biggest Risks

1. Marketplace liquidity (no creators at launch) → pre-seed + niche focus.
2. AI quality → evals + human-in-the-loop + feature flags.
3. Shopify review/compliance → start early, GDPR webhooks.
4. Payouts/tax compliance → Stripe Connect Express, US-first, legal review.
5. Scope creep → ruthless MVP split.

## Recommended Immediate Next Steps

1. Confirm 2–3 launch niches; begin recruiting creators now.
2. Stand up Laravel repo + the `CommerceChannel` contract first.
3. Run 20–40 discovery interviews + a waitlist/price test in parallel.
4. Prototype the AI "campaign in a click" moment on sample data.
5. Submit a minimal Shopify app build for review early.
