# 🛍️ Shopify App Setup — CreatorPlex

Complete step-by-step guide to publish CreatorPlex as a Shopify app that Indian DTC brands can install from the Shopify App Store (or install as a custom/development app on their own store).

**Your live app URL:** `https://creatorplex.rankboosterinfotech.in`

---

## 0. What you're setting up

The `/shopify` routes and controllers are already wired in CreatorPlex:

| Purpose | URL on your server |
| --- | --- |
| Public install form | `https://creatorplex.rankboosterinfotech.in/shopify/install` |
| OAuth callback (must match Shopify exactly) | `https://creatorplex.rankboosterinfotech.in/shopify/callback` |
| Webhook receiver (HMAC verified) | `https://creatorplex.rankboosterinfotech.in/webhooks/shopify` |

---

## 1. Create the Shopify Partners account

1. Go to <https://partners.shopify.com> → **Join now**.
2. Fill business details (Rank Booster Infotech / CreatorPlex).
3. Verify email.

You'll land in the Partner Dashboard.

---

## 2. Create the app

1. Partner Dashboard → **Apps** → **Create app** → **Create app manually**.
2. **App name:** `CreatorPlex — Influencer Marketing`
3. **App URL:** `https://creatorplex.rankboosterinfotech.in/shopify/install`
4. **Allowed redirection URL(s):**
   ```
   https://creatorplex.rankboosterinfotech.in/shopify/callback
   ```
5. Click **Create app**.

You'll be shown your **Client ID** and **Client secret**. Copy both — you need them for `.env`.

---

## 3. Configure the app

From the app's page → **Configuration** → set:

### App URL
```
https://creatorplex.rankboosterinfotech.in/shopify/install
```

### Preferences → Redirection URLs (both entries, exactly)
```
https://creatorplex.rankboosterinfotech.in/shopify/callback
```

### Embedded app
- **Turn OFF** "Embed app in Shopify admin" for now (CreatorPlex is a full-page workflow app, not App Bridge / Polaris embedded).
- You can switch this on later once you build an embedded dashboard.

### Protected customer data access (required by Shopify since 2024)
Under **API access → Protected customer data**:
- Request **Customer name, email, phone** (needed to auto-create creator orders for barter seeding).
- **Data usage reason:** "Auto-create shipping orders for creators who accept barter campaigns."

### GDPR / mandatory compliance webhooks
Shopify requires 3 endpoints. Point all three at your webhook URL:
```
customers/data_request        → https://creatorplex.rankboosterinfotech.in/webhooks/shopify
customers/redact              → https://creatorplex.rankboosterinfotech.in/webhooks/shopify
shop/redact                   → https://creatorplex.rankboosterinfotech.in/webhooks/shopify
```
(The `ShopifyWebhookController` already handles arbitrary topics and verifies HMAC.)

---

## 4. API Scopes

CreatorPlex requests these scopes (already declared in `config/creatorplex.php` → `shopify.scopes`):

- `read_products`
- `write_products`
- `read_inventory`
- `read_orders`
- `write_orders`
- `read_discounts`
- `write_discounts`
- `read_customers`
- `read_content`

You don't need to configure scopes in the Partner Dashboard — they're passed at install time in the OAuth redirect. But if Shopify's review asks "why do you need each scope?" here's what to say:

| Scope | Justification |
| --- | --- |
| `read_products` / `write_products` | Sync store catalogue into the CreatorPlex marketplace; tag creator-seeded SKUs. |
| `read_inventory` | Prevent seeding creators for out-of-stock SKUs. |
| `read_orders` | Attribute every order back to the creator who drove it (discount code + UTM). |
| `write_orders` | Auto-create the draft order that ships a barter creator their product. |
| `read_discounts` / `write_discounts` | Generate one unique code per creator + track redemptions. |
| `read_customers` | Detect repeat orders under the same customer for lifetime value attribution. |
| `read_content` | Read shop policies to auto-fill creator briefs. |

---

## 5. Configure webhook signing

The webhook controller verifies the HMAC signature against the shared secret. In the Partner Dashboard you'll see it labelled **API secret key** — that's the same value as your `SHOPIFY_CLIENT_SECRET`.

Set both in `.env`:
```env
SHOPIFY_CLIENT_ID=xxxxxxxxxxxxxxxxxxxxxxxxxx
SHOPIFY_CLIENT_SECRET=shpss_xxxxxxxxxxxxxxxxxxxxxxxxx
SHOPIFY_WEBHOOK_SECRET=shpss_xxxxxxxxxxxxxxxxxxxxxxxxx   # same as client secret
SHOPIFY_API_VERSION=2025-01
SHOPIFY_APP_BRIDGE=false
FAKE_EXTERNAL_CALLS=false                                 # <-- turn OFF the demo/mock so real API calls fire
```

Then:
```bash
php artisan config:clear
php artisan cache:clear
```

---

## 6. Test the install locally / on your live URL

1. Open <https://creatorplex.rankboosterinfotech.in/shopify/install>.
2. Type your Shopify dev store subdomain (e.g. `creatorplex-dev`) into the form — it'll suffix `.myshopify.com` automatically.
3. Click **Install on Shopify →**.
4. You'll be redirected to Shopify's OAuth consent screen listing every scope above.
5. Approve → Shopify redirects back to `/shopify/callback?shop=…&code=…&state=…`.
6. `ShopifyInstallController@callback` exchanges the code for a token, creates/attaches a Workspace + Channel, and drops the user on `/brand` (CreatorPlex brand dashboard).

If anything fails, check `storage/logs/laravel.log` — the callback logs errors and does a graceful fallback to the AnalyzeStore action.

---

## 7. Development store (for testing before App Store submission)

1. Partner Dashboard → **Stores** → **Add store** → **Development store**.
2. Store type: **Create a store to test and build**.
3. Fill any name/address (India, INR).
4. Once created, install your app from **Apps → Test on development store**.
5. Add a few dummy products so the sync has data.

---

## 8. Custom / private app path (skip the App Store)

If you're only serving specific brands (e.g. Rank Booster's own clients) you don't need to submit to the App Store. Just:
1. Keep the app in **Development** mode.
2. Send each brand this exact link (replace with their store subdomain):
   ```
   https://creatorplex.rankboosterinfotech.in/shopify/install?shop=THEIRSTORE.myshopify.com
   ```
3. They approve once → installed.

That's it. No review required.

---

## 9. Publishing to the Shopify App Store (public listing)

Only do this once you have 10+ happy paying installs. Then:

1. Partner Dashboard → the app → **Distribution** → **Public distribution → Manage listing**.
2. Prepare the assets Shopify requires:
   - **App icon** — 1200×1200 PNG (use the CP gradient logo in `resources/views/partials/marketing-nav.blade.php`).
   - **Feature image** — 1600×900 hero graphic.
   - **6+ screenshots** — export `/preview/*.html` at 1600×1000 or use the built-in `<x-marketing.app-screenshot>` component pages.
   - **Demo video** — 60-second Loom of the campaign flow.
   - **Short description** (≤160 chars): "AI influencer marketing for Indian DTC brands. Seed 100 creators, unique codes per creator, attribute every ₹."
   - **Long description** (≤1000 chars): Pull from `resources/views/marketing/features.blade.php` bullets.
3. **Pricing** — pick "Free" or "Recurring charge" (`Recurring · ₹2,499/mo` matches your Pricing page's Growth plan).
4. **App Store category:** `Marketing → Content marketing` (secondary: `Store management → Orders`).
5. **Test instructions for reviewer:**
   ```
   Test brand: brand@creatorplex.test / password (auto-provisioned on install)
   Steps:
     1. Install into any dev store.
     2. Products sync automatically within 30 seconds.
     3. Go to Brand dashboard → Create campaign → pick a product → set audience filters (city, tier).
     4. Launch campaign → 3 sample creators auto-invited (demo mode).
     5. Verify discount codes appear in the Shopify admin Discounts section.
   ```
6. Submit → 5–10 business days for review.

---

## 10. Post-install checks (run these after your first real install)

```bash
# All should return 200 and log a payload
curl -X POST https://creatorplex.rankboosterinfotech.in/webhooks/shopify \
  -H "X-Shopify-Topic: products/create" \
  -H "X-Shopify-Hmac-Sha256: (Shopify will send a real one)" \
  -H "X-Shopify-Shop-Domain: teststore.myshopify.com" \
  -d '{"id":123,"title":"Test"}'
```

- Products should appear at `/brand/products` within 60 seconds.
- Discount codes generated by campaigns should show up in the Shopify Admin → Discounts.
- Orders with a creator's code should appear as attributed revenue on the campaign page within 15 minutes.

---

## 11. Uninstall + data retention

- Shopify sends `app/uninstalled` webhook → `ShopifyWebhookController` marks the channel `status = disconnected` but keeps campaigns + attribution history (needed for tax + audit).
- Full deletion is triggered only by `shop/redact` (Shopify GDPR endpoint, 48h after uninstall).

---

## 12. TL;DR checklist

- [ ] Partner Dashboard account created
- [ ] App created with correct App URL + callback
- [ ] Client ID + secret pasted into `.env`
- [ ] `FAKE_EXTERNAL_CALLS=false` set
- [ ] GDPR webhooks pointed at `/webhooks/shopify`
- [ ] Development store installed successfully
- [ ] First product sync + first attributed order verified
- [ ] Public listing assets prepared (optional, only if going to App Store)

**Questions?** Everything Shopify-related lives in:
- Controllers: `app/Http/Controllers/Shopify/`
- API client: `app/Domains/Commerce/Channels/Shopify/`
- Webhook jobs: `app/Jobs/HandleShopifyWebhook.php`
- Config: `config/creatorplex.php` (section `shopify`)
- Public install view: `resources/views/shopify/install.blade.php`
