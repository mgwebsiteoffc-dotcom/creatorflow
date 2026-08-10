# 5 — User Journeys & UX Flows (Mobile-First PWA)

Both Brand and Creator panels are **mobile-first PWA** built in Blade + Livewire + Tailwind. The design system is mobile-first; desktop is a progressive enhancement (multi-column, tables, side nav).

## 5.1 Design Principles

- **Thumb-zone primary actions.** Key CTAs (Launch, Message, Submit) in the bottom bar on mobile.
- **Cards over tables** on mobile; tables on ≥lg screens.
- **Progressive disclosure.** One decision per screen during onboarding/campaign creation.
- **Skeleton loading** + optimistic UI for messages/approvals.
- **Offline shell:** read-only dashboards and draft content work offline; queued actions sync when reconnected.
- **Install prompt** after first meaningful action (not on first load).
- **Push opt-in** contextually (e.g., when a creator is invited).

## 5.2 Journey A — Shopify Merchant (fastest path, <10 min to first campaign)

```
Shopify App Store ──▶ Install ──▶ OAuth grant ──▶ App loads embedded
   │
   ├─(background) Products/collections/inventory sync
   ├─(background) AI AnalyzeStore: niche, hero products, audience
   │
   ▼
Welcome screen: "We analyzed {store}. Here's your first campaign."
   │  [ AI campaign card: title, niche, hero products, creator count,
   │    budget estimate, predicted ROI, estimated reach ]
   │
   ├──▶ [Review & edit]  ──▶ tweak brief/budget/products/targets
   ├──▶ [Regenerate] (adjust goal: more UGC / more sales / more awareness)
   │
   ▼
[Launch campaign]  ──▶ choose barter/paid/affiliate
   │  AI generates matches ranked by score
   ▼
Review top creators (swipeable cards)  ──▶ [Invite all top N] / hand-pick
   │
   ▼
Bulk invitations sent (with AI-personalized messages, A/B variants)
   │
   ▼
Live campaign dashboard: acceptances, waitlist, shipped, content, ROI
```

**Key screens (Brand PWA):**
1. **Home / Today** — active campaigns snapshot, AI suggestions, pending approvals, unread messages.
2. **Campaigns** — list + filters; campaign detail with funnel (Invited → Accepted → Shipped → Content → Approved → Sales).
3. **Campaign wizard** — AI-suggested starting point; editable steps:
   - Goal (UGC / sales / awareness / reviews / product launch)
   - Products (pre-selected hero products; toggle; set targets per product via steppers)
   - Creators (niche, size, location, barter vs paid, budget)
   - Brief (AI draft with rich editor; content requirements, do's/don'ts, usage rights)
   - Review & launch (predicted ROI + cost breakdown)
4. **Products** — synced catalog, hero scores, search/filter, bulk-add to campaign.
5. **Creators / Marketplace** — search, filters, AI match %, profile drawer, invite.
6. **Assignments** — per-creator status; order tracking; content carousel; approvals.
7. **Content Inbox** — grid of submissions with AI score, one-tap approve/request changes.
8. **Orders** — Shopify orders tied to creators; tracking; returns.
9. **Analytics** — funnel, attributed revenue, creator leaderboard, ROI, content performance.
10. **Messages** — thread list + chat; AI reply suggestions.
11. **Billing** — plan, invoices, marketplace spend, payout ledger.
12. **Team** — invite members, roles.
13. **Settings** — channels (Shopify/Woo/CSV), notifications, webhooks, API keys.

### Bulk Seeding UX (hero workflow)

A dedicated **"Bulk Seed"** mode in the campaign wizard:

- Table/card list of products with a per-product **target creators** stepper.
- Live summary: `225 creators targeted · ~675 invites (30% acceptance) · $0 fees (barter) · est. 190 UGC assets · est. 4,200 reach`.
- Optional: set product-level fee/commission (hybrid campaigns).
- Waitlist toggle + auto-promotion on by default.
- On launch, platform creates discount codes and, on each acceptance, auto-creates the Shopify order.
- Progress screen: live counters for invited/accepted/shipped/content/approved + per-product breakdown.

## 5.3 Journey B — Non-Shopify Brand (Web Platform)

```
Sign up ──▶ Workspace setup (name, website, country)
   │
   ▼
"Add your products" — choose:
   ├── Connect Shopify (lands them in the same flow as Journey A)
   ├── Connect WooCommerce
   ├── Upload CSV (template + column mapping UI)
   ├── Add manually (quick form)
   └── Import via API (for later)
   │
   ▼
AI analyzes uploaded products (niche, hero score, suitability)
   │
   ▼
Same AI campaign card → same campaign wizard → same everything
```

**Critical:** once products exist, the experience is **byte-for-byte identical** to Shopify users. The only differences are (a) product sync is manual/CSV/Woo and (b) auto-order creation is unavailable for `manual`/`csv` channels (brand records fulfillment manually or via the order API). The UI makes this graceful: "Auto-orders require Shopify/Woo — connect a store to enable."

## 5.4 Journey C — Creator Onboarding & Daily Use

```
Sign up (email/Google/Apple) ──▶ Build profile
   │  - photo, bio, niches (multi-select), location, languages
   │  - connect socials (Instagram/TikTok/YouTube) via OAuth or handle
   │    -> stats pulled in (followers, engagement)
   │  - rates (UGC, post, video, story) + barter preferences
   │  - payout setup (Stripe Connect Express)
   │
   ▼
"Open to work" ON ──▶ Marketplace feed (campaigns matching niche/audience)
   │
   ├──▶ Apply to a campaign (cover note + proposed fee)
   │
   └──▶ Receive invite (push/email) ──▶ View brief + product ──▶ Accept/Decline
          │
          ▼ (accepted)
       Contract (tap to sign) ──▶ Shipping confirmation
          │
          ▼
       Order created/shipped ──▶ tracking in-app
          │
          ▼ (delivered)
       "Create content" ──▶ capture/upload (camera or files)
          │  AI pre-checks (length, brand mentions, quality) before submit
          ▼
       Submitted ──▶ brand approves / requests changes
          │
          ▼ (approved)
       Post content + paste live URL ──▶ payout scheduled/released
          │
          ▼
       Performance score updates + review/ratings
```

**Creator PWA screens:**
1. **Home** — open invitations, active assignments, next action ("Your order was delivered — submit content by Aug 20").
2. **Marketplace** — campaign cards (product, fee/barter, requirements, match score); filter/save search.
3. **Assignments** — tabs: Invitations, Active, Submitted, Completed; each with status timeline.
4. **Assignment detail** — brief, product, contract, order tracking, content upload, deadlines, messages.
5. **Content studio** — upload, caption editor, AI caption suggestions, brand guidelines checklist, submission history.
6. **Portfolio** — grid of work; add from approved content in one tap; public profile preview.
7. **Profile** — stats, niches, rates, barter prefs, sizes, social accounts.
8. **Earnings** — pending/available, payout history, instant payout CTA, tax forms.
9. **Messages** — chat with brands.
10. **Notifications** — filtered feed.

## 5.5 Information Architecture (Brand)

```
Brand
├─ Home (Today)
├─ Campaigns
│  ├─ All
│  ├─ Drafts / Active / Completed
│  └─ New (AI wizard / Bulk seed)
├─ Products
├─ Creators (Marketplace)
│  ├─ Search
│  ├─ Saved lists
│  └─ Creator profile
├─ Assignments / Orders
├─ Content Inbox
├─ Analytics
├─ Messages
├─ Billing
├─ Team
└─ Settings (Channels, Notifications, Webhooks/API)
```

## 5.6 Information Architecture (Creator)

```
Creator
├─ Home
├─ Marketplace
├─ Assignments (Invitations / Active / Submitted / Completed)
├─ Content Studio
├─ Portfolio / Public Profile
├─ Earnings
├─ Messages
└─ Profile / Settings
```

## 5.7 Key UX States to Design

- Empty states (no products yet → first sync CTA; no campaigns → AI suggestion card).
- Loading skeletons (catalog, marketplace).
- Error/recovery (sync failure with retry; failed upload with resume).
- Permission prompts (camera for content capture, notifications for invites).
- Billing gates (upgrade nudges contextual to action, e.g., "Pro unlocks bulk seeding").
- Embedded Shopify mode (App Bridge navigation, no outer chrome duplication).
- Accessibility: WCAG 2.1 AA, contrast, touch targets ≥44px, labels.

## 5.8 Public / Marketing Surfaces

- Landing page with product explainer, pricing, creator signup CTA.
- **Public creator profiles** (`/c/{slug}`) — SEO-friendly, portfolio, stats, "Invite to campaign".
- **Public campaign pages** for invite links (creators not logged in see a branded landing + apply).
- Referral tracking via `/r/{code}`.
