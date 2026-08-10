# 8 — Monetization & Pricing

Revenue model combines **SaaS subscriptions** (primary, predictable), **marketplace commission/fees** (secondary, aligns with GMV), and **optional paid services / add-ons** (expansion). Stripe Connect is the system of record for creator payouts; bill brands via Stripe (web) or Shopify Billing (Shopify channel).

## 8.1 Pricing Tiers

Prices are USD/month, with annual discount (2 months free). These are starting recommendations — validate with 20–40 prospects and adjust.

| | **Free** | **Starter** | **Pro** | **Growth** | **Enterprise** |
|---|---|---|---|---|---|
| Price | $0 | $49/mo | $199/mo | $499/mo | Custom |
| Best for | Solo / trying it out | New/small D2C | Growing D2C brands | Scaling brands & small agencies | Agencies/enterprise |
| Brands / workspaces | 1 | 1 | 1 | 5 | Unlimited |
| Team seats | 1 | 2 | 5 | 10 | Unlimited |
| Products | up to 25 | 250 | 2,500 | 25,000 | Unlimited |
| Active campaigns | 1 | 5 | 25 | 100 | Unlimited |
| Creator invites/mo | 50 | 500 | 5,000 | 25,000 | Custom |
| Channels | 1 | 1 | 3 (Shopify+Woo+CSV) | Unlimited | Unlimited |
| AI campaign generation | 1/mo | 10/mo | 100/mo | 500/mo | Custom |
| Bulk seeding | ○ | ○ | ● | ● | ● |
| Auto Shopify orders/discounts | ● | ● | ● | ● | ● |
| AI content review | ○ | basic | ● | ● | ● |
| AI fraud detection | ○ | ○ | ● | ● | ● |
| ROI prediction / multi-touch attribution | ○ | basic | ● | ● | ● |
| Contracts & e-sign | ○ | ● | ● | ● | ● |
| UGC asset library | ○ | up to 50 | up to 2,000 | Unlimited | Unlimited |
| Payouts to creators (Stripe Connect) | ○ | ● | ● | ● | ● |
| Affiliate/commission engine | ○ | ○ | ● | ● | ● |
| API access | ○ | ○ | ● | ● | ● |
| White-label / agency mode | ○ | ○ | ○ | ○ | ● |
| SSO / advanced security | ○ | ○ | ○ | ○ | ● |
| Dedicated CSM | ○ | ○ | ○ | priority | ● |
| SLA | ○ | ○ | ○ | ○ | ● |

### Tier logic
- **Free** drives adoption (especially Shopify App Store top-of-funnel) but caps volume enough to convert.
- **Starter** removes the "trying it" limits and unlocks contracts/UGC library.
- **Pro** is the flagship: bulk seeding, AI content review, fraud, ROI, multi-channel, affiliate — where most revenue lands.
- **Growth** targets multi-brand/multi-seat power users and small agencies.
- **Enterprise** for agencies/large brands: SSO, white-label, unlimited, CSM, custom contracts.

## 8.2 Marketplace Commissions / Transaction Revenue

Keep SaaS primary; use transaction fees selectively to avoid taxing SMB seeding too hard.

**Recommended model:**
- **Barter campaigns:** 0% platform commission (product is the value; this drives liquidity). Optional small "service fee" on expedited payouts to creators.
- **Paid campaign fees:** Platform fee of **8–10% of creator fee** capped per assignment (or included in higher tiers). Can be reduced/waived on Pro and above to drive subscription upgrades.
- **Affiliate campaigns:** Take rate of **1–2% of attributed sales** OR include in subscription at higher tiers. This aligns with GMV and is easy to defend.
- **Marketplace managed campaigns** (where CreatorFlow handles sourcing/payouts end-to-end): **15–20%** service fee.
- **Creator instant payouts:** small convenience fee (e.g., 1.5–2%) — a creator-paid optional perk, not a brand tax.
- **Whitelisting/ads access:** optional add-on; revenue share or flat fee.

**Avoid:** taxing barter product value — it creates friction in the core seeding use case and is hard to invoice.

## 8.3 Optional Paid Add-ons

- **AI Boost packs:** extra AI campaign generations/content reviews beyond plan allotment.
- **Boosted creator listings:** brands sponsor their campaign in the marketplace feed (promoted campaigns).
- **Creator Sourcing add-on:** proactive AI sourcing for hard-to-fill niches.
- **Premium creator pool / vetted creators:** access to top-rated/verified creators.
- **UGC repurposing / ad creative service:** AI cutdowns or human editing as a paid add-on.
- **Content production services:** done-for-you campaign management (marketplace of vetted managers/editors — take rate).
- **Analytics advanced:** custom dashboards, BI export, incrementality studies (Enterprise).
- **Dedicated IP/warm domains for outreach** to protect deliverability.

## 8.4 Creator Monetization (supply side)

Creators **always join free** — never charge creators for access (it kills liquidity). Monetize through:
- Instant/faster payout fees (optional).
- Premium creator subscription (optional, later): verified badge, boosted profile, media-kit AI, early access to campaigns, analytics. Keep this light and clearly optional.
- Tax/compliance handled via Stripe; no surprise deductions.

## 8.5 Shopify Billing vs Stripe

- **Shopify-installed brands:** bill via **Shopify Billing API** (recurring app charge) — merchants trust it, it's in their Shopify invoice, and conversion is higher. Apply feature flags based on the Shopify plan.
- **Web-platform brands:** bill via **Stripe Billing** (subscriptions + invoices).
- Abstract both behind a `BillingProvider` so plan/entitlements are unified. Marketplace commissions and add-ons billed via the same provider.

## 8.6 Entitlements System

Implement an **entitlements/usage** layer:
- Plan defines limits (products, campaigns, invites, AI credits).
- Track usage in Redis + a periodic `usage_rollups` table.
- Gate UI actions with a `PlanGate`/policy; show friendly upsells at the point of action.
- Overage handling: either block, or charge per-use (configurable by plan), with clear pre-approval.
- Trial: 14-day full-featured trial (Pro features), then downgrade to Free if no card. Shopify apps can use Shopify trial charges.

## 8.7 Unit Economics (illustrative, validate)

- COGS-ish: AI cost per campaign (~$0.50–$3 depending on model usage), S3/CDN/bandwidth, Stripe fees (~2.9%+$0.30), payouts processing (~0.5–1% with Connect), support.
- Target: gross margin >75% on SaaS; transaction revenue should be net-positive after fees.
- Keep AI costs bounded with cheaper models, caching, and caps; AI is a feature, not a blank check.
- LTV/CAC target >3:1; Shopify App Store lowers CAC significantly vs paid acquisition.

## 8.8 Pricing Validation Plan

1. Interview 20–40 brands across Shopify D2C, agencies, non-Shopify; show the tier table and ask willingness-to-pay.
2. Run a landing-page price test (Starter $49 vs $79) before launch.
3. Anchor Pro as the highlighted "most popular" tier.
4. Monitor activation-to-paid conversion and feature usage to set limits that convert (e.g., invites cap should hit right as a brand gets value).
