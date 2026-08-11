# 6 — AI Features & Differentiating Workflows

AI is a cross-cutting capability, not a page. It is delivered through a **pluggable LLM gateway** (OpenAI/Anthropic, with model selection per task), embeddings for semantic matching, and queued/async processing via Horizon. Every AI action is logged in `ai_runs` for cost, latency, and evals.

## 6.1 Core AI Features (from the brief, upgraded)

### 1. Store & Product Analysis (on connect/import)
When products sync, AI:
- Categorizes the store into a **niche and sub-niches**.
- Detects **hero products** using a `hero_score` based on price, margin proxy, visual quality, description richness, review proxy, and category virality.
- Tags products by content suitability (e.g., "demo-friendly", "unboxing-friendly", "aesthetic").
- Identifies target audience demographics and content angles.
- Flags potential policy/Compliance issues (health claims, regulated categories).

**Output:** structured JSON stored on `products.ai_analysis` and workspace-level AI insights.

### 2. Automatic Campaign Generation
From store analysis + a chosen goal, AI produces a **ready-to-launch campaign**:
- Title, niche, audience, content types, deliverables.
- Hero products with suggested `target_creators`.
- Estimated creator count + invite pool (using acceptance-rate model).
- Budget breakdown (product cost, fees, commission).
- Predicted reach, content count, and **ROI range**.
- Drafts the **campaign brief**.

The merchant sees a polished card: *"We suggest a 60-creator UGC barter campaign for your Matcha Latte mix — predicted 48–72 UGC videos and 3.2x ROI. [Launch] [Edit] [Regenerate for sales]."*

### 3. Campaign Brief Writing
Generates a structured brief:
- Brand story/tone, product highlights, key selling points.
- Content requirements (format, length, hooks, must-mentions, disallowed claims).
- Do's and Don'ts, visual guidelines, hashtags.
- Usage rights, deadlines, submission instructions.
The brand edits in a rich editor; AI can "tighten," "make creator-friendly," or "add compliance note."

### 4. AI Creator Matching & Recommendations
Hybrid ranking:
- **Keyword/facet filter** (niche, location, audience size, barter/paid).
- **Semantic match** from creator bio/portfolio/niche embeddings vs campaign brief embeddings.
- **Performance features** (engagement rate, historical completion, content quality, fraud risk).
- **Audience fit** (creator audience demographics vs product ICP).
- **Predicted performance per creator** (see #6).

Output: a 0–100 `score` with human-readable `reasons` ("Beauty nano creator, 8.2% engagement, completed 14 similar campaigns, audience 78% women 18–34 in US").

### 5. Product Suitability Scoring
For each product-creator pairing, score fit based on creator niche, past content style, audience, and product attributes. Used to recommend which product goes to which creator within a bulk-seed campaign.

### 6. Predicted Campaign Performance / ROI
Per campaign and per creator, predict:
- Expected acceptance rate, content delivery rate, and on-time rate.
- Expected impressions/views/engagement based on creator stats.
- Expected attributed orders/revenue using historical conversion per niche/audience and product AOV.
- ROI = (predicted revenue − product cost − fees − commission) / cost.
Show a range (P25/P50/P75), not a false-precise number. Calibrate continuously against actuals.

### 7. AI Messaging
- Personalized outreach messages per creator (uses their name, niche, a portfolio reference, product angle).
- **A/B variants** auto-generated; the system tracks open/accept rates per variant and allocates sends to the winner (multi-armed bandit).
- AI reply suggestions for brands and creators ("Can you ship by Friday?", "Send changes request: add 5 sec product demo").
- Opt-in **auto-nudges** for late deliverables and thank-yous on approval.
- Tone controls and brand voice profile (learned from past messages/website copy).

### 8. AI Content Review
On submission, before a human sees it:
- Transcribes video (Whisper) and checks for required mentions, prohibited claims, and competitor mentions.
- Scores against brief: hook quality, product visibility, pacing, brand safety, audio/visual quality.
- Detects missing required elements (hashtags, disclosure #ad/#gifted).
- Returns an AI score + structured feedback and suggested change requests.
- Auto-approves high-confidence, low-risk content; flags risky content for human review.
- Brand can configure strictness per campaign.

### 9. AI Fraud Detection
- Ingests creator social stats and audience snapshots.
- Estimates **fake follower %**, engagement-pod patterns, comment quality, view-to-engagement anomalies, and sudden growth spikes.
- Produces a `fraud_risk` score; high-risk creators are hidden/demoted or flagged.
- Runs as a scheduled refresh + on-demand before invites.
- Cross-references device/account signals on applications where available.

### 10. ROI Prediction & Attribution
- Combines discount codes, referral links, UTM, and post-purchase surveys into multi-touch attribution.
- Incrementality estimates for paid-whitelisting campaigns.
- Coached recommendations: "Reallocate budget to these 8 creators — they drove 70% of revenue at 30% of cost."

## 6.2 Innovative Differentiating AI Workflows

These are where CreatorPlex pulls ahead:

**A) "Campaign in a Box" from a product URL.**
Paste any product URL (not just Shopify) — AI scrapes/parses it and builds the full campaign. This unifies the Shopify and web entry points and is a killer top-of-funnel tool.

**B) AI Auto-Pilot Campaign Management.**
An agentic loop that, once enabled for a campaign:
- Sends/optimizes invites,
- Promotes waitlisted creators,
- Chases late shipments/content with personalized messages,
- Routes content to approval,
- Reallocates unused product inventory to new creators,
- Reports weekly.
The brand stays in approval-only mode. This is the "self-driving creator program."

**C) Creator Fit Heatmap for bulk seeding.**
For a 500-creator seed, AI assigns each creator the best-matching product variant (e.g., skin tone/hair type for beauty, size for apparel, regional preference for food). Reduces returns and bad-fit content.

**D) UGC Repurposing Engine.**
Once a creator approves usage rights, AI:
- Picks the best hooks/moments,
- Suggests cutdowns (6s, 15s, 30s), captions, and ad variants,
- Generates a "creative brief" for paid ads,
- Routes finished assets to the brand's ad accounts (Meta/TikTok) — future.
This turns a seeding cost into an ad-content pipeline.

**E) Creative Performance Forecasting.**
Predicts which submitted UGC will perform best as an ad (hook first 3s, pacing, text overlays) and scores "ad-readiness." Brands prioritize boosting those.

**F) Automated Creator Sourcing / "Always-On" Recruitment.**
For hard-to-fill niches, AI searches external creator profiles (with permission/ToS-respecting methods), scores them, and drafts outreach — building waitlists passively.

**G) Brand Voice & Compliance Guardrails.**
Learns the brand's tone and forbidden claims; every AI message/brief/caption is checked. Critical for regulated categories (health, finance, supplements, skincare).

**H) Smart Negotiation Assistant.**
When a creator proposes a fee, AI shows a benchmark ("Beauty nano creators in the US average $X–$Y for this deliverable") and suggests a counter based on predicted ROI. Protects both sides.

**I) Churn/Risk Prediction for Creators & Campaigns.**
Flags creators likely to drop off (low response, similar history) and campaigns at risk (low acceptance, inventory shortfall), with remediation suggestions.

**J) AI Post-Purchase Survey Optimization.**
Generates and tunes the "How did you hear about us?" survey to improve attribution where discount codes aren't used.

**K) "Why did this win/lose?" Explainability.**
For every AI recommendation (match, ROI, content score), show plain-English reasons — builds trust and reduces "AI says no" frustration.

**L) AI-generated Creator Media Kit.**
Auto-builds a polished media kit PDF/page from a creator's stats, portfolio, and top-performing content.

## 6.3 AI Architecture & Guardrails

- **Gateway abstraction:** `LlmGateway` interface with `complete()`, `chat()`, `embed()`, `transcribe()`, `moderate()`. Swap providers without touching domains.
- **Prompt management:** versioned prompt classes with typed inputs/outputs; structured JSON output via function calling/JSON mode.
- **Async by default:** AI runs on the `ai` queue; UI shows progress and pushes results via Reverb.
- **Cost controls:** per-workspace monthly AI budget/caps; cache embeddings; use cheaper models for simple tasks.
- **Human in the loop:** AI never sends outreach, releases payouts, or approves content without a defined policy and human/brand override.
- **Evals:** maintain a golden set (brief quality, match accuracy, content scoring precision) run in CI to catch prompt regressions.
- **Safety/PII:** redact PII in prompts; moderate inputs/outputs; log for audit.
- **Feature flags:** ship AI features behind Pennant flags; A/B test quality vs cost.
- **No black-box trust:** display confidence and reasons; allow "thumbs up/down" to fine-tune rankings.

## 6.4 AI Data Flywheel (long-term moat)

1. More campaigns → more outcomes (acceptance, content, sales).
2. Outcomes train/validate matching and ROI models.
3. Better predictions → more brand trust and spend.
4. More spend → more creators and content.
5. More content → better content scoring and UGC repurposing.
This data network effect is the defensibility; design the schema to capture labels from day one.
