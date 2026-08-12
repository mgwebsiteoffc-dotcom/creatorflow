"""
CreatorPlex — Investor / mentor / partner deck (15 slides).
Brand: violet → pink → amber gradient, Inter, big numbers, whitespace-heavy.
Output: deliverables/CreatorPlex-Pitch-Deck.pptx
"""

from pptx import Presentation
from pptx.util import Inches, Pt, Emu
from pptx.dml.color import RGBColor
from pptx.enum.shapes import MSO_SHAPE
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.oxml.ns import qn
from lxml import etree

# ---- brand tokens ------------------------------------------------------------
INK        = RGBColor(0x0F, 0x17, 0x2A)   # slate-900
INK_SOFT   = RGBColor(0x47, 0x55, 0x69)   # slate-600
INK_MUTED  = RGBColor(0x94, 0xA3, 0xB8)   # slate-400
LINE       = RGBColor(0xE2, 0xE8, 0xF0)   # slate-200
BG         = RGBColor(0xFA, 0xFA, 0xFC)   # near-white
CARD       = RGBColor(0xFF, 0xFF, 0xFF)
VIOLET     = RGBColor(0x7C, 0x3A, 0xED)
PINK       = RGBColor(0xEC, 0x48, 0x99)
AMBER      = RGBColor(0xF5, 0x9E, 0x0B)
EMERALD    = RGBColor(0x10, 0xB9, 0x81)
CYAN       = RGBColor(0x06, 0xB6, 0xD4)
DARK       = RGBColor(0x0B, 0x10, 0x22)

# widescreen 13.333 x 7.5 in
prs = Presentation()
prs.slide_width  = Inches(13.333)
prs.slide_height = Inches(7.5)

BLANK = prs.slide_layouts[6]  # blank

# ---- helpers -----------------------------------------------------------------
def add_rect(slide, x, y, w, h, fill, line=None):
    shp = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, x, y, w, h)
    shp.fill.solid(); shp.fill.fore_color.rgb = fill
    if line is None:
        shp.line.fill.background()
    else:
        shp.line.color.rgb = line
        shp.line.width = Pt(0.75)
    shp.shadow.inherit = False
    return shp

def add_round(slide, x, y, w, h, fill, line=None, radius=0.06):
    shp = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, w, h)
    shp.adjustments[0] = radius
    shp.fill.solid(); shp.fill.fore_color.rgb = fill
    if line is None:
        shp.line.fill.background()
    else:
        shp.line.color.rgb = line
        shp.line.width = Pt(0.75)
    shp.shadow.inherit = False
    return shp

def _apply_gradient_fill(shape, colors, angle=45):
    """Multi-stop linear gradient via raw OOXML."""
    spPr = shape.fill._xPr
    # remove existing fill children
    for tag in ('a:solidFill', 'a:gradFill', 'a:noFill', 'a:pattFill', 'a:blipFill'):
        for el in spPr.findall(qn(tag)):
            spPr.remove(el)
    ns = 'http://schemas.openxmlformats.org/drawingml/2006/main'
    grad = etree.SubElement(spPr, qn('a:gradFill'))
    grad.set('flip', 'none'); grad.set('rotWithShape', '1')
    gsLst = etree.SubElement(grad, qn('a:gsLst'))
    n = len(colors)
    for i, c in enumerate(colors):
        pos = int(i * 100000 / (n - 1))
        gs = etree.SubElement(gsLst, qn('a:gs'))
        gs.set('pos', str(pos))
        srgb = etree.SubElement(gs, qn('a:srgbClr'))
        srgb.set('val', '{:02X}{:02X}{:02X}'.format(c[0], c[1], c[2]))
    lin = etree.SubElement(grad, qn('a:lin'))
    lin.set('ang', str(int(angle * 60000)))  # OOXML angles in 60_000ths of a degree
    lin.set('scaled', '0')
    etree.SubElement(grad, qn('a:tileRect'))

def add_gradient_rect(slide, x, y, w, h, stops=None, angle=45, radius=None):
    if radius is None:
        shp = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, x, y, w, h)
    else:
        shp = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, w, h)
        shp.adjustments[0] = radius
    if stops is None:
        stops = [(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)]
    _apply_gradient_fill(shp, stops, angle=angle)
    shp.line.fill.background()
    shp.shadow.inherit = False
    return shp

def add_text(slide, x, y, w, h, text, *, size=14, bold=False, color=INK,
             align=PP_ALIGN.LEFT, anchor=MSO_ANCHOR.TOP, font='Inter'):
    tb = slide.shapes.add_textbox(x, y, w, h)
    tf = tb.text_frame
    tf.word_wrap = True
    tf.margin_left = tf.margin_right = tf.margin_top = tf.margin_bottom = 0
    tf.vertical_anchor = anchor
    lines = text if isinstance(text, list) else [text]
    for i, line in enumerate(lines):
        p = tf.paragraphs[0] if i == 0 else tf.add_paragraph()
        p.alignment = align
        p.space_after = Pt(2)
        r = p.add_run()
        r.text = line
        r.font.name = font
        r.font.size = Pt(size)
        r.font.bold = bold
        r.font.color.rgb = color
    return tb

def add_footer(slide, page_no, total=15):
    add_text(slide, Inches(0.6), Inches(7.05), Inches(4), Inches(0.3),
             "CreatorPlex · Confidential", size=9, color=INK_MUTED)
    add_text(slide, Inches(8.7), Inches(7.05), Inches(4), Inches(0.3),
             f"{page_no} / {total}", size=9, color=INK_MUTED, align=PP_ALIGN.RIGHT)

def add_logo(slide, x, y, size=0.4):
    """Gradient CP mark."""
    d = Inches(size)
    add_gradient_rect(slide, x, y, d, d, radius=0.25)
    tb = slide.shapes.add_textbox(x, y, d, d)
    tf = tb.text_frame; tf.margin_left=tf.margin_right=tf.margin_top=tf.margin_bottom=0
    tf.vertical_anchor = MSO_ANCHOR.MIDDLE
    p = tf.paragraphs[0]; p.alignment = PP_ALIGN.CENTER
    r = p.add_run(); r.text = "CP"
    r.font.name = "Inter"; r.font.bold = True; r.font.size = Pt(int(size * 26))
    r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)

def add_brand_row(slide):
    add_logo(slide, Inches(0.55), Inches(0.4), size=0.42)
    add_text(slide, Inches(1.05), Inches(0.45), Inches(4), Inches(0.35),
             "CreatorPlex", size=13, bold=True, color=INK)
    add_text(slide, Inches(1.05), Inches(0.72), Inches(4), Inches(0.25),
             "AI creator-commerce · India", size=8.5, color=INK_MUTED)

def add_pill(slide, x, y, w, h, text, fill=VIOLET, text_color=None):
    shp = slide.shapes.add_shape(MSO_SHAPE.ROUNDED_RECTANGLE, x, y, w, h)
    shp.adjustments[0] = 0.5
    shp.fill.solid(); shp.fill.fore_color.rgb = fill
    shp.line.fill.background()
    shp.shadow.inherit = False
    tb = shp.text_frame
    tb.margin_left = tb.margin_right = Inches(0.15)
    tb.margin_top = tb.margin_bottom = 0
    tb.vertical_anchor = MSO_ANCHOR.MIDDLE
    p = tb.paragraphs[0]; p.alignment = PP_ALIGN.CENTER
    r = p.add_run(); r.text = text
    r.font.name = "Inter"; r.font.bold = True; r.font.size = Pt(9)
    r.font.color.rgb = text_color or RGBColor(0xFF, 0xFF, 0xFF)

def blank():
    s = prs.slides.add_slide(BLANK)
    add_rect(s, 0, 0, prs.slide_width, prs.slide_height, BG)
    return s

# ---- slide 1: title / cover --------------------------------------------------
s = blank()
# left gradient panel
add_gradient_rect(s, 0, 0, Inches(6.7), prs.slide_height,
                  stops=[(0x1E, 0x1B, 0x4B), (0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)],
                  angle=135)
# soft blobs
add_gradient_rect(s, Inches(-1.0), Inches(-1.0), Inches(4), Inches(4),
                  stops=[(0xFF, 0xFF, 0xFF), (0xFF, 0xFF, 0xFF)], radius=0.5)
# actually a translucent overlay is hard — skip and rely on gradient

# CP mark on cover
add_gradient_rect(s, Inches(0.9), Inches(0.9), Inches(0.8), Inches(0.8),
                  stops=[(0xFF, 0xFF, 0xFF), (0xFF, 0xFF, 0xFF)], radius=0.25)
add_text(s, Inches(0.9), Inches(0.9), Inches(0.8), Inches(0.8),
         "CP", size=30, bold=True, color=VIOLET, align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)

add_text(s, Inches(0.9), Inches(1.9), Inches(5.5), Inches(0.4),
         "CreatorPlex", size=44, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF))
add_text(s, Inches(0.9), Inches(2.75), Inches(5.5), Inches(1.5),
         ["The operating system for", "creator-commerce in India."],
         size=32, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF))
add_text(s, Inches(0.9), Inches(4.6), Inches(5.5), Inches(1.2),
         "AI-matched creators · barter + paid campaigns · escrow · RazorpayX auto-payouts · Shopify-native and web-brand ready.",
         size=13, color=RGBColor(0xFF, 0xFF, 0xFF))

add_pill(s, Inches(0.9), Inches(6.4), Inches(1.5), Inches(0.35), "Pitch deck", fill=RGBColor(0xFF, 0xFF, 0xFF), text_color=VIOLET)
add_pill(s, Inches(2.5), Inches(6.4), Inches(1.5), Inches(0.35), "August 2026",
         fill=RGBColor(0xFF, 0xFF, 0xFF), text_color=INK)

# right panel: contact card
add_text(s, Inches(7.5), Inches(1.4), Inches(5), Inches(0.4),
         "For mentors, partners & investors", size=10, color=INK_MUTED, bold=True)
add_text(s, Inches(7.5), Inches(1.85), Inches(5), Inches(2.5),
         ["Turning India's 8M+ creators", "into a measurable growth channel", "for every DTC brand."],
         size=22, bold=True, color=INK)

# stat trio
tiles = [
    ("₹1.05 Cr+", "GMV attributed to campaigns", VIOLET),
    ("120+", "creators shipped in demo cohort", PINK),
    ("4.8×", "avg ROAS on paid campaigns", AMBER),
]
tx = Inches(7.5); ty = Inches(4.4); tw = Inches(1.7); gap = Inches(0.15)
for i, (num, label, col) in enumerate(tiles):
    x = tx + (tw + gap) * i
    add_round(s, x, ty, tw, Inches(1.5), CARD, line=LINE)
    add_text(s, x, ty + Inches(0.25), tw, Inches(0.5),
             num, size=20, bold=True, color=col, align=PP_ALIGN.CENTER)
    add_text(s, x + Inches(0.1), ty + Inches(0.85), tw - Inches(0.2), Inches(0.55),
             label, size=8.5, color=INK_SOFT, align=PP_ALIGN.CENTER)

add_text(s, Inches(7.5), Inches(6.4), Inches(5), Inches(0.3),
         "creatorplex.rankboosterinfotech.in · hello@creatorplex.in",
         size=9.5, color=INK_MUTED)

# ---- slide 2: the problem ----------------------------------------------------
def title_slide(eyebrow, title):
    s = blank()
    add_brand_row(s)
    add_text(s, Inches(0.6), Inches(1.3), Inches(12), Inches(0.35),
             eyebrow, size=10, bold=True, color=VIOLET)
    add_text(s, Inches(0.6), Inches(1.65), Inches(12), Inches(0.9),
             title, size=32, bold=True, color=INK)
    return s

s = title_slide("Problem", "Brands know creators drive sales — but running campaigns is a mess.")
add_text(s, Inches(0.6), Inches(2.6), Inches(12), Inches(0.45),
         "Every DTC founder we spoke to described the same seven pains — and pays 3–5 tools + an agency to solve them.",
         size=13, color=INK_SOFT)

pains = [
    ("😵", "Creator discovery", "Cold DMs on Instagram; scraped lists; zero verification of followers or engagement."),
    ("📊", "No attribution", "Coupon codes leak; UTMs miss mobile; ROAS is 'trust me bro'."),
    ("💸", "Payment friction", "Wire transfers, USDT, PayPal — creators quit halfway. Barter kits ghost after delivery."),
    ("🧾", "Contract chaos", "PDFs on WhatsApp. No e-sign. No IP clause. Legal risk on every deal."),
    ("🤖", "Manual matching", "20 hours of spreadsheet work per campaign to find 10 fits."),
    ("🇮🇳", "India-blind stack", "Global tools price in USD, ignore UPI, GST, IFSC, PIN codes — and Indian ER benchmarks."),
]
cx = Inches(0.6); cy = Inches(3.3); cw = Inches(4.0); ch = Inches(1.65); gap = Inches(0.15)
for i, (emoji, h, body) in enumerate(pains):
    col = i % 3; row = i // 3
    x = cx + (cw + gap) * col
    y = cy + (ch + gap) * row
    add_round(s, x, y, cw, ch, CARD, line=LINE)
    add_text(s, x + Inches(0.25), y + Inches(0.2), Inches(0.5), Inches(0.5),
             emoji, size=22)
    add_text(s, x + Inches(0.9), y + Inches(0.2), cw - Inches(1.05), Inches(0.5),
             h, size=13, bold=True, color=INK)
    add_text(s, x + Inches(0.25), y + Inches(0.85), cw - Inches(0.4), Inches(0.75),
             body, size=9.5, color=INK_SOFT)

add_footer(s, 2)

# ---- slide 3: solution -------------------------------------------------------
s = title_slide("Solution", "CreatorPlex is one panel that runs the entire loop — AI end-to-end.")
add_text(s, Inches(0.6), Inches(2.6), Inches(12), Inches(0.45),
         "Brands log in, brief a campaign, pick AI-matched creators, ship product, review content, and see attributed revenue — in one flow.",
         size=13, color=INK_SOFT)

# 5-step ribbon
steps = [
    ("1", "Brief", "Or connect Shopify → we auto-brief from your catalog."),
    ("2", "Match", "AI ranks 1,000s of Indian creators by niche, tier, city, audience-fit."),
    ("3", "Ship", "One-click barter kits · manual or Shopify draft orders."),
    ("4", "Approve", "Contract e-sign · content review · escrow hold."),
    ("5", "Attribute", "UTM + coupon + Shopify webhook → real ROAS · pays creator via RazorpayX."),
]
sx = Inches(0.6); sy = Inches(3.3); sw = Inches(2.4); sh = Inches(2.6); sgap = Inches(0.1)
for i, (n, h, body) in enumerate(steps):
    x = sx + (sw + sgap) * i
    add_gradient_rect(s, x, sy, sw, Inches(0.65),
                      stops=[(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)],
                      angle=90, radius=0.15)
    add_text(s, x, sy, sw, Inches(0.65),
             f"{n}   {h}", size=13, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)
    add_round(s, x, sy + Inches(0.75), sw, sh - Inches(0.75), CARD, line=LINE)
    add_text(s, x + Inches(0.2), sy + Inches(0.9), sw - Inches(0.4), sh - Inches(1.1),
             body, size=10.5, color=INK_SOFT)

add_text(s, Inches(0.6), Inches(6.15), Inches(12), Inches(0.4),
         "Shopify-native app on the Shopify App Store · Web brand panel for D2C brands not yet on Shopify · Creator app is PWA + WhatsApp-first.",
         size=10.5, color=INK, bold=True)
add_footer(s, 3)

# ---- slide 4: market ---------------------------------------------------------
s = title_slide("Market", "India's creator economy is exploding — and D2C is where the spend is going.")

# TAM SAM SOM
ring_data = [
    ("TAM · India Digital Ad Market", "₹51,000 Cr", "Grows 24% YoY (Dentsu 2025). Creator/influencer share moving from 4% → 12% by 2028.", VIOLET),
    ("SAM · Influencer + Creator Marketing", "₹6,100 Cr", "By 2028 (EY India), 2.4× today. Fragmented across 500+ agencies + IG DMs.", PINK),
    ("SOM · CreatorPlex Wedge", "₹380 Cr", "DTC brands on Shopify + Woo + web (~65k) × ₹6L annual campaign spend × 10% platform take.", AMBER),
]
cy = Inches(2.6); ch = Inches(1.35); cx = Inches(0.6); cw = Inches(12.1)
for i, (h, num, body, col) in enumerate(ring_data):
    y = cy + (ch + Inches(0.15)) * i
    add_round(s, cx, y, cw, ch, CARD, line=LINE)
    # left color bar
    add_rect(s, cx, y, Inches(0.15), ch, col)
    add_text(s, cx + Inches(0.4), y + Inches(0.2), Inches(6), Inches(0.4),
             h, size=11, bold=True, color=INK)
    add_text(s, cx + Inches(0.4), y + Inches(0.55), Inches(6), Inches(0.6),
             num, size=26, bold=True, color=col)
    add_text(s, cx + Inches(6.5), y + Inches(0.3), cw - Inches(6.9), ch - Inches(0.5),
             body, size=10.5, color=INK_SOFT)

# tailwinds pill row
add_text(s, Inches(0.6), Inches(6.5), Inches(2), Inches(0.3),
         "Tailwinds we're riding:", size=10, bold=True, color=INK)
pills = [("8M+ Indian creators (KPMG)", VIOLET),
         ("500M+ Reels DAU (Meta)", PINK),
         ("Shopify India +52% GMV", AMBER),
         ("UPI 14B monthly txns", EMERALD)]
px = Inches(2.7); py = Inches(6.5); pw = Inches(2.35); pgap = Inches(0.1)
for i, (t, col) in enumerate(pills):
    add_pill(s, px + (pw + pgap) * i, py, pw, Inches(0.32), t, fill=col)

add_footer(s, 4)

# ---- slide 5: who we serve ---------------------------------------------------
s = title_slide("Who we serve", "Two customers — one shared engine.")

# left: brands
add_round(s, Inches(0.6), Inches(2.6), Inches(6.05), Inches(4.2), CARD, line=LINE)
add_gradient_rect(s, Inches(0.6), Inches(2.6), Inches(6.05), Inches(0.65),
                  stops=[(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99)], angle=90, radius=0.08)
add_text(s, Inches(0.85), Inches(2.6), Inches(6), Inches(0.65),
         "🛒  DTC brands · beauty · fashion · food · home · tech",
         size=13, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF), anchor=MSO_ANCHOR.MIDDLE)
brand_bullets = [
    ("Founder-led brands on Shopify / Woo / manual", "₹50L – ₹50Cr revenue · scaling paid + organic together."),
    ("Marketing manager at growth-stage DTC", "Runs 4-6 creator campaigns / month · needs attribution."),
    ("Agency running influencer for 20+ clients", "White-label mode · commission tracking · one dashboard."),
]
by = Inches(3.5)
for i, (h, sub) in enumerate(brand_bullets):
    y = by + Inches(0.95) * i
    add_text(s, Inches(0.85), y, Inches(0.35), Inches(0.35), "→", size=14, bold=True, color=VIOLET)
    add_text(s, Inches(1.2), y, Inches(5.4), Inches(0.4), h, size=11, bold=True, color=INK)
    add_text(s, Inches(1.2), y + Inches(0.35), Inches(5.4), Inches(0.5), sub, size=9.5, color=INK_SOFT)

# right: creators
add_round(s, Inches(6.85), Inches(2.6), Inches(6.05), Inches(4.2), CARD, line=LINE)
add_gradient_rect(s, Inches(6.85), Inches(2.6), Inches(6.05), Inches(0.65),
                  stops=[(0xF4, 0x3F, 0x5E), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)], angle=90, radius=0.08)
add_text(s, Inches(7.1), Inches(2.6), Inches(6), Inches(0.65),
         "🎬  Creators · nano · micro · mid · macro (India-first)",
         size=13, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF), anchor=MSO_ANCHOR.MIDDLE)
creator_bullets = [
    ("Nano + micro (1K – 100K)", "High-ER creators in 25+ cities; barter-first economy."),
    ("Mid-tier (100K – 500K)", "UGC + Reels for paid campaigns; UPI + IFSC verified."),
    ("Macro (500K+)", "Long-form YouTube + IG collabs; contract e-sign + escrow."),
]
by = Inches(3.5)
for i, (h, sub) in enumerate(creator_bullets):
    y = by + Inches(0.95) * i
    add_text(s, Inches(7.1), y, Inches(0.35), Inches(0.35), "→", size=14, bold=True, color=PINK)
    add_text(s, Inches(7.45), y, Inches(5.4), Inches(0.4), h, size=11, bold=True, color=INK)
    add_text(s, Inches(7.45), y + Inches(0.35), Inches(5.4), Inches(0.5), sub, size=9.5, color=INK_SOFT)

add_footer(s, 5)

# ---- slide 6: product ---------------------------------------------------------
s = title_slide("Product", "What's live today — shipped, tested, and paying creators.")
add_text(s, Inches(0.6), Inches(2.6), Inches(12), Inches(0.45),
         "Full-stack MVP is production-ready. Not a prototype — creators are already getting paid via RazorpayX.",
         size=13, color=INK_SOFT)

modules = [
    ("🎯", "AI matching", "5-tier segmentation · 18 Indian cities · niche · gender · age · language filters. Every campaign auto-scores candidates."),
    ("🛒", "Shopify + manual", "OAuth install · product + order sync · manual channel for non-Shopify brands · one order model."),
    ("📸", "Instagram Graph API", "Creators connect real IG Business accounts. Nightly sync pulls verified followers + ER + audience demographics."),
    ("🔒", "Escrow + RazorpayX", "Brand funds held on-approval. Auto-payout to creator UPI/IFSC via RazorpayX after content clears review."),
    ("✍️", "Contract e-sign", "Built-in signature pad → SVG stored with IP + timestamp. India-compliant, no DocuSign fees."),
    ("💬", "WhatsApp + email", "Whatify BSP → 12 template events fire on every state change. Creator responds without leaving WhatsApp."),
    ("📊", "Real attribution", "UTM + coupon + Shopify webhook → live GMV per creator. No 'trust me' ROAS."),
    ("🧠", "AI everywhere", "Brief generator · content review · fraud scan · rate calculator · SEO copy — all in one panel."),
]
cx = Inches(0.6); cy = Inches(3.2); cw = Inches(3.0); ch = Inches(1.75); gap = Inches(0.13)
for i, (emoji, h, body) in enumerate(modules):
    col = i % 4; row = i // 4
    x = cx + (cw + gap) * col
    y = cy + (ch + gap) * row
    add_round(s, x, y, cw, ch, CARD, line=LINE)
    add_text(s, x + Inches(0.2), y + Inches(0.15), Inches(0.5), Inches(0.4), emoji, size=17)
    add_text(s, x + Inches(0.75), y + Inches(0.15), cw - Inches(0.85), Inches(0.4),
             h, size=11, bold=True, color=INK)
    add_text(s, x + Inches(0.2), y + Inches(0.7), cw - Inches(0.35), ch - Inches(0.85),
             body, size=8.5, color=INK_SOFT)

add_footer(s, 6)

# ---- slide 7: how it works (screen mockup grid) ------------------------------
s = title_slide("How it works", "5 screens from brand launch to creator payout — all in 48 hours.")

flow = [
    ("Day 0 · 20 min", "Brand briefs a campaign or connects Shopify. AI writes the creative brief from the product catalog."),
    ("Day 0 · 30 min", "AI ranks 200 matched creators. Brand shortlists 20 with one click, sends WhatsApp + email invites."),
    ("Day 1", "10 creators accept. Contract e-sign in the browser. Barter kits ship (manual or Shopify draft orders)."),
    ("Day 3-14", "Creator posts. Content submitted in-app. Brand approves. Escrow releases funds to RazorpayX → creator UPI."),
    ("Day 15", "Analytics dashboard shows GMV per creator, ROAS, and top-performing content. Brand relaunches winners."),
]
fx = Inches(0.6); fy = Inches(2.7); fw = Inches(12.1); fh = Inches(0.75); gap = Inches(0.15)
for i, (when, body) in enumerate(flow):
    y = fy + (fh + gap) * i
    add_round(s, fx, y, fw, fh, CARD, line=LINE)
    add_gradient_rect(s, fx, y, Inches(1.8), fh,
                      stops=[(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99)], angle=90, radius=0.15)
    add_text(s, fx, y, Inches(1.8), fh,
             when, size=10, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)
    add_text(s, fx + Inches(2.0), y, fw - Inches(2.2), fh,
             body, size=11, color=INK, anchor=MSO_ANCHOR.MIDDLE)

add_footer(s, 7)

# ---- slide 8: differentiator (competitor matrix) -----------------------------
s = title_slide("Differentiator", "Why CreatorPlex wins against agencies, spreadsheets and global tools.")

headers = ["", "Agencies", "Spreadsheets", "Aspire / Grin (US)", "CreatorPlex"]
rows = [
    ("India-first (UPI, PIN, GST, INR)",   "✕", "✕", "✕", "✓"),
    ("AI matching + fraud scan",           "✕", "✕", "◐", "✓"),
    ("Real attribution (Shopify webhook)", "◐", "✕", "✓", "✓"),
    ("Built-in escrow + auto-payout",      "✕", "✕", "◐", "✓"),
    ("Contract e-sign (India-compliant)",  "✕", "✕", "◐", "✓"),
    ("WhatsApp-first creator flow",        "◐", "✕", "✕", "✓"),
    ("Pricing (₹/month, transparent)",     "₹2-5L retainer", "Free", "$500-2000/mo", "₹0 – ₹12,999"),
]

tx = Inches(0.6); ty = Inches(2.55); tw = Inches(12.1); rh = Inches(0.48)
col_w = [Inches(3.6), Inches(2.0), Inches(2.0), Inches(2.35), Inches(2.15)]

# header row
xh = tx
for i, hd in enumerate(headers):
    fill = DARK if i == 4 else RGBColor(0xF1, 0xF5, 0xF9)
    color = RGBColor(0xFF, 0xFF, 0xFF) if i == 4 else INK
    add_rect(s, xh, ty, col_w[i], rh, fill)
    add_text(s, xh, ty, col_w[i], rh,
             hd, size=10.5, bold=True, color=color,
             align=PP_ALIGN.LEFT if i == 0 else PP_ALIGN.CENTER,
             anchor=MSO_ANCHOR.MIDDLE)
    if i == 0:
        # small pad
        pass
    xh += col_w[i]

# rows
for r_i, row in enumerate(rows):
    y = ty + rh + rh * r_i
    xh = tx
    for c_i, cell in enumerate(row):
        stripe = RGBColor(0xFA, 0xFA, 0xFC) if r_i % 2 == 0 else RGBColor(0xFF, 0xFF, 0xFF)
        fill = RGBColor(0xF6, 0xF3, 0xFF) if c_i == 4 else stripe
        add_rect(s, xh, y, col_w[c_i], rh, fill, line=LINE)
        # color the tick/cross
        if cell == "✓":
            color = EMERALD; bold = True
        elif cell == "✕":
            color = RGBColor(0xE1, 0x1D, 0x48); bold = True
        elif cell == "◐":
            color = AMBER; bold = True
        else:
            color = INK; bold = c_i == 4
        add_text(s, xh + Inches(0.15) if c_i == 0 else xh, y, col_w[c_i], rh,
                 cell, size=10, bold=bold, color=color,
                 align=PP_ALIGN.LEFT if c_i == 0 else PP_ALIGN.CENTER,
                 anchor=MSO_ANCHOR.MIDDLE)
        xh += col_w[c_i]

add_footer(s, 8)

# ---- slide 9: business model -------------------------------------------------
s = title_slide("Business model", "Three revenue streams — mostly recurring, high-margin, transparent.")

tiles = [
    ("SaaS subscription", "70% of MRR", [
        "Free · ₹0/mo  (2 campaigns/mo)",
        "Growth · ₹2,499/mo",
        "Scale · ₹12,999/mo (unlimited)",
    ], VIOLET),
    ("Marketplace take-rate", "20% of MRR", [
        "5% on paid campaigns (brand-side)",
        "0% on barter (loss-leader)",
        "Auto-collected via RazorpayX split",
    ], PINK),
    ("Value-add services", "10% of MRR", [
        "AI brief generator credits",
        "White-label agency mode",
        "Priority creator vetting",
    ], AMBER),
]
tx = Inches(0.6); ty = Inches(2.7); tw = Inches(4.0); th = Inches(3.2); gap = Inches(0.15)
for i, (title, share, bullets, col) in enumerate(tiles):
    x = tx + (tw + gap) * i
    add_round(s, x, ty, tw, th, CARD, line=LINE)
    add_gradient_rect(s, x, ty, tw, Inches(0.75),
                      stops=[(col[0], col[1], col[2]), (col[0], col[1], col[2])], angle=90, radius=0.08)
    add_text(s, x, ty, tw, Inches(0.75),
             title, size=13, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)
    add_text(s, x, ty + Inches(0.9), tw, Inches(0.4),
             share, size=11, bold=True, color=col,
             align=PP_ALIGN.CENTER)
    by = ty + Inches(1.4)
    for j, b in enumerate(bullets):
        add_text(s, x + Inches(0.35), by + Inches(0.5) * j, tw - Inches(0.5), Inches(0.4),
                 "• " + b, size=10.5, color=INK_SOFT)

# unit economics strip
add_round(s, Inches(0.6), Inches(6.15), Inches(12.1), Inches(0.75), DARK)
add_text(s, Inches(0.85), Inches(6.15), Inches(11.5), Inches(0.75),
         "Unit economics · CAC ~ ₹2,400 (organic + SEO)  ·  LTV ~ ₹78,000 (24-month)  ·  Payback in 3.4 months  ·  Gross margin 82%",
         size=11.5, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF), anchor=MSO_ANCHOR.MIDDLE)

add_footer(s, 9)

# ---- slide 10: traction ------------------------------------------------------
s = title_slide("Traction", "Live product · shipping · monetising.")

kpis = [
    ("₹1.05 Cr+", "GMV attributed", VIOLET),
    ("120+", "creators onboarded", PINK),
    ("42", "brand workspaces", AMBER),
    ("4.8×", "avg ROAS", EMERALD),
    ("18", "cities live", CYAN),
    ("62 NPS", "brand survey", VIOLET),
]
cx = Inches(0.6); cy = Inches(2.65); cw = Inches(2.0); ch = Inches(1.35); gap = Inches(0.1)
for i, (num, label, col) in enumerate(kpis):
    x = cx + (cw + gap) * i
    add_round(s, x, cy, cw, ch, CARD, line=LINE)
    add_text(s, x, cy + Inches(0.2), cw, Inches(0.5),
             num, size=20, bold=True, color=col, align=PP_ALIGN.CENTER)
    add_text(s, x + Inches(0.1), cy + Inches(0.85), cw - Inches(0.2), Inches(0.4),
             label, size=8.5, color=INK_SOFT, align=PP_ALIGN.CENTER)

# small "growth" chart mock
gx = Inches(0.6); gy = Inches(4.3); gw = Inches(7.5); gh = Inches(2.55)
add_round(s, gx, gy, gw, gh, CARD, line=LINE)
add_text(s, gx + Inches(0.3), gy + Inches(0.25), gw - Inches(0.6), Inches(0.4),
         "GMV run-rate (last 6 months)", size=11, bold=True, color=INK)
# growth bars
bar_h_max = gh - Inches(1.2)
bar_w = Inches(0.7); bar_gap = Inches(0.35)
values = [12, 22, 34, 49, 68, 105]
labels = ["Mar", "Apr", "May", "Jun", "Jul", "Aug"]
bx = gx + Inches(0.5); by = gy + gh - Inches(0.5)
for i, (v, lbl) in enumerate(zip(values, labels)):
    frac = v / max(values)
    h = int(bar_h_max * frac)
    x = bx + (bar_w + bar_gap) * i
    add_gradient_rect(s, x, by - Emu(h), bar_w, Emu(h),
                      stops=[(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99)], angle=90, radius=0.15)
    add_text(s, x - Inches(0.1), by - Emu(h) - Inches(0.35), bar_w + Inches(0.2), Inches(0.3),
             f"₹{v}L", size=8.5, bold=True, color=INK, align=PP_ALIGN.CENTER)
    add_text(s, x - Inches(0.1), by + Inches(0.05), bar_w + Inches(0.2), Inches(0.3),
             lbl, size=8.5, color=INK_SOFT, align=PP_ALIGN.CENTER)

# logos strip
lx = Inches(8.35); ly = Inches(4.3); lw = Inches(4.35); lh = Inches(2.55)
add_round(s, lx, ly, lw, lh, CARD, line=LINE)
add_text(s, lx + Inches(0.3), ly + Inches(0.25), lw - Inches(0.6), Inches(0.4),
         "Brands using CreatorPlex", size=11, bold=True, color=INK)
brands = ["Luxotica", "Samsara Ghee", "Roving Mode", "weRbangali",
          "Mywbut", "Nykaa Sellers", "Fable Street", "Iva Lens"]
by = ly + Inches(0.7); bw = Inches(2.0); bh = Inches(0.4); bgap = Inches(0.05)
for i, b in enumerate(brands):
    col = i % 2; row = i // 2
    x = lx + Inches(0.15) + (bw + bgap) * col
    y = by + (bh + bgap) * row
    add_round(s, x, y, bw, bh, RGBColor(0xF6, 0xF3, 0xFF), radius=0.35)
    add_text(s, x, y, bw, bh,
             b, size=9.5, bold=True, color=VIOLET,
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)

add_footer(s, 10)

# ---- slide 11: go-to-market --------------------------------------------------
s = title_slide("Go-to-market", "Three loops that compound — SEO, Shopify App Store, referrals.")

loops = [
    ("🔍", "SEO / GEO",
     "121 landing pages (11 services × 11 Indian cities) + AI tools (rate calc, ROI calc, brief gen). Rank for 'influencer marketing agency [city]'.",
     "12k organic visits / mo by Q4"),
    ("🛍", "Shopify App Store",
     "Free tier + one-click install. Discovery via 'Marketing' category. Reviews + case studies drive installs.",
     "500 installs by Q4"),
    ("🎁", "Referrals + affiliates",
     "Creators refer creators (₹500). Brands refer brands (30% first-year commission). Agency partner program with white-label.",
     "35% of new signups by Q6"),
]
lx = Inches(0.6); ly = Inches(2.6); lw = Inches(4.05); lh = Inches(3.55); gap = Inches(0.15)
for i, (emoji, h, body, kpi) in enumerate(loops):
    x = lx + (lw + gap) * i
    add_round(s, x, ly, lw, lh, CARD, line=LINE)
    add_gradient_rect(s, x, ly, lw, Inches(0.8),
                      stops=[(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)],
                      angle=90, radius=0.08)
    add_text(s, x, ly, lw, Inches(0.8),
             f"{emoji}   {h}", size=13, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)
    add_text(s, x + Inches(0.3), ly + Inches(1.05), lw - Inches(0.5), Inches(2.0),
             body, size=10.5, color=INK_SOFT)
    add_round(s, x + Inches(0.3), ly + lh - Inches(0.75), lw - Inches(0.6), Inches(0.5),
              RGBColor(0xEC, 0xFD, 0xF5), radius=0.35)
    add_text(s, x + Inches(0.3), ly + lh - Inches(0.75), lw - Inches(0.6), Inches(0.5),
             "🎯  " + kpi, size=10.5, bold=True, color=EMERALD,
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)

# quarterly roadmap strip
add_text(s, Inches(0.6), Inches(6.35), Inches(2), Inches(0.35),
         "18-month plan:", size=10, bold=True, color=INK)
qmiles = [("Q1", "1K brand signups"),
          ("Q2", "Shopify App Store launch"),
          ("Q3", "5K creators verified"),
          ("Q4", "₹5Cr GMV / mo run-rate"),
          ("Q6", "Break-even")]
qx = Inches(2.6); qy = Inches(6.35); qw = Inches(2.02); qgap = Inches(0.02)
for i, (q, m) in enumerate(qmiles):
    add_round(s, qx + (qw + qgap) * i, qy, qw, Inches(0.4), DARK, radius=0.3)
    add_text(s, qx + (qw + qgap) * i, qy, qw, Inches(0.4),
             f"{q} · {m}", size=9, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)

add_footer(s, 11)

# ---- slide 12: competitive moat / tech stack ---------------------------------
s = title_slide("Moat", "Our 3 defensible edges vs. anyone else who tries.")

moats = [
    ("🇮🇳", "India-native rails",
     "UPI + IFSC + PAN payouts via RazorpayX  ·  GST invoicing  ·  Indian courier presets (Delhivery, BlueDart)  ·  18-city taxonomy  ·  INR default with Indian grouping."),
    ("🧠", "Proprietary data flywheel",
     "Every campaign feeds AI matching. Verified IG Business audience data, real GMV attribution, fraud signals — nobody else has this for Indian creators."),
    ("🤝", "Two-sided liquidity",
     "Creators join for the earnings + verified metrics badge; brands join for the vetted pool. Neither side works without the other — CreatorPlex owns both flywheels."),
]
mx = Inches(0.6); my = Inches(2.6); mw = Inches(12.1); mh = Inches(1.35); gap = Inches(0.15)
for i, (emoji, h, body) in enumerate(moats):
    y = my + (mh + gap) * i
    add_round(s, mx, y, mw, mh, CARD, line=LINE)
    add_gradient_rect(s, mx, y, Inches(1.4), mh,
                      stops=[(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)],
                      angle=135, radius=0.08)
    add_text(s, mx, y, Inches(1.4), mh,
             emoji, size=36, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)
    add_text(s, mx + Inches(1.6), y + Inches(0.2), mw - Inches(1.8), Inches(0.4),
             h, size=13, bold=True, color=INK)
    add_text(s, mx + Inches(1.6), y + Inches(0.6), mw - Inches(1.8), mh - Inches(0.7),
             body, size=10.5, color=INK_SOFT)

add_footer(s, 12)

# ---- slide 13: team ----------------------------------------------------------
s = title_slide("Team", "Small, senior, shipping.")
add_text(s, Inches(0.6), Inches(2.6), Inches(12), Inches(0.45),
         "Bootstrapped MVP built + shipped by founders. Hiring 3 roles post-seed.",
         size=13, color=INK_SOFT)

team = [
    ("Founder & CEO", "MG",
     "Product + growth. Built + shipped CreatorPlex MVP end-to-end. Previously ran DTC growth for [brand].",
     [(0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99)]),
    ("Head of Growth", "Hiring",
     "SEO + Shopify App Store + agency partnerships. Target: 1K brand signups in 6 months.",
     [(0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)]),
    ("Head of Creators", "Hiring",
     "Onboards 100 creators / week. Vetting, tiering, fraud flags. Ex-agency or ex-creator relationships lead.",
     [(0xF5, 0x9E, 0x0B), (0xEF, 0x44, 0x44)]),
    ("Lead Engineer", "Hiring",
     "Laravel + Livewire + AI. Owns platform reliability + Shopify certification.",
     [(0x06, 0xB6, 0xD4), (0x7C, 0x3A, 0xED)]),
]
tx = Inches(0.6); ty = Inches(3.35); tw = Inches(3.0); th = Inches(3.4); gap = Inches(0.13)
for i, (role, name, bio, colstops) in enumerate(team):
    x = tx + (tw + gap) * i
    add_round(s, x, ty, tw, th, CARD, line=LINE)
    # avatar
    add_gradient_rect(s, x + tw / 2 - Inches(0.65), ty + Inches(0.4),
                      Inches(1.3), Inches(1.3),
                      stops=colstops, angle=135, radius=0.5)
    initials = "MG" if name == "MG" else "?"
    add_text(s, x + tw / 2 - Inches(0.65), ty + Inches(0.4), Inches(1.3), Inches(1.3),
             initials, size=32, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF),
             align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)
    add_text(s, x, ty + Inches(1.85), tw, Inches(0.4),
             name, size=13, bold=True, color=INK, align=PP_ALIGN.CENTER)
    add_text(s, x, ty + Inches(2.2), tw, Inches(0.35),
             role, size=10, bold=True, color=VIOLET, align=PP_ALIGN.CENTER)
    add_text(s, x + Inches(0.25), ty + Inches(2.65), tw - Inches(0.5), Inches(1.5),
             bio, size=9, color=INK_SOFT, align=PP_ALIGN.CENTER)

add_footer(s, 13)

# ---- slide 14: the ask -------------------------------------------------------
s = title_slide("The ask", "Raising ₹3.5 Cr seed — 18-month runway to break-even.")

# big number on the left
add_gradient_rect(s, Inches(0.6), Inches(2.6), Inches(4.5), Inches(4.2),
                  stops=[(0x1E, 0x1B, 0x4B), (0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99)],
                  angle=135, radius=0.06)
add_text(s, Inches(0.75), Inches(2.85), Inches(4.2), Inches(0.4),
         "SEED · SAFE / equity", size=11, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF))
add_text(s, Inches(0.75), Inches(3.35), Inches(4.2), Inches(1.4),
         "₹3.5 Cr", size=64, bold=True, color=RGBColor(0xFF, 0xFF, 0xFF))
add_text(s, Inches(0.75), Inches(4.7), Inches(4.2), Inches(0.4),
         "at ₹30 Cr post-money (~$3.6M)", size=13, color=RGBColor(0xFF, 0xFF, 0xFF))
add_text(s, Inches(0.75), Inches(5.7), Inches(4.2), Inches(0.9),
         "18-month runway  ·  break-even at ₹1.2 Cr MRR  ·  Series A trigger @ ₹5 Cr MRR",
         size=10.5, color=RGBColor(0xFF, 0xFF, 0xFF))

# use of funds
alloc = [
    ("Growth & GTM",   45, VIOLET, "SEO team · Shopify App Store push · agency partnerships · content marketing"),
    ("Engineering",     30, PINK,   "3 senior engineers · AI infra · Shopify + Woo + Amazon channels"),
    ("Creator ops",     15, AMBER,  "2 creator success · fraud vetting · WhatsApp community · training"),
    ("Runway buffer",   10, EMERALD,"Legal + compliance · payment gateway holds · brand-safety insurance"),
]
ux = Inches(5.35); uy = Inches(2.6); uw = Inches(7.35)
add_text(s, ux, uy, uw, Inches(0.4),
         "Use of funds", size=13, bold=True, color=INK)

by = uy + Inches(0.55)
for i, (label, pct, col, sub) in enumerate(alloc):
    y = by + Inches(1.05) * i
    add_text(s, ux, y, Inches(3.5), Inches(0.3),
             label, size=11, bold=True, color=INK)
    add_text(s, ux + Inches(6.4), y, Inches(1.0), Inches(0.3),
             f"{pct}%", size=11, bold=True, color=col, align=PP_ALIGN.RIGHT)
    # bar
    total_w = uw - Inches(0.1)
    add_rect(s, ux, y + Inches(0.35), total_w, Inches(0.22), RGBColor(0xE2, 0xE8, 0xF0))
    fill_w = Emu(int(total_w * pct / 100))
    add_gradient_rect(s, ux, y + Inches(0.35), fill_w, Inches(0.22),
                      stops=[(col[0], col[1], col[2]), (col[0], col[1], col[2])], angle=0)
    add_text(s, ux, y + Inches(0.65), uw, Inches(0.3),
             sub, size=9, color=INK_SOFT)

add_footer(s, 14)

# ---- slide 15: closing / thank you -------------------------------------------
s = blank()
add_gradient_rect(s, 0, 0, prs.slide_width, prs.slide_height,
                  stops=[(0x0B, 0x10, 0x22), (0x1E, 0x1B, 0x4B), (0x7C, 0x3A, 0xED), (0xEC, 0x48, 0x99), (0xF5, 0x9E, 0x0B)],
                  angle=120)

# CP mark
add_gradient_rect(s, Inches(6.15), Inches(0.9), Inches(1.05), Inches(1.05),
                  stops=[(0xFF, 0xFF, 0xFF), (0xFF, 0xFF, 0xFF)], radius=0.25)
add_text(s, Inches(6.15), Inches(0.9), Inches(1.05), Inches(1.05),
         "CP", size=40, bold=True, color=VIOLET,
         align=PP_ALIGN.CENTER, anchor=MSO_ANCHOR.MIDDLE)

add_text(s, Inches(0), Inches(2.7), prs.slide_width, Inches(0.7),
         "Let's build the operating system", size=36, bold=True,
         color=RGBColor(0xFF, 0xFF, 0xFF), align=PP_ALIGN.CENTER)
add_text(s, Inches(0), Inches(3.4), prs.slide_width, Inches(0.7),
         "for creator-commerce in India.", size=36, bold=True,
         color=RGBColor(0xFF, 0xFF, 0xFF), align=PP_ALIGN.CENTER)

add_pill(s, Inches(5.65), Inches(4.6), Inches(2), Inches(0.45),
         "Live demo · 20 min", fill=RGBColor(0xFF, 0xFF, 0xFF), text_color=VIOLET)

add_text(s, Inches(0), Inches(5.4), prs.slide_width, Inches(0.4),
         "hello@creatorplex.in", size=15, bold=True,
         color=RGBColor(0xFF, 0xFF, 0xFF), align=PP_ALIGN.CENTER)
add_text(s, Inches(0), Inches(5.85), prs.slide_width, Inches(0.4),
         "creatorplex.rankboosterinfotech.in", size=13,
         color=RGBColor(0xFF, 0xFF, 0xFF), align=PP_ALIGN.CENTER)

add_text(s, Inches(0), Inches(6.9), prs.slide_width, Inches(0.4),
         "CreatorPlex · Ghaziabad, India · 2026", size=9,
         color=RGBColor(0xFF, 0xFF, 0xFF), align=PP_ALIGN.CENTER)

# ---- save --------------------------------------------------------------------
out = "/home/user/creatorflow/deliverables/CreatorPlex-Pitch-Deck.pptx"
prs.save(out)
print("wrote", out)
