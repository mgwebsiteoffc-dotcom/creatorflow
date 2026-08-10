# 11 — Validation Plan, Risks & Open Decisions

## 11.1 Validation Plan (before/during build)

Don't build on assumptions — validate riskiest hypotheses first.

### Problem validation (week 0–2, can start before coding)
- 20–40 discovery interviews: Shopify D2C founders/marketers, non-Shopify brands, creators, and 3–5 agencies.
- Test: current creator workflow, pain points (finding creators, fulfillment, content, attribution), budget, willingness to pay.
- Test the "one-click AI campaign" concept with a non-functional demo/prototype — gauge excitement vs indifference.
- Recruit beta brands and creators from these interviews.

### Demand validation
- Landing page with waitlist + price test; run small paid spend to measure intent and channel CAC.
- Shopify App Store keyword research; assess competition and search volume.
- Post a "build in public" and measure inbound interest.

### Liquidity validation (critical)
- Confirm you can recruit 300–500 vetted creators in 2–3 niches before launch.
- Measure creator response/acceptance on a few manually run pilot campaigns.

### UX validation
- Prototype the Shopify onboarding (Figma) and test "time to first campaign" with 5 merchants.
- Test creator upload flow on real mobile devices/poor networks.

### AI quality validation
- Build evaluation sets for: niche detection, hero products, brief quality, match ranking, content scoring.
- Compare AI suggestions to human expert judgment; tune before trusting in production.
- Start AI as "suggestions humans approve," not autopilot.

### Pricing validation
- Van Westendorp/price sensitivity questions + live price testing.
- Measure conversion at Free→Starter→Pro.

## 11.2 Risk Register

| Risk | Impact | Likelihood | Mitigation |
|---|---|---|---|
| Two-sided marketplace has no liquidity at launch | Critical | High | Pre-seed creators, niche focus, white-glove beta, inbound profiles |
| Shopify app review/rejections delay launch | High | Medium | Start review early; follow requirements; GDPR webhooks; test on dev stores |
| AI quality not good enough (bad matches/briefs) | High | Medium | Evals, human-in-the-loop, fallback rules, feature flags, tune with real data |
| Shopify changes API/pricing or competes harder | High | Medium | Channel abstraction; build for non-Shopify too; data/AI moat |
| Fraudulent creators / fake engagement | High | Medium | Fraud scoring, manual vetting early, gradual automation, performance history |
| Creator payouts/compliance complexity (tax, international) | High | Medium | Stripe Connect Express; start US + supported countries; legal review; clear terms |
| Content/rights/legal disputes | Medium | Medium | Clear contracts, usage rights, audit trail, approvals, content moderation |
| High AI costs erode margins | Medium | Medium | Model selection, caching, caps/entitlements, async batching, cost monitoring |
| Low conversion from free to paid | High | Medium | Usage limits set at value moment; activation focus; ROI proof points |
| Bulk operations (500 creators) cause rate limits/timeouts | Medium | Medium | Job batching, throttling, idempotency, backoff, separate queues |
| Webhook reliability (missed events) | Medium | Medium | Nightly full reconciles, idempotency, event log, retries |
| Video upload failures on mobile networks | Medium | High | Chunked/resumable uploads, compression guidance, retry, PWA background upload |
| Data/privacy incidents | High | Low | Encryption, least privilege, backups, GDPR flow, security review, audit logs |
| Scope creep kills MVP | High | High | Ruthless Must/Nice/Future split; PM discipline; ship in 12 weeks |
| Non-Shopify product onboarding is too manual (CSV friction) | Medium | Medium | Great column-mapping UI, templates, URL import, Woo soon after |
| Attribution over-claims ROI, eroding trust | High | Medium | Multi-touch + conservative defaults; show ranges; explain models |
| PWA limitations (iOS push, background upload) | Medium | Medium | Feature detection, fallbacks, clear UX; revisit native only if needed |
| Delivering AI "autopilot" safely | High | Medium | Guardrails, approvals, audit, kill switches; start assistive, then automate |

## 11.3 Open Decisions (to resolve with stakeholders)

1. **Launch niches:** which 2–3 verticals first? (Recommend beauty/skincare + food/beverage + fitness/pet based on demonstrability and seeding economics — validate.)
2. **Billing at MVP:** full Stripe/Shopify billing at launch vs manual provisioning during beta? (Recommend billing by week 11, manual during closed beta.)
3. **Creator payouts at MVP:** full Stripe Connect auto-payouts vs track-and-manual? (Recommend Connect onboarding immediately; automated release once approved.)
4. **Affiliate at MVP:** include commission-based affiliate campaigns or defer? (Recommend basic discount-code attribution + fee payouts; full affiliate splits post-MVP.)
5. **WooCommerce at MVP:** in or out? (Recommend out — Shopify + CSV/manual proves unified core; ship Woo within 6 weeks post-MVP.)
6. **Geographies:** US-only creators/brands at launch vs international? (Recommend US + a few Stripe-supported countries for payouts; Shopify stores globally.)
7. **AI provider strategy:** single provider vs pluggable from day one? (Recommend pluggable gateway but ship one provider to start.)
8. **White-label/agency features:** schema supports it now, but build UI later — confirm priority.
9. **Content licensing defaults:** standard license terms included; paid whitelisting as add-on — legal review needed.
10. **Support model:** how much white-glove support in beta/free tier, and when to transition to self-serve?

## 11.4 Key Metrics to Instrument from Day One

Instrument everything; the data flywheel depends on it.

- **Activation:** install → product sync → AI suggestion → campaign launch → first invite.
- **Funnel:** invites sent → opened → accepted → contract → order → shipped → content submitted → approved → post live → attributed order.
- **Per-creator:** acceptance rate, on-time content rate, content quality score, attributed revenue, completion rate, fraud risk.
- **Per-campaign:** CPE, cost per content asset, CAC-equivalent, attributed GMV, ROI, content output.
- **AI:** suggestion adoption, edit rate, time-to-launch, AI vs human match overlap, content review precision, cost/run.
- **Marketplace liquidity:** creator-to-active-campaign ratio, creator fill rate, waitlist promotion rate, creator retention.
- **Revenue:** MRR, ARR, plan distribution, NRR, CAC, payback, take-rate revenue.

## 11.5 Suggested Next Actions

1. Lock the 2–3 launch niches and begin creator recruitment immediately.
2. Stand up the Laravel repo + CI + environments and the `CommerceChannel` contract first; it de-risks the "unified core" promise.
3. Run 20 discovery interviews and a waitlist/price test in parallel with weeks 1–2 of engineering.
4. Build the AI campaign-generation prototype early (even against sample data) to test the "wow" moment.
5. Get the Shopify app into review with a minimal submission early; iterate while building.
