<x-layouts.admin title="Integrations">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Superadmin</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">🔌 Integrations</h1>
            <p class="mt-1 text-sm text-slate-500">Mail delivery, Razorpay payments, analytics scripts + verification tags — and roadmap feature flags. Secrets are encrypted at rest.</p>
        </div>
    </div>

    @if($schemaMissing || $columnsMissing)
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">Migration required</p><p class="mt-1 text-xs">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code>. Nothing on this page can be saved until you do.</p></div>
        </div>
    @endif

    {{-- Tab nav (anchor jump) --}}
    <div class="mt-6 flex flex-wrap gap-2 text-sm">
        <a href="#mail"      class="rounded-full bg-white px-4 py-2 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:ring-violet-300">📧 Mail</a>
        <a href="#payments"  class="rounded-full bg-white px-4 py-2 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:ring-violet-300">💳 Payments</a>
        <a href="#analytics" class="rounded-full bg-white px-4 py-2 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:ring-violet-300">📊 Analytics &amp; SEO</a>
        <a href="#push"      class="rounded-full bg-white px-4 py-2 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:ring-violet-300">🔔 Push (VAPID)</a>
        <a href="#features"  class="rounded-full bg-white px-4 py-2 font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200 hover:ring-violet-300">🚦 Feature flags</a>
    </div>

    {{-- ═════════════════════════ MAIL ═════════════════════════ --}}
    <section id="mail" class="mt-8 scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900">📧 Transactional email</h2>
                <p class="mt-1 text-xs text-slate-500">Powers creator invitations, brand alerts, order-shipped emails, weekly digests.</p>
            </div>
            @if($settings->mail_last_test_status)
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ str_starts_with($settings->mail_last_test_status, 'ok') ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ str_starts_with($settings->mail_last_test_status, 'ok') ? '✓ Working' : '✗ Error' }} · {{ $settings->mail_last_tested_at?->diffForHumans() }}
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.integrations.mail.update') }}" class="mt-5 grid gap-4 md:grid-cols-2">
            @csrf
            <div>
                <label class="label">Driver</label>
                <select class="input" name="mail_driver">
                    <option value="log"        @selected($settings->mail_driver === 'log')>Log · writes to storage/logs (safe default)</option>
                    <option value="resend"     @selected($settings->mail_driver === 'resend')>Resend · resend.com</option>
                    <option value="mailersend" @selected($settings->mail_driver === 'mailersend')>MailerSend · mailersend.com</option>
                    <option value="smtp"       @selected($settings->mail_driver === 'smtp')>SMTP · configure via .env</option>
                </select>
            </div>
            <div>
                <label class="label">API key</label>
                <input class="input font-mono" type="password" name="mail_api_key" autocomplete="off" placeholder="{{ $settings->mail_api_key ? $settings->maskedMailKey() : 're_… or ms.…' }}">
                <p class="mt-1 text-xs text-slate-500">Leave blank to keep existing. Ignored when driver is Log or SMTP.</p>
            </div>
            <div>
                <label class="label">From address</label>
                <input class="input" type="email" name="mail_from_address" value="{{ old('mail_from_address', $settings->mail_from_address) }}" placeholder="hello@creatorplex.in">
            </div>
            <div>
                <label class="label">From name</label>
                <input class="input" name="mail_from_name" value="{{ old('mail_from_name', $settings->mail_from_name ?: 'CreatorPlex') }}">
            </div>
            <div class="md:col-span-2">
                <label class="label">Reply-to (optional)</label>
                <input class="input" type="email" name="mail_reply_to" value="{{ old('mail_reply_to', $settings->mail_reply_to) }}" placeholder="support@creatorplex.in">
            </div>
            <div class="md:col-span-2 flex justify-end"><button class="btn-primary">Save mail settings</button></div>
        </form>

        <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4">
            <form method="POST" action="{{ route('admin.integrations.mail.test') }}" class="flex flex-wrap items-end gap-2">
                @csrf
                <div class="flex-1 min-w-[200px]">
                    <label class="label">Send a test email</label>
                    <input class="input" type="email" name="to" required placeholder="you@brand.com">
                </div>
                <button class="btn-secondary">Send test →</button>
            </form>
        </div>
    </section>

    {{-- ═════════════════════════ PAYMENTS ═════════════════════════ --}}
    <section id="payments" class="mt-8 scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-900">💳 Razorpay (Indian payments)</h2>
                <p class="mt-1 text-xs text-slate-500">Collect subscription + top-up payments in ₹. UPI, cards, netbanking, wallets — all in one flow.</p>
            </div>
            @if($settings->razorpay_last_test_status)
                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ str_starts_with($settings->razorpay_last_test_status, 'ok') ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                    {{ str_starts_with($settings->razorpay_last_test_status, 'ok') ? '✓ Connected' : '✗ Error' }} · {{ $settings->razorpay_last_tested_at?->diffForHumans() }}
                </span>
            @endif
        </div>

        <form method="POST" action="{{ route('admin.integrations.razorpay.update') }}" class="mt-5 grid gap-4 md:grid-cols-2">
            @csrf
            <div>
                <label class="label">Mode</label>
                <select class="input" name="razorpay_mode">
                    <option value="test" @selected($settings->razorpay_mode === 'test')>Test · use rzp_test_… keys</option>
                    <option value="live" @selected($settings->razorpay_mode === 'live')>Live · use rzp_live_… keys</option>
                </select>
            </div>
            <div>
                <label class="label">Key ID</label>
                <input class="input font-mono" name="razorpay_key_id" value="{{ old('razorpay_key_id', $settings->razorpay_key_id) }}" placeholder="rzp_test_XXXXXXXXXXXX">
            </div>
            <div>
                <label class="label">Key Secret</label>
                <input class="input font-mono" type="password" name="razorpay_key_secret" autocomplete="off" placeholder="{{ $settings->razorpay_key_secret ? $settings->maskedRazorpaySecret() : '' }}">
                <p class="mt-1 text-xs text-slate-500">Leave blank to keep existing.</p>
            </div>
            <div>
                <label class="label">Webhook secret</label>
                <input class="input font-mono" type="password" name="razorpay_webhook_secret" autocomplete="off" placeholder="{{ $settings->razorpay_webhook_secret ? '••••••••' : '' }}">
                <p class="mt-1 text-xs text-slate-500">From Razorpay Dashboard → Webhooks → your webhook → Secret.</p>
            </div>
            <div class="md:col-span-2 rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs text-slate-600">
                <p class="font-bold text-slate-800">Webhook URL</p>
                <p class="mt-1">Add this URL in Razorpay Dashboard → Webhooks:</p>
                <p class="mt-1 font-mono break-all rounded bg-white px-2 py-1">{{ url('/webhooks/razorpay') }}</p>
                <p class="mt-2">Subscribe to at least: <code>payment.captured</code>, <code>payment.failed</code>, <code>refund.created</code>, <code>subscription.charged</code>.</p>
            </div>
            <div class="md:col-span-2 flex justify-end gap-2">
                <form method="POST" action="{{ route('admin.integrations.razorpay.test') }}" class="inline-block">@csrf<button class="btn-secondary">Test connection</button></form>
                <button class="btn-primary">Save Razorpay settings</button>
            </div>
        </form>
    </section>

    {{-- ═════════════════════════ ANALYTICS ═════════════════════════ --}}
    <section id="analytics" class="mt-8 scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-6">
        <div>
            <h2 class="text-xl font-black text-slate-900">📊 Analytics + SEO verification</h2>
            <p class="mt-1 text-xs text-slate-500">Site-wide scripts injected on every marketing + app page. Paste your IDs — no code changes required.</p>
        </div>

        <form method="POST" action="{{ route('admin.integrations.analytics.update') }}" class="mt-5 grid gap-4 md:grid-cols-2">
            @csrf

            <div>
                <label class="label">GA4 Measurement ID</label>
                <input class="input font-mono" name="ga4_measurement_id" value="{{ old('ga4_measurement_id', $settings->ga4_measurement_id) }}" placeholder="G-XXXXXXXXXX">
                <p class="mt-1 text-xs text-slate-500">Skipped automatically if GTM is set (fire GA4 through GTM instead).</p>
            </div>
            <div>
                <label class="label">Google Tag Manager container</label>
                <input class="input font-mono" name="gtm_container_id" value="{{ old('gtm_container_id', $settings->gtm_container_id) }}" placeholder="GTM-XXXXXXX">
            </div>
            <div>
                <label class="label">Meta Pixel ID (Facebook / Instagram ads)</label>
                <input class="input font-mono" name="meta_pixel_id" value="{{ old('meta_pixel_id', $settings->meta_pixel_id) }}" placeholder="123456789012345">
            </div>
            <div>
                <label class="label">LinkedIn Partner ID</label>
                <input class="input font-mono" name="linkedin_partner_id" value="{{ old('linkedin_partner_id', $settings->linkedin_partner_id) }}" placeholder="1234567">
            </div>
            <div>
                <label class="label">Hotjar Site ID</label>
                <input class="input font-mono" name="hotjar_id" value="{{ old('hotjar_id', $settings->hotjar_id) }}" placeholder="3456789">
            </div>
            <div>
                <label class="label">Google Search Console verification token</label>
                <input class="input font-mono" name="google_site_verification" value="{{ old('google_site_verification', $settings->google_site_verification) }}" placeholder="abc123…">
                <p class="mt-1 text-xs text-slate-500">The value inside <code>content="…"</code> of the HTML tag Google gives you.</p>
            </div>
            <div>
                <label class="label">Bing Webmaster verification token</label>
                <input class="input font-mono" name="bing_site_verification" value="{{ old('bing_site_verification', $settings->bing_site_verification) }}" placeholder="msvalidate.01 content">
            </div>
            <div class="md:col-span-2">
                <label class="label">Custom &lt;head&gt; HTML <span class="text-xs font-normal text-slate-400">(escape hatch — Ahrefs, Clarity, Segment, etc.)</span></label>
                <textarea class="input font-mono min-h-32" name="custom_head_html" placeholder="<script src='...'></script>">{{ old('custom_head_html', $settings->custom_head_html) }}</textarea>
            </div>
            <div class="md:col-span-2">
                <label class="label">Custom end-of-&lt;body&gt; HTML</label>
                <textarea class="input font-mono min-h-24" name="custom_body_html">{{ old('custom_body_html', $settings->custom_body_html) }}</textarea>
            </div>

            <div class="md:col-span-2 rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs text-slate-600">
                <p class="font-bold text-slate-800">SEO submission checklist</p>
                <ol class="mt-2 list-decimal space-y-1 pl-5">
                    <li>Paste Google Search Console token above → hit Save → verify in <a href="https://search.google.com/search-console" class="text-violet-700 hover:underline" target="_blank">Search Console</a>.</li>
                    <li>Submit your sitemap: <code class="rounded bg-white px-1.5 py-0.5">{{ url('/sitemap.xml') }}</code></li>
                    <li>Bing Webmaster Tools: <a href="https://www.bing.com/webmasters" class="text-violet-700 hover:underline" target="_blank">bing.com/webmasters</a> — same drill.</li>
                </ol>
            </div>

            <div class="md:col-span-2 flex justify-end"><button class="btn-primary">Save analytics + SEO</button></div>
        </form>
    </section>

    {{-- ═════════════════════════ PUSH (VAPID) ═════════════════════════ --}}
    <section id="push" class="mt-8 scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-6">
        <div>
            <h2 class="text-xl font-black text-slate-900">🔔 Push notifications (VAPID)</h2>
            <p class="mt-1 text-xs text-slate-500">Paste your VAPID keys. Users see an "Enable push" button on the notifications page once these are set.</p>
        </div>

        <form method="POST" action="{{ route('admin.integrations.vapid.update') }}" class="mt-5 grid gap-4 md:grid-cols-2">
            @csrf
            <div class="md:col-span-2 rounded-xl border border-slate-100 bg-slate-50 p-4 text-xs text-slate-600">
                <p class="font-bold text-slate-800">Generate keys once</p>
                <p class="mt-1">On any machine with Node installed run:</p>
                <p class="mt-1 font-mono break-all rounded bg-white px-2 py-1">npx web-push generate-vapid-keys</p>
                <p class="mt-2">Copy the <strong>public key</strong> and <strong>private key</strong> into the fields below. The subject is a mailto/https URL Chrome uses to contact you if the push service has questions.</p>
            </div>
            <div>
                <label class="label">Public key</label>
                <input class="input font-mono" name="vapid_public_key" value="{{ old('vapid_public_key', $settings->vapid_public_key) }}" placeholder="BEl62iUY…">
            </div>
            <div>
                <label class="label">Private key</label>
                <input class="input font-mono" type="password" name="vapid_private_key" autocomplete="off" placeholder="{{ $settings->vapid_private_key ? '••••••••••••' : '' }}">
                <p class="mt-1 text-xs text-slate-500">Leave blank to keep existing.</p>
            </div>
            <div class="md:col-span-2">
                <label class="label">Subject</label>
                <input class="input" name="vapid_subject" value="{{ old('vapid_subject', $settings->vapid_subject ?: 'mailto:hello@creatorplex.in') }}" placeholder="mailto:you@example.com">
            </div>
            <div class="md:col-span-2 flex justify-end"><button class="btn-primary">Save VAPID keys</button></div>
        </form>
    </section>

    {{-- ═════════════════════════ FEATURE FLAGS ═════════════════════════ --}}
    <section id="features" class="mt-8 scroll-mt-24 rounded-2xl border border-slate-200 bg-white p-6">
        <div>
            <h2 class="text-xl font-black text-slate-900">🚦 Roadmap feature flags</h2>
            <p class="mt-1 text-xs text-slate-500">Turn upcoming features on/off. Some require additional setup (see notes).</p>
        </div>

        <form method="POST" action="{{ route('admin.integrations.features.update') }}" class="mt-5 grid gap-3 md:grid-cols-2">
            @csrf
            @php
                $flagLabels = [
                    'contract_esign'       => ['⚡ Contract e-signature',        'Enables HelloSign / DocuSign flow on contracts.'],
                    'referrals'            => ['🎁 Refer-a-brand + creator affiliate program', 'Adds referral codes + payout tracking.'],
                    'ab_testing'           => ['🧪 A/B testing on landing pages', 'Experiment framework for hero + CTA variants.'],
                    'push_notifications'   => ['🔔 PWA push notifications',       'Requires VAPID keys and service worker.'],
                    'fraud_scan'           => ['🛡️ Automated fraud/bot detection', 'Scheduled AiRun computes creator fraud_risk nightly.'],
                    'auto_content_review'  => ['🤖 AI content review automation',   'Auto-score submitted UGC against brand brief.'],
                    'agency_mode'          => ['🏢 Agency accounts',                'Multi-brand hierarchy under one agency.'],
                    'public_creator_pages' => ['🌟 Public creator portfolios',      'SEO-friendly /creator/{slug} pages.'],
                    'case_study_cms'       => ['📚 Admin-managed case studies',     'Public case study library at /case-studies.'],
                ];
            @endphp
            @foreach($flagLabels as $key => [$label, $desc])
                <label class="flex items-start gap-3 rounded-xl border border-slate-200 p-4 has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50/50">
                    <input type="hidden" name="features[{{ $key }}]" value="0">
                    <input type="checkbox" name="features[{{ $key }}]" value="1" @checked($features[$key] ?? false) class="mt-1 h-5 w-5 rounded border-slate-300 text-violet-600 focus:ring-violet-400">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">{{ $label }}</div>
                        <div class="mt-0.5 text-xs text-slate-500">{{ $desc }}</div>
                    </div>
                </label>
            @endforeach
            <div class="md:col-span-2 flex justify-end"><button class="btn-primary">Save feature flags</button></div>
        </form>
    </section>
</x-layouts.admin>
