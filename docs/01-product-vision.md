# 1 — Product Vision & Positioning

## 1.1 Elevator Pitch

CreatorFlow is the AI-powered creator-commerce platform where any brand — Shopify store or not — can turn products into fully managed creator campaigns in one click, and where creators discover products, get paid, and build their portfolio — all from a mobile-first PWA.

## 1.2 Why Now

- Shopify Collabs proved demand but is shallow: weak discovery, no true bulk seeding, limited AI, and Shopify-only.
- Aspire, Grin, CreatorIQ are expensive, complex, agency-oriented, and slow to launch.
- InfluCollabs and Modash are discovery-first but lightweight on commerce/fulfillment.
- AI (LLMs + embeddings) has only just made "one-click campaign generation" genuinely good.
- Creators increasingly want product-first, mobile experiences, not clunky portals.

The gap: **a commerce-native, AI-native, mobile-first platform that is affordable for SMBs and powerful for agencies.**

## 1.3 Two Entry Points, One Product

```
                         ┌─────────────────────────────┐
   Shopify App Store ──▶ │  Shopify OAuth + Webhooks   │
                         └──────────────┬──────────────┘
                                        │
                                        ▼
                         ┌─────────────────────────────┐
                         │   Channel Ingestion Layer   │  (Shopify, Woo, Amazon,
                         │   products / inventory /    │   CSV, manual, API)
                         │   orders / discounts        │
                         └──────────────┬──────────────┘
                                        │
   Web Signup ────────▶ ┌───────────────┴──────────────┐
   (any business)       │                              │
                         ▼                              ▼
                  ┌─────────────────────────────────────────┐
                  │   UNIFIED CORE (same backend, same DB)  │
                  │  Products · Campaigns · Creator Market  │
                  │  Matching · Messaging · Orders · Payouts│
                  │  Analytics · Billing · Teams · AI       │
                  └─────────────────────────────────────────┘
                                        │
              ┌─────────────────────────┴─────────────────────────┐
              ▼                                                   ▼
      Brand Panel (PWA)                                   Creator Panel (PWA)
      mobile-first Blade/Livewire                       mobile-first Blade/Livewire
```

### Channel = onboarding + data sync, never a feature fork

A brand's `channel` determines only:
- How products/inventory/orders are synced
- Whether discount codes and orders can be auto-created
- Which billing integration (Shopify Billing API vs Stripe)

**Everything else — campaigns, creators, messaging, analytics, AI — is identical.** This is enforced in code: features live in core domain services, never in `if ($shopify) { ... }` branches at the feature level.

## 1.4 Target Users

### Brands / Merchants
| Segment | Needs | Entry point |
|---|---|---|
| Shopify D2C stores | Fast launch, auto-sync, sales attribution | Shopify App |
| WooCommerce stores | Same, via adapter | Web + Woo connect |
| Amazon sellers | Seeding for reviews/UGC, off-Amazon traffic | Web |
| D2C brands (custom stack) | Product API/CSV | Web |
| Agencies | Multi-client, teams, white-label | Web (Enterprise) |
| Manufacturers | Bulk seeding to many creators | Web |
| Local businesses | Hyper-local creator matching | Web |

### Creators
- **UGC creators** — content-first, often faceless/anonymous to end consumers, want product + usage fee
- **Influencers** — audience-first, paid posts
- **Micro (10k–100k)** and **Nano (<10k)** — high engagement, affordable, the volume sweet tooth for seeding

## 1.5 Unifying Product Principles

1. **No blank pages.** When a brand logs in, there is a recommended campaign ready.
2. **Products over posts.** Everything traces back to a product, an order, and revenue.
3. **Friction asymmetry.** Make the brand's life 10x easier even if it makes our internals harder.
4. **Creator respect.** Fast payouts, clear briefs, no ghosting — supply quality is the moat.
5. **Mobile first for creators; mobile-capable for brands.** Creators do 90% on phone.
6. **Trust via transparency.** Clear contracts, approvals, usage rights, and attribution.
7. **One codebase.** No "Shopify version" vs "web version" divergence.

## 1.6 Assumptions Challenged & Recommended Improvements

> The brief is strong; here are places I'd push back or upgrade.

**A) "Sync orders where applicable" should be bolder.**
Make **automatic Draft Order / discount-code creation** the hero of bulk seeding. The moment a creator accepts, CreatorFlow creates a Shopify draft order (or a $0 tagged order) applying a unique code, so inventory, fulfillment, tracking, and returns stay in Shopify. This is the single biggest differentiator vs Shopify Collabs.

**B) "AI generates a brief" is table stakes; the differentiator is AI operating the loop.**
- AI writes the **first outreach message** and A/B tests variants.
- AI **scores incoming content** against the brief before a human sees it.
- AI **detects fraud** (fake followers, engagement pods, screenshot UGC).
- AI **predicts ROI per creator** before invite.
- AI **chases late deliverables** with personalized nudges.

**C) Bulk seeding needs a "capacity & acceptance" model, not just counts.**
"100 creators" doesn't mean 100 invites. If historical acceptance is ~30%, you invite ~330 (capped by waitlist). The platform should model `target_accepted`, `invite_pool`, `acceptance_rate`, and `waitlist_size` explicitly.

**D) Don't build native iOS/Android yet.**
PWA covers install, push, camera, offline caching, and home-screen presence for creators at ~10% of the cost. Revisit native only if a specific feature (advanced video editing) demands it.

**E) Payouts and tax compliance are a moat, not a chore.**
Use **Stripe Connect Express** for creators (1099/K-1 handling in US, international support). Fast/early payouts are a creator-retention weapon. Offer "instant payout after content approval."

**F) Marketplace commission alone won't fund the business early.**
SMB seeding campaigns have low take-rate potential. Lead with **SaaS subscription**, treat marketplace commission as secondary/optional, and monetize paid-campaign flow + add-ons (AI briefs, boosted marketplace listings, whitelisting/ads access).

**G) Agencies need multi-tenant from day one.**
Even if not in MVP, schema should support an `agency_id` that owns many `brand_workspaces`. Avoid a painful migration later.

**H) Creators need inbound, not just outbound.**
Build a **public creator profile + "Open to work"** status so brands can discover and invite. Many platforms make creators apply only; two-sided discovery increases liquidity.

**I) Content rights & whitelisting should be native.**
Every contract should include a clear **usage license** (duration, channels, whitelisting/ads rights, exclusivity). This unlocks high-value paid campaigns and is a known pain point.

**J) Attribution should be multi-touch, not just last-click discount.**
Combine unique discount codes + referral links + post-purchase surveys ("How did you hear about us?") + UTM. Give credit to creators even when no code was used at checkout.

## 1.7 Success Metrics (North Star)

- **North Star:** *Attributed GMV driven through CreatorFlow per month* (ties product to real commerce).
- Activation: % of new brands that launch a campaign within 7 days.
- Liquidity: creator-to-active-campaign ratio; invite acceptance rate.
- Creator retention: % of creators who complete a second campaign.
- Time-to-first-campaign (target: < 10 minutes for Shopify merchants).
- AI trust: % of AI-suggested campaigns launched with minimal/no edits.
