<x-layouts.admin :title="'Template · '.$tpl->label">
    <a href="{{ route('admin.notification-templates.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← All templates</a>

    <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">{{ $tpl->label }}</h1>
            <p class="mt-1 text-xs text-slate-500">Audience: <span class="capitalize font-semibold">{{ $tpl->audience }}</span> · Event: <code class="font-mono">{{ $tpl->event_key }}</code></p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.notification-templates.update', $tpl) }}" class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf
        @method('PATCH')

        <div class="lg:col-span-2 space-y-6">
            {{-- Email --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">📧 Email</h2>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold">
                        <input type="hidden" name="email_enabled" value="0">
                        <input type="checkbox" name="email_enabled" value="1" @checked($tpl->email_enabled) class="rounded"> Send email
                    </label>
                </div>
                <div class="mt-4 grid gap-3">
                    <div><label class="label">Subject</label><input class="input" name="email_subject" value="{{ old('email_subject', $tpl->email_subject) }}"></div>
                    <div>
                        <label class="label">Body <span class="text-xs font-normal text-slate-400">— plain text, supports @{{placeholders}}</span></label>
                        <textarea class="input font-mono min-h-64" name="email_body">{{ old('email_body', $tpl->email_body) }}</textarea>
                    </div>
                </div>
            </section>

            {{-- WhatsApp --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">💬 WhatsApp (Whatify)</h2>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold">
                        <input type="hidden" name="whatsapp_enabled" value="0">
                        <input type="checkbox" name="whatsapp_enabled" value="1" @checked($tpl->whatsapp_enabled) class="rounded"> Send WhatsApp
                    </label>
                </div>
                <div class="mt-4 grid gap-3">
                    <div>
                        <label class="label">Approved template name</label>
                        <input class="input font-mono" name="whatsapp_template_name" value="{{ old('whatsapp_template_name', $tpl->whatsapp_template_name) }}" placeholder="creator_invited">
                        <p class="mt-1 text-xs text-slate-500">Must match the template you created + got approved inside Whatify.</p>
                    </div>
                    <div>
                        <label class="label">Body placeholders <span class="text-xs font-normal text-slate-400">— comma separated, in template order</span></label>
                        <input class="input font-mono" name="whatsapp_body_params" value="{{ collect((array) $tpl->whatsapp_body_params)->implode(', ') }}" placeholder="@{{creator_name}}, @{{campaign_title}}, @{{brand_name}}">
                        <p class="mt-1 text-xs text-slate-500">Each param maps to <code>@{{1}} @{{2}} @{{3}}</code> in Whatify's approved template.</p>
                    </div>
                </div>
            </section>

            {{-- In-app --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-black text-slate-900">🔔 In-app</h2>
                    <label class="inline-flex items-center gap-2 text-xs font-semibold">
                        <input type="hidden" name="inapp_enabled" value="0">
                        <input type="checkbox" name="inapp_enabled" value="1" @checked($tpl->inapp_enabled) class="rounded"> Create notification row
                    </label>
                </div>
                <p class="mt-2 text-xs text-slate-500">Shows up in the recipient's notification bell + /notifications inbox. Uses the email subject + first 200 chars of body.</p>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Metadata</h2>
                <div class="mt-3 space-y-3">
                    <div><label class="label">Label</label><input class="input" name="label" value="{{ old('label', $tpl->label) }}"></div>
                    <div><label class="label">Description</label><textarea class="input min-h-20" name="description">{{ old('description', $tpl->description) }}</textarea></div>
                    <button class="btn-primary w-full">Save template</button>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-5 text-xs text-slate-600">
                <h3 class="text-sm font-black text-slate-800">Available placeholders</h3>
                <p class="mt-1">CreatorPlex passes these to the interpolator when this event fires:</p>
                <div class="mt-2 flex flex-wrap gap-1 font-mono text-[11px]">
                    @foreach(['platform','app_url','link','creator_name','brand_name','campaign_title','product_title','tracking_number','tracking_company','amount','followers','engagement_rate','comment','message','support_email'] as $p)
                        <span class="rounded bg-white px-1.5 py-0.5">&#123;&#123;{{ $p }}&#125;&#125;</span>
                    @endforeach
                </div>
            </section>
        </aside>
    </form>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-black text-slate-900">✉️ Send a test</h2>
        <form method="POST" action="{{ route('admin.notification-templates.test', $tpl) }}" class="mt-3 grid gap-3 md:grid-cols-3">
            @csrf
            <div><label class="label">Test email</label><input class="input" type="email" name="to_email" placeholder="you@brand.com"></div>
            <div><label class="label">Test phone (E.164)</label><input class="input" name="to_phone" placeholder="+91 98765 43210"></div>
            <div class="md:pt-6"><button class="btn-gradient">Fire test →</button></div>
        </form>
        <p class="mt-2 text-xs text-slate-500">Test uses fake sample data (Aisha, Glow &amp; Co., SPF 50…). Result is shown in a flash message.</p>
    </section>
</x-layouts.admin>
