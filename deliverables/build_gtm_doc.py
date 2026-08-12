"""
CreatorPlex — GTM, Marketing, SEO, Blog & Reels playbook (DOCX).
Output: deliverables/CreatorPlex-GTM-Marketing-SEO-Blogs-Reels.docx
"""

from docx import Document
from docx.shared import Pt, Inches, RGBColor, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_LINE_SPACING
from docx.enum.table import WD_ALIGN_VERTICAL
from docx.oxml.ns import qn
from docx.oxml import OxmlElement

INK    = RGBColor(0x0F, 0x17, 0x2A)
INK_SO = RGBColor(0x47, 0x55, 0x69)
INK_MU = RGBColor(0x94, 0xA3, 0xB8)
VIOLET = RGBColor(0x7C, 0x3A, 0xED)
PINK   = RGBColor(0xEC, 0x48, 0x99)
AMBER  = RGBColor(0xF5, 0x9E, 0x0B)
EMERAL = RGBColor(0x10, 0xB9, 0x81)
LINE   = RGBColor(0xE2, 0xE8, 0xF0)

doc = Document()

# page setup: A4 with generous margins
sec = doc.sections[0]
sec.page_width  = Cm(21.0)
sec.page_height = Cm(29.7)
sec.top_margin = sec.bottom_margin = Cm(1.8)
sec.left_margin = sec.right_margin = Cm(2.0)

# default font: Inter
style = doc.styles['Normal']
style.font.name = 'Inter'
style.font.size = Pt(11)
style.font.color.rgb = INK
style.paragraph_format.space_after = Pt(6)
style.paragraph_format.line_spacing = 1.35

def _shade(cell, hex_color):
    tc_pr = cell._tc.get_or_add_tcPr()
    shd = OxmlElement('w:shd')
    shd.set(qn('w:val'), 'clear')
    shd.set(qn('w:color'), 'auto')
    shd.set(qn('w:fill'), hex_color)
    tc_pr.append(shd)

def _borders(cell, color='E2E8F0', size='4'):
    tc_pr = cell._tc.get_or_add_tcPr()
    borders = OxmlElement('w:tcBorders')
    for side in ('top', 'left', 'bottom', 'right'):
        b = OxmlElement(f'w:{side}')
        b.set(qn('w:val'), 'single')
        b.set(qn('w:sz'), size)
        b.set(qn('w:color'), color)
        borders.append(b)
    tc_pr.append(borders)

def h1(text, color=VIOLET, size=26):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(24)
    p.paragraph_format.space_after = Pt(6)
    r = p.add_run(text)
    r.font.name = 'Inter'; r.font.size = Pt(size); r.font.bold = True
    r.font.color.rgb = color

def h2(text, color=INK, size=18):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(18)
    p.paragraph_format.space_after = Pt(4)
    r = p.add_run(text)
    r.font.name = 'Inter'; r.font.size = Pt(size); r.font.bold = True
    r.font.color.rgb = color

def h3(text, color=INK, size=14):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(12)
    p.paragraph_format.space_after = Pt(2)
    r = p.add_run(text)
    r.font.name = 'Inter'; r.font.size = Pt(size); r.font.bold = True
    r.font.color.rgb = color

def para(text, color=INK, size=11, bold=False, italic=False):
    p = doc.add_paragraph()
    r = p.add_run(text)
    r.font.name = 'Inter'; r.font.size = Pt(size); r.font.bold = bold; r.font.italic = italic
    r.font.color.rgb = color
    return p

def bullets(items, color=INK, size=11):
    for it in items:
        p = doc.add_paragraph(style='List Bullet')
        r = p.add_run(it)
        r.font.name = 'Inter'; r.font.size = Pt(size); r.font.color.rgb = color

def numbered(items, color=INK, size=11):
    for it in items:
        p = doc.add_paragraph(style='List Number')
        r = p.add_run(it)
        r.font.name = 'Inter'; r.font.size = Pt(size); r.font.color.rgb = color

def eyebrow(text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(6)
    p.paragraph_format.space_after = Pt(0)
    r = p.add_run(text.upper())
    r.font.name = 'Inter'; r.font.size = Pt(9); r.font.bold = True
    r.font.color.rgb = VIOLET

def hr():
    p = doc.add_paragraph()
    pPr = p._p.get_or_add_pPr()
    pBdr = OxmlElement('w:pBdr')
    bottom = OxmlElement('w:bottom')
    bottom.set(qn('w:val'), 'single')
    bottom.set(qn('w:sz'), '6')
    bottom.set(qn('w:color'), 'E2E8F0')
    pBdr.append(bottom)
    pPr.append(pBdr)

def add_table(headers, rows, header_bg='7C3AED', header_fg='FFFFFF', alt_bg='FAFAFC'):
    tbl = doc.add_table(rows=1, cols=len(headers))
    tbl.autofit = True
    hdr = tbl.rows[0].cells
    for i, h in enumerate(headers):
        hdr[i].text = ''
        p = hdr[i].paragraphs[0]
        r = p.add_run(h)
        r.font.name = 'Inter'; r.font.bold = True; r.font.size = Pt(10)
        r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
        _shade(hdr[i], header_bg)
        _borders(hdr[i])
        hdr[i].vertical_alignment = WD_ALIGN_VERTICAL.CENTER
    for r_i, row in enumerate(rows):
        cells = tbl.add_row().cells
        for c_i, val in enumerate(row):
            cells[c_i].text = ''
            p = cells[c_i].paragraphs[0]
            run = p.add_run(str(val))
            run.font.name = 'Inter'; run.font.size = Pt(10); run.font.color.rgb = INK
            if r_i % 2 == 0:
                _shade(cells[c_i], alt_bg)
            _borders(cells[c_i])
            cells[c_i].vertical_alignment = WD_ALIGN_VERTICAL.CENTER
    doc.add_paragraph()  # small gap after table

def callout(title, body, bg='F6F3FF', border='7C3AED'):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.autofit = True
    cell = tbl.rows[0].cells[0]
    cell.text = ''
    p1 = cell.paragraphs[0]
    r = p1.add_run(title)
    r.font.name = 'Inter'; r.font.bold = True; r.font.size = Pt(11)
    r.font.color.rgb = VIOLET
    p2 = cell.add_paragraph()
    r2 = p2.add_run(body)
    r2.font.name = 'Inter'; r2.font.size = Pt(10); r2.font.color.rgb = INK
    _shade(cell, bg)
    _borders(cell, color=border, size='8')
    cell.width = Cm(17)
    doc.add_paragraph()

# ==============================================================================
# COVER
# ==============================================================================
p = doc.add_paragraph()
p.paragraph_format.space_before = Pt(60)
p.paragraph_format.space_after = Pt(0)
r = p.add_run('CreatorPlex')
r.font.name = 'Inter'; r.font.size = Pt(40); r.font.bold = True; r.font.color.rgb = VIOLET

p = doc.add_paragraph()
p.paragraph_format.space_after = Pt(4)
r = p.add_run('GTM · Marketing · SEO · Blog · Reels playbook')
r.font.name = 'Inter'; r.font.size = Pt(22); r.font.bold = True; r.font.color.rgb = INK

p = doc.add_paragraph()
r = p.add_run('The 90-day launch plan + 12-month growth engine for creator-commerce in India.')
r.font.name = 'Inter'; r.font.size = Pt(13); r.font.color.rgb = INK_SO
r.italic = True

p = doc.add_paragraph()
p.paragraph_format.space_before = Pt(36)
r = p.add_run('Version 1.0  ·  August 2026  ·  Owner: Growth')
r.font.name = 'Inter'; r.font.size = Pt(10); r.font.color.rgb = INK_MU

hr()

eyebrow('Contents')
bullets([
    '1.  Positioning & messaging house',
    '2.  Ideal customer profiles',
    '3.  Go-to-market motion (90 days)',
    '4.  SEO strategy — programmatic + editorial',
    '5.  Blog content calendar (12 months)',
    '6.  Reels strategy — organic + creator-side',
    '7.  Paid + partnerships',
    '8.  Email + WhatsApp lifecycle',
    '9.  KPIs & reporting',
    '10. Budget allocation',
])
doc.add_page_break()

# ==============================================================================
# 1. POSITIONING & MESSAGING
# ==============================================================================
h1('1.  Positioning & messaging house')

h2('One-liner')
callout('CreatorPlex is the operating system for creator-commerce in India.',
        'AI-matched creators · barter + paid campaigns · escrow · RazorpayX auto-payouts · Shopify-native and web-brand ready.')

h2('Positioning statement')
para('For DTC brands in India who want to run creator-marketing campaigns that actually convert — '
     'CreatorPlex is the AI-powered platform that handles discovery, contracts, payments and attribution in one panel. '
     'Unlike agencies (₹2-5L retainers, opaque results) or spreadsheets (20 hours per campaign, no attribution), '
     'CreatorPlex ships end-to-end automation with India-native rails — UPI, GST, PIN codes, INR — so brands see '
     'real GMV per creator and creators get paid to their UPI within hours.')

h2('Voice & tone')
add_table(
    ['We are…', 'We are NOT…'],
    [
        ['Confident, plain-spoken, founder-to-founder', 'Corporate, buzzword-heavy, "leverage synergies"'],
        ['India-first (₹, UPI, PIN, GST examples always)', 'Silicon Valley speak ($, "Y Combinator style")'],
        ['Numbers over adjectives ("4.8× ROAS")', 'Vague ("supercharge", "revolutionize")'],
        ['Playful gradients, whitespace, gen-Z visual', 'Stock photos of handshakes'],
        ['Educational — we teach creator-commerce', 'Sales-heavy — cold DM spam'],
    ]
)

h2('The 3 message pillars')
h3('Pillar 1 · "Ship in a day, not a quarter."')
para('Brief a campaign, ship product to 40 creators, and see the first Reel go live in 24 hours. '
     'Every screen is designed for speed: AI-generated briefs, one-click barter kits, WhatsApp + email invites.')
h3('Pillar 2 · "See real ROAS, not vibes."')
para('UTM + coupon + Shopify webhook = live GMV attribution per creator. No more "we think it worked". '
     'Every rupee traced back to the post.')
h3('Pillar 3 · "Built in India, for India."')
para('UPI + IFSC + PAN payouts. GST invoicing. Indian-city creator taxonomy. INR default with Indian grouping (₹1,84,200.00). '
     'The only platform where the creator gets paid before their brand-partner leaves the office.')

doc.add_page_break()

# ==============================================================================
# 2. ICP
# ==============================================================================
h1('2.  Ideal customer profiles')

h2('ICP 1 · Growth-stage DTC brand (primary)')
para('Persona:  “Marketing manager Neha”  ·  27, at a ₹5-50 Cr revenue brand on Shopify.', bold=True)
add_table(
    ['Dimension', 'Value'],
    [
        ['Revenue',    '₹5 Cr – ₹50 Cr / year'],
        ['Platform',   'Shopify (72%) · Woo (18%) · custom / manual (10%)'],
        ['Category',   'Beauty · Fashion · Food · Home · Fitness · Baby'],
        ['Location',   'Mumbai · Bangalore · Delhi NCR · Pune · Hyderabad · Chennai · Ahmedabad'],
        ['Team size',  '1-5 marketing (usually the founder + 1 growth marketer)'],
        ['Pain #1',    'Spends ₹2-5L / mo on creators with no way to measure ROAS'],
        ['Pain #2',    'Discovery is cold DMs on Instagram — 20 hours per campaign'],
        ['Trigger',    'Just raised funding · launching new category · Diwali / D2C season'],
        ['Buys when',  'Sees a case study OR gets referred by another brand founder'],
    ]
)

h2('ICP 2 · Content creator (supply-side)')
para('Persona:  “Micro-influencer Riya”  ·  22, 45K IG followers, food + lifestyle, tier-2 city.', bold=True)
add_table(
    ['Dimension', 'Value'],
    [
        ['Followers', '1K – 500K (nano to mid)'],
        ['Platform',  'Instagram Reels (85%) · YouTube Shorts (35%) · LinkedIn (12%)'],
        ['Niche',     'Beauty · Fashion · Food · Fitness · Tech · Parenting · Travel'],
        ['Location',  '25+ cities including tier-2/3 (Lucknow · Indore · Coimbatore · Bhubaneswar)'],
        ['Pain #1',   'Gets DMs from 10 brands / week, most are scams or ghost after delivery'],
        ['Pain #2',   'Payments arrive 30-90 days later via bank transfer with 15% TDS friction'],
        ['Trigger',   'A brand-partner she trusts recommends us OR sees the "verified metrics" badge'],
        ['Joins when','She sees another creator got paid on the same day the brand approved content'],
    ]
)

h2('ICP 3 · Agency (channel partner)')
para('Persona:  “Boutique agency owner Rohit”  ·  runs influencer for 20 brands, 6-person team.', bold=True)
para('We white-label our platform to them — they keep their branding, we take a 20% platform fee. '
     'One agency = 20 brands = 20× the leverage of a direct sale.')

doc.add_page_break()

# ==============================================================================
# 3. GTM MOTION
# ==============================================================================
h1('3.  Go-to-market motion (90 days)')

h2('Phase 1 · Weeks 1-4  ·  Foundation')
numbered([
    'Publish landing pages for 11 services × 11 Indian cities = 121 pages (already shipped).',
    'Ship 10 case studies (real brands + creators) with GMV screenshots.',
    'Onboard 200 vetted creators — hand-picked, high-ER, across 6 niches × 6 cities.',
    'Enable Shopify install form + private-install path (already shipped).',
    'Launch the referral program (creators refer creators for ₹500; brands refer brands for 30% commission).',
    'Set up GSC + GA4 + Meta Pixel + Hotjar (integrated via /admin/integrations).',
])

h2('Phase 2 · Weeks 5-8  ·  Launch')
numbered([
    'Submit Shopify App Store listing (docs already in /docs/SHOPIFY-APP-SETUP.md).',
    'ProductHunt launch (US audience — awareness; not the main channel).',
    '10 founder-podcast appearances (D2C India · Backstage with Millionaires · The Ken).',
    'LinkedIn founder-post schedule: 3 posts / week (build-in-public tone).',
    'First press push: YourStory, Inc42, ETRetail on the ₹51,000 Cr TAM angle.',
    '200 personalised outbound emails to Shopify Plus brand marketing heads.',
])

h2('Phase 3 · Weeks 9-12  ·  Compound')
numbered([
    'Weekly editorial blog post (playbook below). Target 4 posts / month.',
    'Bi-weekly webinar: "How [brand] shipped 40 barter kits and got ₹6.1L GMV". Recruit brand partners to co-present.',
    'Onboard first 3 agency partners on white-label.',
    'Diwali / holiday campaign kit (free template): brands who use it show up as case studies.',
    'First round of paid ads (₹1L test budget) — Google Search on "influencer marketing agency [city]" long-tail.',
])

h2('90-day north stars')
add_table(
    ['Metric', 'Target Week 4', 'Target Week 8', 'Target Week 12'],
    [
        ['Brand signups (workspaces)',     '30',     '150',     '400'],
        ['Verified creators',              '200',    '600',     '1,500'],
        ['GMV attributed',                 '₹15L',   '₹65L',    '₹1.6 Cr'],
        ['Organic monthly visits',         '800',    '2,500',   '6,000'],
        ['Case studies shipped',           '3',      '6',       '10'],
        ['Paid MRR',                       '₹18K',   '₹75K',    '₹2.2 L'],
    ]
)

doc.add_page_break()

# ==============================================================================
# 4. SEO
# ==============================================================================
h1('4.  SEO strategy — programmatic + editorial')

h2('Approach')
para('Two engines running in parallel: (1) programmatic pages that already ship and rank for high-intent local queries, '
     '(2) editorial long-form that captures top-of-funnel + earns backlinks.')

h2('Programmatic — the 11 × 11 grid (already live)')
para('11 services × 11 Indian cities = 121 landing pages. Each has: keyword-optimised H1, service description, '
     'city-specific rate benchmarks (INR), 3 case-study cards, FAQ schema, HowTo schema, SoftwareApplication schema.', bold=False)

para('Services covered:')
bullets([
    'Influencer marketing agency',
    'Barter / seeding campaigns',
    'UGC campaign management',
    'Reels marketing',
    'Product-review campaigns',
    'Brand awareness campaigns',
    'Affiliate / performance influencer',
    'Micro-influencer marketing',
    'YouTube integration campaigns',
    'Shopify influencer app',
    'D2C creator marketing',
])
para('Cities: Mumbai · Bangalore · Delhi · Pune · Hyderabad · Chennai · Kolkata · Ahmedabad · Jaipur · Lucknow · Chandigarh.')

h2('Editorial — the 12 pillar posts (blog calendar below)')
para('Each pillar is a 2,500-word playbook targeting a “how to” or “vs.” query that a marketing manager Googles. '
     'Every pillar links out to 3-4 sub-posts + 5-6 landing pages (internal link mesh).')

h2('Technical SEO checklist')
bullets([
    'Sitemap.xml auto-generated on deploy · submitted to GSC',
    'Structured data: SoftwareApplication, Organization, WebSite, BlogPosting, FAQ, HowTo — validated on schema.org',
    'Core Web Vitals: LCP < 1.8s (currently 1.4s), CLS < 0.05, INP < 200ms',
    'Mobile-first (60% of traffic) · PWA installable',
    'INR + Indian grouping in all price copy (₹1,84,200.00)',
    'Canonical URLs on all city × service duplicates',
    'Open Graph + Twitter Card meta on every page',
    '2xx status on every internal link · zero JS-only routes for crawlable content',
])

h2('Target keywords (year-1 focus)')
add_table(
    ['Keyword', 'Monthly volume (India)', 'Difficulty', 'Our page'],
    [
        ['influencer marketing agency mumbai',     '1,900', 'Medium', '/services/influencer-marketing-agency/mumbai'],
        ['influencer marketing agency delhi',      '2,400', 'Medium', '/services/influencer-marketing-agency/delhi'],
        ['influencer marketing platform india',    '1,200', 'High',   '/'],
        ['barter influencer campaign',             '590',   'Low',    '/services/barter-campaigns'],
        ['micro influencer marketing india',       '880',   'Medium', '/blog/micro-influencer-guide-india'],
        ['shopify influencer app india',           '390',   'Low',    '/services/shopify-influencer-app'],
        ['ugc creator platform india',             '480',   'Low',    '/services/ugc-campaigns'],
        ['influencer marketing cost india',        '1,600', 'Medium', '/blog/influencer-marketing-cost-india'],
        ['how to find influencers for my brand',   '2,900', 'Medium', '/blog/find-influencers-for-brand-india'],
        ['creator marketplace india',              '320',   'Low',    '/creators'],
    ]
)

h2('Backlink strategy')
bullets([
    'Guest post + HARO responses: Inc42, YourStory, The Ken, ETRetail, Storyboard18 (target 2 / month).',
    'Free tools (rate calculator, ROI calculator, brief generator) — the "hero content" for links. Already shipped.',
    'Original research: publish "State of Creator-Commerce India 2026" annual report with our platform data. Instant PR bait.',
    'Podcast appearances → each episode = 1 backlink from show notes.',
    'Case study co-marketing: brand + creator + CreatorPlex all cross-link.',
])

doc.add_page_break()

# ==============================================================================
# 5. BLOG CALENDAR
# ==============================================================================
h1('5.  Blog content calendar (12 months)')
para('4 posts / month. Every post: 1,800-2,500 words, primary keyword in H1, 2 CTAs (soft: newsletter · hard: sign up).')

h2('Month-by-month')
posts = [
    ('Sep 26',
     [
        ('How to find influencers for your brand in India (2026)', 'find influencers india', 'Playbook'),
        ('Micro vs macro influencers: which drives ROAS for D2C?', 'micro vs macro influencer', 'Comparison'),
        ('The real cost of influencer marketing in India (with data)', 'influencer marketing cost india', 'Data'),
        ('Barter campaigns 101: how to seed 40 kits in a week', 'barter campaign guide', 'How-to'),
     ]),
    ('Oct 26',
     [
        ('Diwali creator playbook: 30-day sprint for D2C brands', 'diwali creator marketing', 'Seasonal'),
        ('How Luxotica shipped 3.4M reach on ₹6L (case study)', 'luxotica case study', 'Case study'),
        ('Instagram Reels engagement benchmarks in India (2026)', 'reels engagement rate india', 'Benchmark'),
        ('Attribution 101: UTMs, coupons, Shopify pixel', 'influencer attribution', 'Education'),
     ]),
    ('Nov 26',
     [
        ('Black Friday for Indian D2C: creator campaigns that work', 'bfcm creator marketing india', 'Seasonal'),
        ('Escrow explained: why creators trust the process', 'creator payment escrow', 'Trust'),
        ('The AI matching stack under the hood', 'ai creator matching', 'Tech blog'),
        ('State of creator-commerce India 2026 — annual report', 'creator economy india 2026', 'Report'),
     ]),
    ('Dec 26',
     [
        ('Year-end brand playbook: relaunch winners, retire flops', 'year end influencer strategy', 'Playbook'),
        ('The 10 highest-performing reel formats for beauty brands', 'beauty reel formats', 'List'),
        ('Nano creators: how tier-3 city creators drive real GMV', 'nano influencer india', 'Trend'),
        ('How to write a creator brief the AI can execute', 'creator brief template', 'Template'),
     ]),
    ('Jan 27',
     [
        ('New year new stack: 5 tools every D2C brand should ditch', 'd2c marketing tools', 'Opinion'),
        ('Contract e-sign vs DocuSign for creators — the India angle', 'creator contract india', 'How-to'),
        ('How Roving Mode 10x’d their ER on IG in 90 days', 'roving mode case study', 'Case study'),
        ('The Reels editing prompt every creator should have', 'reels editing prompt', 'Template'),
     ]),
    ('Feb 27',
     [
        ('Valentine’s Day creator kits: the sold-out playbook', 'valentine creator campaign', 'Seasonal'),
        ('Fraud in creator-marketing: how CreatorPlex spots fake followers', 'fake follower detection', 'Trust'),
        ('YouTube long-form vs Reels: what wins for tech brands', 'youtube vs reels tech', 'Comparison'),
        ('The RazorpayX auto-payout flow explained', 'razorpayx creator payout', 'Tech'),
     ]),
    ('Mar 27', 'Q2 planning + IPL creator campaigns  ·  4 posts (topics TBD)'),
    ('Apr 27', 'End of financial year benchmarks  ·  4 posts'),
    ('May 27', 'Summer sale playbook + wedding-season brands  ·  4 posts'),
    ('Jun 27', 'Half-year State of Creator-Commerce update  ·  4 posts'),
    ('Jul 27', 'Monsoon consumer trends + rainwear brand plays  ·  4 posts'),
    ('Aug 27', 'Independence Day + Rakhi seasonal creator kits  ·  4 posts'),
]
for month, items in posts:
    h3(f'{month}')
    if isinstance(items, str):
        para(items, color=INK_SO)
    else:
        for title, kw, kind in items:
            p = doc.add_paragraph(style='List Bullet')
            r = p.add_run(title); r.font.name = 'Inter'; r.font.size = Pt(11); r.font.color.rgb = INK
            r2 = p.add_run(f'  ·  target: “{kw}”  ·  {kind}')
            r2.font.name = 'Inter'; r2.font.size = Pt(9); r2.font.color.rgb = INK_MU; r2.italic = True

h2('Blog post template')
bullets([
    'Title: primary keyword + benefit (60 chars max)',
    'Meta description: 150 chars, ends with soft CTA',
    'H1 = title  ·  H2s follow the "What → Why → How → Proof → CTA" arc',
    'Hero image: gradient (violet→pink→amber) + big number OR real screenshot',
    'Table of contents (auto-anchored) — jump links help SEO + skim readers',
    'Data or quote in every H2 section — cite Dentsu / EY / KPMG / our own data',
    'Every article ends with: 1 soft CTA (newsletter) + 1 hard CTA (sign up / demo)',
    'Related posts strip at bottom → 3 links to sub-topics',
    'FAQ schema block with 5 questions',
])

doc.add_page_break()

# ==============================================================================
# 6. REELS
# ==============================================================================
h1('6.  Reels strategy — organic + creator-side')

h2('Two motions')
para('Motion A: CreatorPlex’s OWN Instagram account (@creatorplex.in). '
     'Motion B: creator-generated Reels that mention or tag CreatorPlex in exchange for the "verified metrics" badge + featured slot.')

h2('Weekly cadence (own account)')
add_table(
    ['Day', 'Format', 'Angle', 'Hook example'],
    [
        ['Mon', 'Trend hijack',       'Piggyback a trending audio + overlay a D2C growth stat',           '“POV: your creator campaign has ZERO attribution.”'],
        ['Wed', 'Case study reel',    'Screen-record a brand’s dashboard → GMV timeline',                 '“How this brand did ₹6L in 14 days using 40 nano creators”'],
        ['Fri', 'Founder POV',        'Founder to camera, 30-45s, 1 insight + 1 receipt',                 '“I spent ₹47L on influencer marketing before I built this.”'],
        ['Sat', 'Creator spotlight',  'Feature a top-performing creator + their campaign result',         '“Riya (45K) got 2.4M views on this ghee reel. Here’s the brief.”'],
        ['Sun', 'Meme / cultural',    'India-specific brand-marketer humour',                            '“When the agency sends you the ROAS report a month late…”'],
    ]
)

h2('The 8 reel formats that always work for CreatorPlex')
numbered([
    'Dashboard screen-record → real GMV numbers scrolling up',
    'Before / after: "Old way vs CreatorPlex way" split-screen',
    'Founder to camera with a bold claim (backed by a source on-screen)',
    'Creator earnings screen: "@riya just got paid ₹8,500 to her UPI in 3 hours"',
    'Meme-style: brand marketer pain-points in D2C context',
    'How-to: 15s tactical tip (e.g. "write briefs the AI can execute")',
    'Live event / office footage — behind the scenes credibility',
    'Data reveal: 1 stat that shocks the D2C audience',
])

h2('Reel production checklist')
bullets([
    'Length: 15-30s (best) · never over 45s',
    'Hook in first 1.5s (question, bold claim, or big number on screen)',
    'Vertical 9:16 · captions burned in (80% of feed is muted)',
    'Brand mark (CP gradient) bottom-right corner — 5% opacity, subtle',
    'CTA in on-screen text at 80% of the video: "Link in bio to try free"',
    'Post at 8:30 PM IST (highest India engagement window)',
    'Repurpose to YouTube Shorts + LinkedIn video + WhatsApp status',
])

h2('Creator-generated reels (motion B)')
para('We give creators the "verified metrics" badge on their public profile in exchange for 1 reel / month tagging @creatorplex.in. '
     'The reel gets featured on the CreatorPlex homepage carousel (already built — autoplays in-place, no redirect). '
     'That mutual visibility drives creator signups + gives us cheap, authentic content.')

callout('Reels north star',
        'By Q4: 4 posts / week on @creatorplex.in · avg 15K reach per reel · 25 creator-generated reels / month tagging us · homepage carousel updated weekly.')

doc.add_page_break()

# ==============================================================================
# 7. PAID + PARTNERSHIPS
# ==============================================================================
h1('7.  Paid + partnerships')

h2('Paid budget — first 90 days')
add_table(
    ['Channel', 'Budget / month', 'What we spend on', 'Target CAC'],
    [
        ['Google Search',    '₹80,000',  '"influencer marketing agency [city]" long-tail + brand terms', '₹2,400'],
        ['Meta (Reels + IG)','₹60,000',  'Retargeting site visitors + lookalike of paying brands',       '₹2,900'],
        ['LinkedIn',         '₹30,000',  'Sponsored posts targeting "Marketing Manager" + "D2C" titles', '₹3,800'],
        ['YouTube Shorts',   '₹20,000',  'Boost top-3 organic Shorts on our channel',                     '₹2,100'],
        ['Total',            '₹1,90,000','',                                                              'avg ₹2,700'],
    ]
)

h2('Agency partner program')
bullets([
    'White-label the CreatorPlex platform under the agency’s brand',
    'They pay ₹25,000 / month platform fee + 20% of any campaign fee they charge their clients',
    'We provide: onboarding, training, priority support, co-marketing case studies',
    'Target: 10 agency partners in first 12 months = 200 brand clients funneled to us',
])

h2('Shopify App Store playbook')
numbered([
    'Ship the app with 15+ 5-star reviews from beta brands (offer free growth-tier for a review)',
    'Screencast the "Ship in a day" flow → featured on app card',
    'Optimize listing for "influencer", "creator", "UGC" search terms',
    'Publish an app-specific case study to the Shopify blog (submit for feature)',
    'Bid on Shopify search ads for competitor apps',
])

h2('Founder-led press / podcasts (first 90 days)')
bullets([
    'YourStory D2C interview',
    'Inc42 - "The unbundling of the creator agency" op-ed',
    'The Ken - long-form profile on Indian creator-commerce',
    'D2C India podcast (Anand Sinha)',
    'Backstage with Millionaires (Nikhil Kamath) — pitch',
    'The Product Folks podcast — India product-craft angle',
    'Storyboard18 — India ad + marketing publication',
])

doc.add_page_break()

# ==============================================================================
# 8. EMAIL + WHATSAPP LIFECYCLE
# ==============================================================================
h1('8.  Email + WhatsApp lifecycle')
para('Every event on CreatorPlex fires a notification via the built-in Whatify BSP + Mail service. '
     '12 template events are seeded. Below is the sequence that drives activation, retention and referrals.')

h2('Brand-side (signup → first campaign)')
add_table(
    ['#', 'Trigger', 'Channel', 'Message'],
    [
        ['1', 'Brand signup (workspace created)',        'Email + WhatsApp', 'Welcome + 90-sec onboarding video'],
        ['2', '+ 24 hr no campaign',                     'WhatsApp',         'One-tap "Import your product catalog"'],
        ['3', 'Campaign draft saved but not launched',   'WhatsApp',         '"Ready to invite? 200 creators are waiting"'],
        ['4', 'First campaign launched',                 'Email',            'Celebration + link to case-study library'],
        ['5', 'First creator application received',      'WhatsApp',         'Push notification with 1-tap approve'],
        ['6', 'First content submitted',                 'Email',            'Preview + approve / request changes in-app'],
        ['7', 'First payout released',                   'Email',            '"You just paid your first creator — here\'s the receipt"'],
        ['8', 'Campaign complete',                       'Email',            'GMV report + "Relaunch top-3 winners" CTA'],
        ['9', 'Monthly digest',                          'Email',            'MoM ROAS + top performing creator + next month\'s trend'],
    ]
)

h2('Creator-side (signup → first payout)')
add_table(
    ['#', 'Trigger', 'Channel', 'Message'],
    [
        ['1', 'Creator signup',                          'Email + WhatsApp', 'Welcome + how to boost profile'],
        ['2', 'Profile incomplete after 24 hr',          'WhatsApp',         '"Add 3 more posts to unlock invites"'],
        ['3', 'Instagram connected',                     'WhatsApp',         '"Your verified metrics badge is live"'],
        ['4', 'First invite received',                   'Push + WhatsApp',  '1-tap accept / decline'],
        ['5', 'Application approved',                    'WhatsApp',         '"You got picked! Kit ships tomorrow"'],
        ['6', 'Order shipped',                           'WhatsApp',         'Tracking link + due date reminder'],
        ['7', 'Content approved',                        'WhatsApp',         '"Payment coming to your UPI in 24 hr"'],
        ['8', 'Payout released',                         'WhatsApp',         'UTR + receipt + "Share your win" template'],
    ]
)

h2('Weekly newsletter (both sides)')
para('Tuesday 10:30 AM IST. 400 words max. Format:')
bullets([
    'Section 1 · One data point (India creator-commerce stat)',
    'Section 2 · One brand case study (real numbers)',
    'Section 3 · One creator spotlight',
    'Section 4 · One product update (new feature shipped)',
    'Section 5 · One resource link (blog / calculator / template)',
])

doc.add_page_break()

# ==============================================================================
# 9. KPIs
# ==============================================================================
h1('9.  KPIs & reporting')

h2('The 5 numbers every founder tracks weekly')
add_table(
    ['Metric', 'Definition', 'Target (Month 3)', 'Target (Month 12)'],
    [
        ['New brand workspaces',     'Workspaces created this week',              '30',       '250'],
        ['Verified creators added',  'IG-Business verified this week',            '75',       '400'],
        ['GMV attributed',           'Cumulative revenue attributed to campaigns','₹1.6 Cr',  '₹18 Cr'],
        ['Paying customers',         'Workspaces on Growth or Scale plan',        '25',       '650'],
        ['NPS (brand + creator)',    'Rolling 30-day survey',                     '55+',      '65+'],
    ]
)

h2('Marketing dashboard (weekly)')
bullets([
    'Organic sessions (GSC + GA4) — split by "programmatic city × service" and "blog"',
    'Top-3 keywords by clicks + top-3 rising keywords',
    'Blog post performance (top 5 by unique pageviews)',
    'Reel reach + saves + shares on @creatorplex.in',
    'Signup conversion by traffic source',
    'CAC by channel  ·  LTV / CAC by channel',
    'Referral program: codes issued · signups · conversions',
])

h2('Reporting rhythm')
bullets([
    'Daily (Slack): signups, GMV attributed, ops incidents',
    'Weekly (Monday 10 AM): 30-min growth review with full team',
    'Monthly (first Friday): board deck update — 5 numbers + narrative',
    'Quarterly (last week of Q): retrospective + next-quarter OKRs',
])

doc.add_page_break()

# ==============================================================================
# 10. BUDGET
# ==============================================================================
h1('10.  Budget allocation (Year 1)')

para('Growth & GTM = 45% of the seed = ~₹1.6 Cr for 12 months.', bold=True)
add_table(
    ['Line item',                          'Year 1 budget (₹)', '% of GTM'],
    [
        ['Paid ads (Google + Meta + LinkedIn)', '22,80,000',  '14%'],
        ['Content team (2 writers + editor)',   '18,00,000',  '11%'],
        ['SEO + link-building agency retainer', '9,60,000',   '6%'],
        ['Video production (reels + case)',     '12,00,000',  '8%'],
        ['Events + sponsorships (D2C summits)', '10,00,000',  '6%'],
        ['Founder-led PR / podcast production', '6,00,000',   '4%'],
        ['Creator community (₹500 × 5,000)',    '25,00,000',  '16%'],
        ['Referral commissions',                '20,00,000',  '13%'],
        ['Case-study production (10 × ₹80K)',   '8,00,000',   '5%'],
        ['Newsletter + email tooling',          '3,60,000',   '2%'],
        ['Analytics + attribution tools',       '4,00,000',   '2%'],
        ['Buffer (10%)',                        '16,00,000',  '10%'],
        ['Total',                               '1,55,00,000', '100%'],
    ]
)

hr()

eyebrow('Closing note')
para('This playbook is a living document. Every 4 weeks we revisit the KPI dashboard, kill what is not compounding, '
     'and double-down on what is. The engine is: publish → distribute → measure → repeat.', italic=True, color=INK_SO)
para('Owner: Growth  ·  Version 1.0  ·  Reviewed monthly.', size=10, color=INK_MU)

out = '/home/user/creatorflow/deliverables/CreatorPlex-GTM-Marketing-SEO-Blogs-Reels.docx'
doc.save(out)
print('wrote', out)
