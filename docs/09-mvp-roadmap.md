# 9 — MVP Definition & 8–12 Week Roadmap

The MVP must prove the core thesis: **a Shopify merchant installs, AI suggests a campaign, they launch it, creators accept, orders ship, content comes back, and sales are attributed.** A non-Shopify path (CSV/manual) is included to validate the "unified backend" promise, but can be thinner.

## 9.1 Must Have (MVP)

### Foundation
- Laravel 11 + MySQL 8 + Redis/Horizon + S3/R2 + Blade/Livewire/Tailwind PWA shell.
- Auth (email/password + Google), workspaces, team members (owner/manager roles).
- Entitlements/plan scaffolding (Free/Starter/Pro flags; billing can be deferred to post-MVP with manual provisioning).
- Brand panel + Creator panel PWA (mobile-first), with push notifications and installability.
- Basic analytics/event logging, error tracking, CI.

### Entry Point 1 — Shopify
- Shopify OAuth install + embedded app (App Bridge).
- Webhook registration + HMAC verification.
- Sync products, variants, images, collections, inventory (incremental + nightly reconcile).
- Shopify Billing stub (can use a test charge or manual activation for MVP).
- GDPR webhook handlers.

### Entry Point 2 — Web
- Account + workspace creation.
- Add website, create product manually, **CSV import** with column mapping.
- AI analysis on imported products (same pipeline as Shopify).
- Clear messaging that auto-orders require a connected store.

### Shared product model
- Products/variants/images/collections unified behind the `CommerceChannel` contract.
- Product list/search/filter; hero score + AI niche tags.

### AI (the "wow")
- Store/product analysis (niche, hero products, tags).
- AI campaign generation (title, brief, target creator count, product selection, predicted ROI range).
- Editable brief (rich text).
- Creator matching with a 0–100 score (keyword/facet + lightweight semantic + performance), with reasons.
- AI outreach message generation (single variant for MVP; A/B later).

### Campaigns & Bulk Seeding
- Create campaign (barter + paid; affiliate can be basic/discount-code based).
- Bulk seed: per-product target counts, invite pool computed from assumed acceptance rate.
- Invitations (in-app + email), applications, accept/decline.
- Waitlist with manual/simple promotion.
- Campaign detail with funnel counters.

### Creator marketplace & portal
- Creator sign-up, profile, niches, social handles (manual stats entry for MVP; one-platform auto-sync if feasible), rates, barter prefs.
- Marketplace discovery + filters + apply.
- Assignment timeline; shipping address capture.
- Content upload (images/video) with direct-to-S3.
- Messaging (1:1 threads, realtime via Reverb).
- Earnings/payouts records (Stripe Connect onboarding + payout release after approval; can start with payout tracking + manual fulfillment if Connect verification takes too long).

### Operations
- Discount code creation (Shopify unique codes per creator).
- Auto-order creation on acceptance for Shopify (Draft Order → $0/paid/tagged order) with fallback to manual.
- Content approval (approve / request changes); AI content review as a basic score/flags.
- Basic order/fulfillment tracking from Shopify webhooks.

### Attribution & Analytics
- Unique discount-code attribution (primary).
- Referral link with UTM capture + click tracking.
- Campaign dashboard: invited/accepted/shipped/content/approved/orders/revenue.
- Daily rollups.

## 9.2 Nice to Have (post-MVP, within ~3–6 months)

- WooCommerce adapter.
- Affiliate campaigns with commission payouts and Stripe Connect splits.
- A/B outreach variants + send-time optimization.
- AI content review auto-approval thresholds and transcription-based checks.
- Fraud detection dashboard.
- Contracts/e-sign (can use a third-party like Dropbox Sign quickly).
- Public creator profiles + inbound invites.
- Creator portfolio media kit export.
- Multi-touch attribution with post-purchase survey.
- CSV import of creators for agencies.
- Saved creator lists/segments.
- Email digests and reports.
- Usage rights/license templates.
- AI "campaign autopilot" (limited beta).

## 9.3 Future Features (6–12+ months)

- Amazon channel (read + review seeding workflows).
- Agency white-label, SSO, audit/compliance exports.
- UGC repurposing engine + ad-account integration (Meta/TikTok whitelisting).
- External creator sourcing / always-on recruitment.
- Incrementality/lift studies.
- Creator subscription tier, boosted marketplace listings.
- Native apps only if a hard requirement emerges (avoid until then).
- Multi-region data residency.
- Advanced BI / data warehouse export.
- Creator collaborations and team accounts for creator collectives.

## 9.4 What I Would Deliberately Cut or Defer

- **Native iOS/Android** — PWA is sufficient; saves months.
- **Full affiliate/payments split complexity** — discount-code attribution + fee payouts first.
- **All channels at launch** — Shopify + CSV/manual first; Woo soon after.
- **Highly configurable AI** — ship opinionated defaults; add controls later.
- **Complex permissions** — 2–3 brand roles at MVP.
- **Internationalization/payouts in many countries** — start US + a few supported Stripe countries; expand deliberately.
- **Custom landing-page builder** — simple invite landing pages; no page builder.
- **Built-in video editor** — capture/upload only; editing stays on creators' devices.

## 9.5 8–12 Week Plan (week-by-week)

Assume a small, senior team: 1 PM/founder, 1–2 Laravel engineers, 1 full-stack/Blade/Livewire engineer, 1 part-time designer, fractional DevOps. Adjust if team is larger/smaller.

### Weeks 1–2 — Foundation & Auth
- Repo, CI, environments, staging with Shopify dev store.
- Laravel + MySQL + Redis + Horizon + S3 + Reverb + PWA shell.
- Workspaces, users, roles, Brand/Creator route groups & layouts.
- Design system (Tailwind components, mobile-first), PWA manifest/service worker.

### Weeks 3–4 — Products & Channels
- `CommerceChannel` contract; Shopify OAuth + webhooks.
- Product/variant/image/collection sync (incremental + full reconcile jobs).
- Web: manual product create + CSV import with mapping.
- Product UI (list, detail, filters, hero score display).

### Weeks 5–6 — AI Core & Campaigns
- AI gateway; `AnalyzeStore`, hero scoring, niche detection.
- AI campaign generation + brief editing UI.
- Campaign model, wizard, bulk-seed product/target UI.
- Basic creator matching/scoring with reasons.

### Weeks 7–8 — Creators, Invitations & Marketplace
- Creator sign-up/profile/social/rates/prefs; portfolio basics.
- Marketplace browse/search/apply; campaign landing/invite flow.
- Invitations (in-app + email), accept/decline, waitlist.
- Discount codes (Shopify) + auto-order creation on acceptance.
- 1:1 messaging (Reverb realtime).

### Weeks 9–10 — Content, Orders & Payouts
- Content upload (direct-to-S3, chunked video), submission flow.
- Content inbox, approve/request changes; basic AI content score.
- Shopify order webhooks → fulfillment/tracking in assignment timeline.
- Stripe Connect onboarding for creators; payout record + release on approval (can be manual to start).

### Weeks 11 — Attribution & Analytics
- Discount-code + referral-link attribution.
- Campaign dashboard funnel + revenue/ROI display.
- Daily rollups; notifications/push for key events.
- Billing/entitlements wiring (Stripe/Shopify), plan gating + usage caps.

### Week 12 — Hardening, Beta & Launch Prep
- End-to-end testing across both entry points.
- Performance, queuing/batching, idempotency, error handling.
- Accessibility, mobile device testing, offline shell.
- GDPR/data handling, security review, backup/restore test.
- Beta with 5–10 brands + recruited creators; feedback loop.
- Marketing site, pricing, Shopify App Store submission assets.

## 9.6 MVP Success Metrics

- ≥60% of Shopify installers reach first AI campaign suggestion.
- ≥30% launch a campaign within 7 days; target <10 min time-to-launch.
- Invite acceptance rate ≥20% in seeded niches.
- ≥50% of accepted assignments produce submitted content.
- At least one instance of attributed revenue per active campaign.
- Creator week-2 retention ≥40%.
- App store rating target ≥4.5 after first 50 installs.

## 9.7 Beta Strategy

- Recruit 10–20 Shopify D2C brands from communities, cold outreach, and founder network.
- Pre-onboard a curated creator pool (a few hundred) in 2–3 niches to guarantee liquidity for beta brands (do NOT launch an empty marketplace).
- White-glove onboarding to learn the workflow; record every point of friction.
- Manually run AI suggestions behind the scenes if quality needs tuning before automating.
