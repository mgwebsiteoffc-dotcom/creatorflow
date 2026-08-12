<x-layouts.admin title="Notification templates">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Superadmin · Messaging</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">📧 Notification templates</h1>
            <p class="mt-1 text-sm text-slate-500">Every process + transaction fires a template. Toggle email / WhatsApp / in-app per event. Placeholders like <code class="rounded bg-slate-100 px-1.5">{{ '{{creator_name}}' }}</code> get interpolated at send-time.</p>
        </div>
    </div>

    @if($schemaMissing ?? false)
        <div class="mt-6 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate --seed</code> to create the templates table.
        </div>
    @endif

    @foreach($templates ?? [] as $audience => $group)
        <section class="mt-8">
            <h2 class="text-lg font-black text-slate-900">
                @if($audience === 'creator') 🎬 For creators
                @elseif($audience === 'brand') 🏢 For brands
                @else 🛡 For admins
                @endif
                <span class="ml-2 text-xs font-normal text-slate-500">{{ $group->count() }} template{{ $group->count() === 1 ? '' : 's' }}</span>
            </h2>
            <div class="mt-3 space-y-3">
                @foreach($group as $t)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="font-black text-slate-900">{{ $t->label }}</p>
                                <p class="mt-0.5 text-xs text-slate-500">{{ $t->description }}</p>
                                <p class="mt-1 font-mono text-[11px] text-slate-400">event: {{ $t->event_key }}</p>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $t->email_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    📧 Email {{ $t->email_enabled ? 'on' : 'off' }}
                                </span>
                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $t->whatsapp_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    💬 WhatsApp {{ $t->whatsapp_enabled ? 'on' : 'off' }}
                                </span>
                                <span class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold {{ $t->inapp_enabled ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    🔔 In-app {{ $t->inapp_enabled ? 'on' : 'off' }}
                                </span>
                                <a href="{{ route('admin.notification-templates.edit', $t) }}" class="btn-secondary !py-1 !text-xs">Edit</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

    <p class="mt-8 text-xs text-slate-500">💡 Tip: for WhatsApp business messages outside the 24-hour session window you must use a Whatify-approved <strong>template</strong>. Set the template name (matches what you created inside Whatify) — the body placeholders map 1-to-1 with Whatify's <code>body_params</code>.</p>
</x-layouts.admin>
