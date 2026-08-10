# 3 — Database Design (MySQL 8)

Conventions:
- Logical multi-tenancy: every tenant-owned table has `workspace_id`.
- Creators are **global** to the marketplace (no `workspace_id` on `creators`).
- All money stored as integer minor units (`*_cents`) with `currency`.
- `uuid` primary keys for public-facing resources; `bigint` internal IDs where it helps joins.
- Soft deletes on core business records; hard deletes for GDPR via queued purge.
- Timestamps (`created_at`, `updated_at`) everywhere; `deleted_at` where noted.

Below is the complete schema grouped by domain.

## 3.1 Identity, Tenancy & Teams

```sql
-- Agencies own many workspaces (future-proof; nullable for now)
CREATE TABLE agencies (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid            CHAR(36) NOT NULL UNIQUE,
  name            VARCHAR(190) NOT NULL,
  plan            VARCHAR(40) DEFAULT 'agency',
  owner_id        BIGINT UNSIGNED NULL,
  settings        JSON NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL
);

-- A brand account / tenant
CREATE TABLE workspaces (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid            CHAR(36) NOT NULL UNIQUE,
  agency_id       BIGINT UNSIGNED NULL,
  name            VARCHAR(190) NOT NULL,
  website         VARCHAR(255) NULL,
  logo_path       VARCHAR(255) NULL,
  country         CHAR(2) NULL,
  currency        CHAR(3) DEFAULT 'USD',
  timezone        VARCHAR(60) DEFAULT 'UTC',
  plan            VARCHAR(40) DEFAULT 'free',
  plan_status     VARCHAR(40) DEFAULT 'trialing',
  onboarding_step VARCHAR(40) DEFAULT 'signup',
  onboarding_completed_at TIMESTAMP NULL,
  settings        JSON NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL,
  INDEX idx_agency (agency_id),
  INDEX idx_plan (plan_status)
);

CREATE TABLE users (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid            CHAR(36) NOT NULL UNIQUE,
  name            VARCHAR(190) NOT NULL,
  email           VARCHAR(190) NOT NULL,
  password        VARCHAR(255) NULL,
  avatar_path     VARCHAR(255) NULL,
  phone           VARCHAR(40) NULL,
  email_verified_at TIMESTAMP NULL,
  last_login_at   TIMESTAMP NULL,
  two_factor_secret VARCHAR(255) NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL,
  UNIQUE (email)
);

-- Brand-side membership (many users per workspace)
CREATE TABLE workspace_users (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id    BIGINT UNSIGNED NOT NULL,
  user_id         BIGINT UNSIGNED NOT NULL,
  role            ENUM('owner','admin','manager','analyst') NOT NULL DEFAULT 'manager',
  invited_at      TIMESTAMP NULL, accepted_at TIMESTAMP NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (workspace_id, user_id),
  INDEX idx_user (user_id)
);

-- Creator-side user link (one user can be a creator)
CREATE TABLE creator_users (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id         BIGINT UNSIGNED NOT NULL UNIQUE,
  creator_id      BIGINT UNSIGNED NOT NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_creator (creator_id)
);
```

## 3.2 Channels (Shopify / Woo / CSV / Manual)

```sql
CREATE TABLE channels (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id    BIGINT UNSIGNED NOT NULL,
  type            ENUM('shopify','woocommerce','amazon','csv','manual','api') NOT NULL,
  name            VARCHAR(190) NULL,
  external_id     VARCHAR(190) NULL,          -- e.g. shopify mystore.myshopify.com
  credentials     TEXT NULL,                   -- encrypted JSON (tokens, secrets)
  settings        JSON NULL,
  status          ENUM('active','paused','disconnected','error') DEFAULT 'active',
  last_synced_at  TIMESTAMP NULL,
  last_full_sync_at TIMESTAMP NULL,
  sync_errors     INT DEFAULT 0,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL,
  INDEX idx_ws_type (workspace_id, type),
  INDEX idx_external (type, external_id)
);

CREATE TABLE channel_webhooks (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  channel_id      BIGINT UNSIGNED NOT NULL,
  external_id     VARCHAR(190) NULL,
  topic           VARCHAR(120) NOT NULL,
  address         VARCHAR(255) NOT NULL,
  status          VARCHAR(40) DEFAULT 'active',
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_channel (channel_id),
  INDEX idx_topic (topic)
);

CREATE TABLE sync_jobs (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  channel_id      BIGINT UNSIGNED NOT NULL,
  workspace_id    BIGINT UNSIGNED NOT NULL,
  type            VARCHAR(60) NOT NULL,        -- products, inventory, orders, full
  mode            ENUM('incremental','full') DEFAULT 'incremental',
  status          ENUM('queued','running','completed','failed') DEFAULT 'queued',
  started_at      TIMESTAMP NULL, finished_at TIMESTAMP NULL,
  processed       INT DEFAULT 0,
  errors          INT DEFAULT 0,
  error_log       LONGTEXT NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id), INDEX idx_status (status)
);
```

## 3.3 Products & Inventory

```sql
CREATE TABLE products (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid            CHAR(36) NOT NULL UNIQUE,
  workspace_id    BIGINT UNSIGNED NOT NULL,
  channel_id      BIGINT UNSIGNED NULL,
  external_id     VARCHAR(190) NULL,           -- shopify product id
  title           VARCHAR(255) NOT NULL,
  description     LONGTEXT NULL,
  vendor          VARCHAR(190) NULL,
  product_type    VARCHAR(190) NULL,
  niche           VARCHAR(120) NULL,           -- AI-detected
  hero_score      DECIMAL(5,2) DEFAULT 0,      -- AI suitability 0-100
  status          ENUM('active','draft','archived') DEFAULT 'active',
  tags            JSON NULL,
  metadata        JSON NULL,                   -- channel-specific payload
  ai_analysis     JSON NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id, status),
  INDEX idx_external (channel_id, external_id),
  FULLTEXT ft_search (title, description)
);

CREATE TABLE product_variants (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id      BIGINT UNSIGNED NOT NULL,
  external_id     VARCHAR(190) NULL,
  sku             VARCHAR(190) NULL,
  title           VARCHAR(255) NULL,
  price_cents     INT NOT NULL DEFAULT 0,
  compare_at_cents INT NULL,
  currency        CHAR(3) DEFAULT 'USD',
  inventory_qty   INT DEFAULT 0,
  inventory_policy VARCHAR(40) DEFAULT 'continue',
  barcode         VARCHAR(60) NULL,
  weight          DECIMAL(10,2) NULL,
  requires_shipping TINYINT(1) DEFAULT 1,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_product (product_id),
  INDEX idx_sku (sku)
);

CREATE TABLE product_images (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id      BIGINT UNSIGNED NOT NULL,
  variant_id      BIGINT UNSIGNED NULL,
  disk            VARCHAR(40) DEFAULT 's3',
  path            VARCHAR(500) NOT NULL,
  alt             VARCHAR(255) NULL,
  position        INT DEFAULT 0,
  is_primary      TINYINT(1) DEFAULT 0,
  width           INT NULL, height INT NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_product (product_id, position)
);

CREATE TABLE collections (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id    BIGINT UNSIGNED NOT NULL,
  external_id     VARCHAR(190) NULL,
  title           VARCHAR(255) NOT NULL,
  slug            VARCHAR(255) NOT NULL,
  description     TEXT NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (workspace_id, slug)
);

CREATE TABLE collection_product (
  collection_id   BIGINT UNSIGNED NOT NULL,
  product_id      BIGINT UNSIGNED NOT NULL,
  position        INT DEFAULT 0,
  PRIMARY KEY (collection_id, product_id)
);
```

## 3.4 Creators (Global Marketplace)

```sql
CREATE TABLE creators (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  user_id                 BIGINT UNSIGNED NULL UNIQUE,
  display_name            VARCHAR(190) NOT NULL,
  slug                    VARCHAR(190) NOT NULL UNIQUE,
  avatar_path             VARCHAR(255) NULL,
  bio                     TEXT NULL,
  email                   VARCHAR(190) NULL,
  phone                   VARCHAR(40) NULL,
  country                 CHAR(2) NULL,
  city                    VARCHAR(120) NULL,
  languages               JSON NULL,
  niches                  JSON NULL,            -- array
  categories              JSON NULL,
  status                  ENUM('pending','active','suspended','banned') DEFAULT 'pending',
  open_to_work            TINYINT(1) DEFAULT 1,
  accepts_barter          TINYINT(1) DEFAULT 1,
  accepts_paid            TINYINT(1) DEFAULT 1,
  accepts_affiliate       TINYINT(1) DEFAULT 1,
  rate_ugc_cents          INT NULL,
  rate_post_cents         INT NULL,
  rate_video_cents        INT NULL,
  rate_story_cents        INT NULL,
  currency                CHAR(3) DEFAULT 'USD',
  follower_count_total    INT DEFAULT 0,
  engagement_rate         DECIMAL(5,2) DEFAULT 0,
  avg_views               INT DEFAULT 0,
  performance_score       DECIMAL(5,2) DEFAULT 0,  -- 0-100, our internal rating
  fraud_risk              DECIMAL(5,2) DEFAULT 0,  -- 0-100, lower is safer
  stripe_connect_id       VARCHAR(190) NULL,
  payout_method_status    VARCHAR(40) DEFAULT 'unverified',
  ai_summary              TEXT NULL,
  metadata                JSON NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL,
  INDEX idx_status (status, open_to_work),
  INDEX idx_niche ((CAST(niches AS CHAR(100)))),  -- functional or use mapping table in prod
  FULLTEXT ft_creator (display_name, bio)
);

-- Proper relational niche/category mapping (preferred over JSON for filtering)
CREATE TABLE creator_niches (
  creator_id      BIGINT UNSIGNED NOT NULL,
  niche           VARCHAR(120) NOT NULL,
  PRIMARY KEY (creator_id, niche),
  INDEX idx_niche (niche)
);

CREATE TABLE creator_social_accounts (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  creator_id      BIGINT UNSIGNED NOT NULL,
  platform        ENUM('instagram','tiktok','youtube','x','facebook','linkedin','pinterest','blog','other') NOT NULL,
  handle          VARCHAR(190) NOT NULL,
  url             VARCHAR(255) NULL,
  follower_count  INT DEFAULT 0,
  engagement_rate DECIMAL(5,2) DEFAULT 0,
  avg_views       INT DEFAULT 0,
  verified        TINYINT(1) DEFAULT 0,
  metadata        JSON NULL,                       -- audience demographics, last stats snapshot
  last_synced_at  TIMESTAMP NULL,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (platform, handle),
  INDEX idx_creator (creator_id)
);

CREATE TABLE creator_portfolio_items (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  creator_id      BIGINT UNSIGNED NOT NULL,
  type            ENUM('image','video','link','embed','case_study') NOT NULL,
  title           VARCHAR(255) NULL,
  disk            VARCHAR(40) DEFAULT 's3',
  path            VARCHAR(500) NULL,
  external_url    VARCHAR(500) NULL,
  thumbnail_path  VARCHAR(500) NULL,
  description     TEXT NULL,
  metrics         JSON NULL,                        -- views, likes
  position        INT DEFAULT 0,
  created_at      TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_creator (creator_id, position)
);

CREATE TABLE creator_preferences (
  creator_id              BIGINT UNSIGNED PRIMARY KEY,
  barter_product_categories JSON NULL,
  min_paid_cents          INT DEFAULT 0,
  shipping_address        JSON NULL,
  clothing_size           VARCHAR(40) NULL,
  skin_tone               VARCHAR(40) NULL,
  hair_type               VARCHAR(40) NULL,
  availability_status     ENUM('available','busy','inactive') DEFAULT 'available',
  response_time_hours     INT NULL,
  notifications_json      JSON NULL,
  updated_at              TIMESTAMP NULL
);

CREATE TABLE creator_audience_snapshots (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  creator_id      BIGINT UNSIGNED NOT NULL,
  social_account_id BIGINT UNSIGNED NULL,
  snapshot_date   DATE NOT NULL,
  followers       INT, engagement DECIMAL(5,2), avg_views INT,
  age_buckets     JSON NULL, gender_split JSON NULL,
  top_countries   JSON NULL, top_cities JSON NULL,
  fake_follower_pct DECIMAL(5,2) DEFAULT 0,
  created_at      TIMESTAMP NULL,
  INDEX idx_creator_date (creator_id, snapshot_date)
);
```

## 3.5 Campaigns

```sql
CREATE TABLE campaigns (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  created_by              BIGINT UNSIGNED NULL,
  title                   VARCHAR(255) NOT NULL,
  type                    ENUM('barter','paid','affiliate','hybrid') NOT NULL,
  status                  ENUM('draft','matching','inviting','active','paused','completed','cancelled') DEFAULT 'draft',
  niche                   VARCHAR(120) NULL,
  summary                 TEXT NULL,
  brief                   LONGTEXT NULL,                 -- AI/human-written brief (markdown)
  objectives              JSON NULL,                      -- awareness, ugc, sales, reviews
  content_types           JSON NULL,                      -- ['video','photo','story']
  deliverables            JSON NULL,                      -- structured deliverable spec
  usage_rights            JSON NULL,                      -- duration, channels, whitelisting
  exclusivity             JSON NULL,
  start_date              DATE NULL, end_date DATE NULL,
  budget_total_cents      INT DEFAULT 0,
  budget_currency         CHAR(3) DEFAULT 'USD',
  creator_fee_cents       INT DEFAULT 0,
  product_cost_cents      INT DEFAULT 0,
  commission_rate         DECIMAL(5,2) DEFAULT 0,         -- affiliate %
  target_creators         INT DEFAULT 0,                  -- desired accepted count
  invite_pool_size        INT DEFAULT 0,                  -- invites to send
  acceptance_rate_assumed DECIMAL(5,2) DEFAULT 30.00,
  waitlist_size           INT DEFAULT 0,
  ai_generated            TINYINT(1) DEFAULT 0,
  ai_predicted_roi        DECIMAL(8,2) NULL,
  ai_metadata             JSON NULL,
  launched_at             TIMESTAMP NULL, completed_at TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL, deleted_at TIMESTAMP NULL,
  INDEX idx_ws_status (workspace_id, status),
  INDEX idx_type (type, status),
  FULLTEXT ft_camp (title, brief, summary)
);

-- Products assigned to a campaign (bulk seeding: each row has a target count)
CREATE TABLE campaign_products (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  product_id              BIGINT UNSIGNED NOT NULL,
  variant_id              BIGINT UNSIGNED NULL,
  target_creators         INT NOT NULL DEFAULT 1,        -- Product A -> 100
  accepted_count          INT NOT NULL DEFAULT 0,
  shipped_count           INT NOT NULL DEFAULT 0,
  content_received_count  INT NOT NULL DEFAULT 0,
  fee_cents               INT DEFAULT 0,
  commission_rate         DECIMAL(5,2) NULL,
  settings                JSON NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (campaign_id, product_id, variant_id),
  INDEX idx_campaign (campaign_id)
);

-- Eligibility/matching snapshot for campaign
CREATE TABLE campaign_creator_matches (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  score                   DECIMAL(5,2) NOT NULL,         -- AI 0-100
  reasons                 JSON NULL,
  predicted_performance   JSON NULL,
  status                  ENUM('candidate','invited','accepted','declined','waitlisted','rejected') DEFAULT 'candidate',
  invited_at              TIMESTAMP NULL, responded_at TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (campaign_id, creator_id),
  INDEX idx_status (campaign_id, status),
  INDEX idx_score (campaign_id, score)
);

-- The actionable assignment once a creator accepts
CREATE TABLE campaign_assignments (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  campaign_product_id     BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  status                  ENUM('accepted','contract_sent','contract_signed',
                               'order_created','shipped','delivered',
                               'in_progress','submitted','changes_requested',
                               'approved','completed','cancelled') DEFAULT 'accepted',
  discount_code           VARCHAR(60) NULL,
  channel_order_id        BIGINT UNSIGNED NULL,
  contract_id             BIGINT UNSIGNED NULL,
  content_due_date        DATE NULL,
  fee_cents               INT DEFAULT 0,
  commission_rate         DECIMAL(5,2) NULL,
  payout_id               BIGINT UNSIGNED NULL,
  tracking                JSON NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (campaign_id, creator_id, campaign_product_id),
  INDEX idx_creator (creator_id, status),
  INDEX idx_status (status, content_due_date)
);

CREATE TABLE campaign_invitations (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  campaign_product_id     BIGINT UNSIGNED NULL,
  channel                 ENUM('email','in_app','sms','push') DEFAULT 'in_app',
  message                 TEXT NULL,
  ai_variant              VARCHAR(40) NULL,
  status                  ENUM('queued','sent','opened','accepted','declined','expired') DEFAULT 'queued',
  sent_at                 TIMESTAMP NULL, opened_at TIMESTAMP NULL,
  responded_at            TIMESTAMP NULL, expires_at TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_campaign (campaign_id, status),
  INDEX idx_creator (creator_id)
);

CREATE TABLE applications (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  cover_note              TEXT NULL,
  proposed_fee_cents      INT NULL,
  status                  ENUM('submitted','shortlisted','approved','rejected','withdrawn') DEFAULT 'submitted',
  reviewed_by             BIGINT UNSIGNED NULL, reviewed_at TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (campaign_id, creator_id),
  INDEX idx_status (campaign_id, status)
);

CREATE TABLE waitlist_entries (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  campaign_product_id     BIGINT UNSIGNED NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  position                INT NOT NULL,
  status                  ENUM('waiting','promoted','expired','removed') DEFAULT 'waiting',
  promoted_at             TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (campaign_id, creator_id),
  INDEX idx_pos (campaign_id, status, position)
);
```

## 3.6 Contracts, Orders, Discounts, Content

```sql
CREATE TABLE contracts (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  assignment_id           BIGINT UNSIGNED NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  title                   VARCHAR(255) NOT NULL,
  body                    LONGTEXT NOT NULL,
  usage_rights            JSON NULL,
  fee_cents               INT DEFAULT 0,
  status                  ENUM('draft','sent','viewed','signed','declined','expired') DEFAULT 'draft',
  signed_by_creator_at    TIMESTAMP NULL,
  signed_by_brand_at      TIMESTAMP NULL,
  signature_ip            VARCHAR(45) NULL,
  expires_at              TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id, status)
);

CREATE TABLE discount_codes (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  assignment_id           BIGINT UNSIGNED NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  product_id              BIGINT UNSIGNED NULL,
  code                    VARCHAR(60) NOT NULL,
  type                    ENUM('percentage','fixed_amount','free_shipping','full_comp') NOT NULL,
  value                   DECIMAL(8,2) NOT NULL,
  external_id             VARCHAR(190) NULL,
  usage_limit             INT DEFAULT 1,
  times_used              INT DEFAULT 0,
  starts_at               TIMESTAMP NULL, expires_at TIMESTAMP NULL,
  status                  ENUM('active','disabled','expired') DEFAULT 'active',
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  UNIQUE (workspace_id, code),
  INDEX idx_campaign (campaign_id)
);

CREATE TABLE orders (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  channel_id              BIGINT UNSIGNED NULL,
  assignment_id           BIGINT UNSIGNED NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  external_id             VARCHAR(190) NULL,          -- shopify order id
  order_number            VARCHAR(60) NULL,
  email                   VARCHAR(190) NULL,
  subtotal_cents          INT DEFAULT 0,
  total_discount_cents    INT DEFAULT 0,
  total_cents             INT DEFAULT 0,
  currency                CHAR(3) DEFAULT 'USD',
  status                  ENUM('draft','open','paid','fulfilled','cancelled','refunded','returned') DEFAULT 'draft',
  shipping_address        JSON NULL,
  tracking_number         VARCHAR(190) NULL,
  tracking_company        VARCHAR(190) NULL,
  placed_at               TIMESTAMP NULL, fulfilled_at TIMESTAMP NULL, delivered_at TIMESTAMP NULL,
  raw_payload             JSON NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id, status),
  INDEX idx_external (channel_id, external_id),
  INDEX idx_assignment (assignment_id)
);

CREATE TABLE order_items (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  order_id                BIGINT UNSIGNED NOT NULL,
  product_id              BIGINT UNSIGNED NULL,
  variant_id              BIGINT UNSIGNED NULL,
  external_line_id        VARCHAR(190) NULL,
  title                   VARCHAR(255) NOT NULL,
  sku                     VARCHAR(190) NULL,
  quantity                INT NOT NULL DEFAULT 1,
  price_cents             INT NOT NULL DEFAULT 0,
  total_discount_cents    INT DEFAULT 0,
  created_at              TIMESTAMP NULL,
  INDEX idx_order (order_id)
);

CREATE TABLE content_submissions (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  assignment_id           BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  type                    ENUM('image','video','story','reel','link','caption_draft') NOT NULL,
  disk                    VARCHAR(40) DEFAULT 's3',
  path                    VARCHAR(500) NOT NULL,
  thumbnail_path          VARCHAR(500) NULL,
  caption                 TEXT NULL,
  external_post_url       VARCHAR(500) NULL,
  metadata                JSON NULL,
  ai_score                DECIMAL(5,2) NULL,
  ai_feedback             JSON NULL,
  ai_flags                JSON NULL,
  status                  ENUM('submitted','in_review','changes_requested','approved','rejected','published') DEFAULT 'submitted',
  submitted_at            TIMESTAMP NULL, reviewed_at TIMESTAMP NULL, approved_at TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_assignment (assignment_id),
  INDEX idx_creator (creator_id, status)
);

CREATE TABLE content_reviews (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  content_submission_id   BIGINT UNSIGNED NOT NULL,
  reviewer_type           ENUM('brand','ai') NOT NULL,
  reviewer_id             BIGINT UNSIGNED NULL,
  decision                ENUM('approved','changes_requested','rejected') NOT NULL,
  comment                 TEXT NULL,
  ai_annotations          JSON NULL,
  created_at              TIMESTAMP NULL,
  INDEX idx_submission (content_submission_id)
);
```

## 3.7 Messaging & Notifications

```sql
CREATE TABLE message_threads (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  campaign_id             BIGINT UNSIGNED NULL,
  subject                 VARCHAR(255) NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id)
);

CREATE TABLE message_thread_participants (
  thread_id               BIGINT UNSIGNED NOT NULL,
  participant_type        ENUM('user','creator') NOT NULL,
  participant_id          BIGINT UNSIGNED NOT NULL,
  last_read_at            TIMESTAMP NULL,
  muted                   TINYINT(1) DEFAULT 0,
  PRIMARY KEY (thread_id, participant_type, participant_id)
);

CREATE TABLE messages (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  thread_id               BIGINT UNSIGNED NOT NULL,
  sender_type             ENUM('user','creator','system','ai') NOT NULL,
  sender_id               BIGINT UNSIGNED NULL,
  body                    TEXT NOT NULL,
  attachments             JSON NULL,
  ai_generated            TINYINT(1) DEFAULT 0,
  delivered_at            TIMESTAMP NULL, read_at TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_thread (thread_id, created_at)
);

CREATE TABLE notifications (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  recipient_type          ENUM('user','creator') NOT NULL,
  recipient_id            BIGINT UNSIGNED NOT NULL,
  type                    VARCHAR(120) NOT NULL,
  data                    JSON NOT NULL,
  read_at                 TIMESTAMP NULL,
  created_at              TIMESTAMP NULL,
  INDEX idx_recipient (recipient_type, recipient_id, read_at)
);
```

## 3.8 Billing, Payouts & Subscriptions

```sql
CREATE TABLE subscriptions (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  provider                ENUM('stripe','shopify') NOT NULL,
  provider_id             VARCHAR(190) NULL,
  plan                    VARCHAR(40) NOT NULL,
  status                  VARCHAR(40) NOT NULL,
  quantity                INT DEFAULT 1,
  trial_ends_at           TIMESTAMP NULL,
  current_period_start    TIMESTAMP NULL,
  current_period_end      TIMESTAMP NULL,
  cancelled_at            TIMESTAMP NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id, status)
);

CREATE TABLE invoices (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  subscription_id         BIGINT UNSIGNED NULL,
  provider                VARCHAR(40) NOT NULL,
  provider_invoice_id     VARCHAR(190) NULL,
  amount_cents            INT NOT NULL,
  currency                CHAR(3) DEFAULT 'USD',
  status                  VARCHAR(40) NOT NULL,
  due_at                  TIMESTAMP NULL, paid_at TIMESTAMP NULL,
  pdf_path                VARCHAR(500) NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id, status)
);

CREATE TABLE payouts (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  creator_id              BIGINT UNSIGNED NOT NULL,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  assignment_id           BIGINT UNSIGNED NULL,
  amount_cents            INT NOT NULL,
  currency                CHAR(3) DEFAULT 'USD',
  platform_fee_cents      INT DEFAULT 0,
  processing_fee_cents    INT DEFAULT 0,
  net_cents               INT NOT NULL,
  method                  ENUM('stripe_connect','bank','paypal','manual') DEFAULT 'stripe_connect',
  status                  ENUM('pending','in_transit','paid','failed','cancelled') DEFAULT 'pending',
  external_transfer_id    VARCHAR(190) NULL,
  scheduled_for           TIMESTAMP NULL,
  paid_at                 TIMESTAMP NULL,
  metadata                JSON NULL,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_creator (creator_id, status),
  INDEX idx_ws (workspace_id, status)
);

CREATE TABLE marketplace_commissions (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  assignment_id           BIGINT UNSIGNED NULL,
  order_id                BIGINT UNSIGNED NULL,
  amount_cents            INT NOT NULL,
  currency                CHAR(3) DEFAULT 'USD',
  rate                    DECIMAL(5,2) NOT NULL,
  status                  ENUM('pending','held','released','waived','refunded') DEFAULT 'pending',
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_ws (workspace_id, status)
);
```

## 3.9 Analytics & Attribution

```sql
CREATE TABLE visits (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  uuid                    CHAR(36) NOT NULL UNIQUE,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NULL,
  campaign_id             BIGINT UNSIGNED NULL,
  referral_code           VARCHAR(60) NULL,
  utm_source              VARCHAR(120) NULL,
  utm_medium              VARCHAR(120) NULL,
  utm_campaign            VARCHAR(190) NULL,
  landing_url             VARCHAR(500) NULL,
  ip_country              CHAR(2) NULL,
  referrer                VARCHAR(500) NULL,
  created_at              TIMESTAMP NULL,
  INDEX idx_ws_date (workspace_id, created_at),
  INDEX idx_creator (creator_id)
);

CREATE TABLE attributions (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NOT NULL,
  order_id                BIGINT UNSIGNED NULL,
  creator_id              BIGINT UNSIGNED NOT NULL,
  campaign_id             BIGINT UNSIGNED NULL,
  assignment_id           BIGINT UNSIGNED NULL,
  model                   ENUM('first_touch','last_touch','linear','post_purchase_survey','discount_code') NOT NULL,
  weight                  DECIMAL(5,2) DEFAULT 1.00,
  revenue_cents           INT NOT NULL,
  currency                CHAR(3) DEFAULT 'USD',
  attributed_at           TIMESTAMP NOT NULL,
  created_at              TIMESTAMP NULL,
  INDEX idx_creator (creator_id, attributed_at),
  INDEX idx_ws (workspace_id, attributed_at)
);

-- Pre-aggregated rollups for fast dashboards
CREATE TABLE analytics_daily_campaign (
  workspace_id            BIGINT UNSIGNED NOT NULL,
  campaign_id             BIGINT UNSIGNED NOT NULL,
  creator_id              BIGINT UNSIGNED NULL,
  date                    DATE NOT NULL,
  impressions             INT DEFAULT 0,
  clicks                  INT DEFAULT 0,
  content_count           INT DEFAULT 0,
  orders                  INT DEFAULT 0,
  revenue_cents           INT DEFAULT 0,
  commission_cents        INT DEFAULT 0,
  product_cost_cents      INT DEFAULT 0,
  fee_cents               INT DEFAULT 0,
  PRIMARY KEY (workspace_id, campaign_id, creator_id, date)
);
```

## 3.10 AI & Audit

```sql
CREATE TABLE ai_runs (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NULL,
  subject_type            VARCHAR(100) NOT NULL,    -- product, campaign, creator
  subject_id              BIGINT UNSIGNED NOT NULL,
  task                    VARCHAR(100) NOT NULL,    -- generate_brief, match, content_review, fraud
  model                   VARCHAR(60) NOT NULL,
  prompt                  LONGTEXT NULL,
  response                LONGTEXT NULL,
  tokens_in               INT, tokens_out INT,
  cost_cents              INT DEFAULT 0,
  status                  ENUM('queued','running','completed','failed') DEFAULT 'queued',
  latency_ms              INT,
  created_at              TIMESTAMP NULL, updated_at TIMESTAMP NULL,
  INDEX idx_subject (subject_type, subject_id),
  INDEX idx_ws (workspace_id)
);

CREATE TABLE audit_logs (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  workspace_id            BIGINT UNSIGNED NULL,
  actor_type              ENUM('user','creator','system','ai') NULL,
  actor_id                BIGINT UNSIGNED NULL,
  action                  VARCHAR(120) NOT NULL,
  subject_type            VARCHAR(100) NULL,
  subject_id              BIGINT UNSIGNED NULL,
  changes                 JSON NULL,
  ip                      VARCHAR(45) NULL,
  created_at              TIMESTAMP NULL,
  INDEX idx_ws_action (workspace_id, action, created_at)
);

CREATE TABLE event_log (
  id                      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  event                   VARCHAR(120) NOT NULL,
  aggregate_type          VARCHAR(100) NULL,
  aggregate_id            BIGINT UNSIGNED NULL,
  payload                 JSON NULL,
  created_at              TIMESTAMP NULL,
  INDEX idx_event (event, created_at),
  INDEX idx_agg (aggregate_type, aggregate_id)
);
```

## 3.11 Key Relationships at a Glance

```
Agency 1─* Workspace 1─* Users (via workspace_users)
Workspace 1─* Channels 1─* Products 1─* Variants / Images
Workspace 1─* Campaigns 1─* CampaignProducts *─1 Product
Campaigns 1─* CampaignCreatorMatches *─1 Creator
Campaigns 1─* Assignments 1─1 Order? / 1─1 Contract / 1─* ContentSubmissions
Creator 1─* SocialAccounts / PortfolioItems / Payouts
Assignments ── Attributions ── Orders (campaign revenue)
```

## 3.12 Indexing & Performance Notes

- Composite indexes above cover the hottest dashboard queries (workspace-scoped lists filtered by status/date).
- Use `analytics_daily_campaign` rollups; never aggregate raw `attributions`/`visits` on page load.
- Archive `event_log` and `ai_runs` older than 90 days to cold storage.
- Add read replicas for analytics-heavy workspaces when needed.
- For creator niche filtering at scale, the `creator_niches` mapping table outperforms JSON filtering.
- Use database transactions + idempotency keys for order/discount/payout creation to prevent duplicates from webhook retries.
