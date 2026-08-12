# Instagram Graph API — Setup guide (CreatorPlex)

This is the **one-time** setup so creators can tap **"Connect Instagram"** in their
CreatorPlex profile and we pull real followers, reach, and engagement rate from
their **Instagram Business / Creator** account — no more self-reported vanity
numbers.

CreatorPlex uses the **Instagram Graph API** (through a Facebook Login App).
It's the same API brands use to schedule posts and read insights.

> Instagram *Basic Display* API is being retired — do **not** use it. We use
> **Instagram Graph** (which requires a Facebook Page linked to an IG Business /
> Creator account).

---

## What you get after setup

| Field in CreatorPlex | Where it comes from |
|---|---|
| `follower_count_total` | `/{ig-user-id}?fields=followers_count` |
| `engagement_rate` | Avg of `like_count + comments_count` from last 25 media ÷ followers |
| `content_categories`, `bio` | `/{ig-user-id}?fields=biography` |
| `audience_female_pct`, `audience_male_pct` | `/{ig-user-id}/insights?metric=audience_gender_age` |
| `audience_city_top`, `audience_country_top` | `/{ig-user-id}/insights?metric=audience_city,audience_country` |
| Auto-refresh | Nightly `instagram:sync` command at 05:00 IST |

---

## 1. Create the Facebook / Meta developer app

1. Go to <https://developers.facebook.com/apps> and click **Create App**.
2. **Use case** → *Other* → **Business** (this exposes the Instagram Graph
   products).
3. **App name** → `CreatorPlex` (or whatever the brand is). **Contact email** →
   your admin inbox.
4. Once the app is created, from the left sidebar click **Add products** and
   add:
   - **Facebook Login for Business**
   - **Instagram Graph API**
   - **Instagram Basic Display** *(only if you want back-compat, optional)*

## 2. Configure Facebook Login

In **Facebook Login → Settings**:

- **Valid OAuth Redirect URIs** — add both:
  ```
  https://creatorplex.rankboosterinfotech.in/creator/instagram/callback
  http://127.0.0.1:8000/creator/instagram/callback
  ```
- Turn **Client OAuth Login** and **Web OAuth Login** ON.
- Leave **Enforce HTTPS** ON (default).

## 3. App Domains + Site URL

In **Settings → Basic**:

- **App Domains**: `creatorplex.rankboosterinfotech.in`
- **Privacy Policy URL**: `https://creatorplex.rankboosterinfotech.in/legal/privacy`
- **Terms of Service URL**: `https://creatorplex.rankboosterinfotech.in/legal/terms`
- **Data Deletion Callback URL**: `https://creatorplex.rankboosterinfotech.in/legal/data-deletion`
- **Category**: *Business and Pages*
- **App Icon**: 1024×1024 PNG of the CP mark

Save. Copy the **App ID** and **App Secret** from the top of this page — you'll
paste them into CreatorPlex in step 6.

## 4. Request permissions (Advanced Access)

From **App Review → Permissions and Features**, request **Advanced Access** for
each of these. All are required for the CreatorPlex sync:

| Permission | Why we need it |
|---|---|
| `instagram_basic` | Read profile + media list |
| `instagram_manage_insights` | Read follower/reach/impressions insights |
| `pages_show_list` | Find the Facebook Page linked to the creator's IG business account |
| `pages_read_engagement` | Read engagement metrics on the linked Page |
| `business_management` | Access assets owned by a Business account |
| `public_profile` | Baseline — always granted |

For each permission you'll fill a form with:

- **Platform**: Website
- **How your app uses it**: *"We help D2C brands run influencer marketing
  campaigns. Creators voluntarily connect their Instagram Business account so
  brands can see verified follower counts, engagement rate, and audience
  demographics before offering them a paid or barter collaboration. Data is
  used only inside the creator's private profile and shared with brands the
  creator actively applies to. Full deletion available via the account page."*
- **Screencast**: 30-60s Loom showing:
  1. A creator logging into CreatorPlex → clicks **Connect Instagram**.
  2. Meta's OAuth screen → creator picks their FB Page → grants permissions.
  3. Back in CreatorPlex, the profile shows their real follower count + ER.
  4. Creator hits **Disconnect** → data is wiped.

Meta reviews take **3–7 business days**. Until then, only accounts listed under
**App Roles** can complete the OAuth flow (perfect for internal testing).

## 5. Add test users (skip if already in Live mode)

**App Roles → Roles → Add People**:

- **Instagram Testers** — every internal creator's IG handle
- **Test Users** — your own FB account

Each tester has to **accept the invitation** from Instagram Settings →
Apps and Websites → Tester Invitations, otherwise the OAuth flow rejects them.

## 6. Paste keys into CreatorPlex admin

1. Log in as an admin at `/admin/integrations`.
2. Scroll to the **📸 Instagram** section.
3. Fill:
   - **App ID** → from step 3
   - **App Secret** → from step 3
   - **Redirect URI** → leave the default
     (`https://creatorplex.rankboosterinfotech.in/creator/instagram/callback`)
   - **Enabled** → toggle ON
4. Save. The green "Instagram is live" pill should appear next to the section.
5. Click **Test connection** — this hits `/v19.0/me` with an App Access Token
   and confirms the credentials are valid.

## 7. Creators connect their account

Ask a test creator to:

1. Log in → **Profile** → **Connect Instagram** (violet button in the
   Instagram card).
2. Meta OAuth → pick the FB Page that's linked to their IG Business account.
3. Grant every permission (missing any = broken sync).
4. Get redirected back to CreatorPlex with a green **✓ Connected** pill and
   the follower/ER numbers populated in ~5 seconds.

If they don't see their Page in the picker, it means their IG account is
still Personal — they need to **switch to Creator or Business** first
(Instagram app → Settings → Account → Switch account type) and link it to a
Facebook Page.

## 8. Nightly refresh

CreatorPlex runs `instagram:sync --limit=200` daily at **05:00 IST** via
Laravel Scheduler. Make sure your production server has the Laravel cron
installed:

```cron
* * * * * cd /var/www/creatorplex && php artisan schedule:run >> /dev/null 2>&1
```

Verify it's running with:
```bash
php artisan schedule:list
# Should show:  instagram:sync ................ 0 5 * * *
```

Long-lived tokens are valid for **60 days** and CreatorPlex auto-refreshes
them on every sync. If a creator revokes access, the next sync marks their
Instagram row `status = 'revoked'` and hides the verified badge.

---

## Troubleshooting

| Symptom | Fix |
|---|---|
| **"Page requested cannot be displayed"** on FB OAuth screen | The Redirect URI in step 2 doesn't match. It must be **byte-identical** including trailing slash. |
| **"Invalid platform app"** error after OAuth | You're in Development mode and the creator isn't added as a Tester (step 5). |
| **`(#100) instagram_business_account is missing`** | The creator's IG is still Personal. They need to switch to Business/Creator and link to a Page. |
| **`(#10) requires business_management permission`** | You didn't request `business_management` in App Review (step 4), OR the creator declined it during OAuth. |
| **Tokens expire after 60 days** | Fine — the nightly sync refreshes them. Long-lived tokens can be refreshed for up to 6 months of inactivity. |
| **Rate-limit errors (`(#4) Application request limit reached`)** | Increase `--limit=` on `instagram:sync` OR spread the sync across multiple times of day. Meta's default is 200 calls/hour per user. |

---

## API endpoints CreatorPlex hits

Every call is made through `App\Support\InstagramGraphService`. Base URL:
`https://graph.facebook.com/v19.0`.

```
GET  /oauth/access_token
     ?client_id={app-id}
     &client_secret={app-secret}
     &grant_type=fb_exchange_token
     &fb_exchange_token={short-lived-token}

GET  /me/accounts?fields=name,instagram_business_account
GET  /{ig-user-id}?fields=followers_count,media_count,biography,username
GET  /{ig-user-id}/media?fields=like_count,comments_count,timestamp&limit=25
GET  /{ig-user-id}/insights?metric=audience_gender_age,audience_city,audience_country&period=lifetime
```

That's the complete surface — nothing else.

---

## Data policy note (for legal / privacy page)

CreatorPlex stores **only the fields listed in the "What you get" table** and
**only for the creators who actively connect their account**. We never scrape
public IG data and never sell it. Creators can hit **Disconnect** in their
profile at any time to instantly wipe the token + the derived metrics.
Documented on `/legal/privacy`.
