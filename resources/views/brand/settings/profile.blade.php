<x-layouts.app panel="brand" title="Brand profile & settings">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Brand profile</h1>
            <p class="mt-1 text-sm text-slate-500">Everything creators, invoices, and receipts see about your brand.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('brand.billing.index') }}" class="btn-secondary !py-2 text-sm">💳 Billing</a>
            <a href="{{ route('brand.settings.team') }}" class="btn-secondary !py-2 text-sm">👥 Team</a>
        </div>
    </div>

    {{-- Section tabs (visual) --}}
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('brand.settings.profile') }}" class="tab-pill is-active">🏢 Profile</a>
        <a href="{{ route('brand.settings.team') }}"    class="tab-pill">👥 Team</a>
        <a href="{{ route('brand.billing.index') }}"    class="tab-pill">💳 Billing</a>
    </div>

    <form method="POST" action="{{ route('brand.settings.profile.update') }}" enctype="multipart/form-data"
          class="mt-8 grid gap-6 lg:grid-cols-3">
        @csrf

        {{-- Left column --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Basics --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-bold text-slate-900">Basics</h2>
                <p class="mt-1 text-sm text-slate-500">Public-facing brand name and how you contact us.</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Brand name <span class="text-rose-500">*</span></label>
                        <input class="input" name="name" required value="{{ old('name', $workspace->name) }}">
                    </div>
                    <div>
                        <label class="label">Legal / registered name</label>
                        <input class="input" name="legal_name" value="{{ old('legal_name', $workspace->legal_name) }}" placeholder="For invoices">
                    </div>
                    <div>
                        <label class="label">Website</label>
                        <input class="input" name="website" type="url" placeholder="https://…" value="{{ old('website', $workspace->website) }}">
                    </div>
                    <div>
                        <label class="label">Contact email</label>
                        <input class="input" name="contact_email" type="email" value="{{ old('contact_email', $workspace->contact_email) }}">
                    </div>
                    <div>
                        <label class="label">Contact phone</label>
                        <input class="input" name="contact_phone" value="{{ old('contact_phone', $workspace->contact_phone) }}" placeholder="+91 98…">
                    </div>
                    <div>
                        <label class="label">Country (ISO 2)</label>
                        <input class="input" name="country" maxlength="2" value="{{ old('country', $workspace->country) }}" placeholder="IN">
                    </div>
                </div>
            </section>

            {{-- Address --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-bold text-slate-900">Billing address</h2>
                <p class="mt-1 text-sm text-slate-500">Appears on every invoice and creator contract.</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label class="label">Address line 1</label>
                        <input class="input" name="address_line1" value="{{ old('address_line1', $workspace->address_line1) }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Address line 2</label>
                        <input class="input" name="address_line2" value="{{ old('address_line2', $workspace->address_line2) }}">
                    </div>
                    <div>
                        <label class="label">City</label>
                        <input class="input" name="address_city" value="{{ old('address_city', $workspace->address_city) }}">
                    </div>
                    <div>
                        <label class="label">State / region</label>
                        <input class="input" name="address_state" value="{{ old('address_state', $workspace->address_state) }}">
                    </div>
                    <div>
                        <label class="label">Postal code</label>
                        <input class="input" name="address_postal" value="{{ old('address_postal', $workspace->address_postal) }}">
                    </div>
                    <div>
                        <label class="label">Timezone</label>
                        <input class="input" name="timezone" value="{{ old('timezone', $workspace->timezone) }}" placeholder="Asia/Kolkata">
                    </div>
                </div>
            </section>

            {{-- Money --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-bold text-slate-900">Currency &amp; taxes</h2>
                <p class="mt-1 text-sm text-slate-500">Every price, payout, and invoice uses this currency.</p>

                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="label">Default currency <span class="text-rose-500">*</span></label>
                        <select class="input" name="currency" required>
                            @foreach($currencies as $code => $label)
                                <option value="{{ $code }}" @selected(old('currency', $workspace->currency) === $code)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-slate-500">Indian brands: pick <strong>INR (₹)</strong>. All prices you enter will show as ₹.</p>
                    </div>
                    <div>
                        <label class="label">Tax type</label>
                        <select class="input" name="tax_type">
                            @php $tt = old('tax_type', $workspace->tax_type ?: 'none'); @endphp
                            <option value="none"  @selected($tt==='none')>None</option>
                            <option value="gstin" @selected($tt==='gstin')>GSTIN (India)</option>
                            <option value="vat"   @selected($tt==='vat')>VAT (EU/UK)</option>
                            <option value="ein"   @selected($tt==='ein')>EIN (US)</option>
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Tax ID</label>
                        <input class="input" name="tax_id" value="{{ old('tax_id', $workspace->tax_id) }}" placeholder="27ABCDE1234F1Z5">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="label">Billing notes</label>
                        <textarea class="input min-h-20" name="billing_notes" placeholder="PO number, GST rules, accounts email…">{{ old('billing_notes', $workspace->billing_notes) }}</textarea>
                    </div>
                </div>
            </section>

            <div class="flex justify-end">
                <button class="btn-primary">Save profile</button>
            </div>
        </div>

        {{-- Right rail --}}
        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Brand logo</h3>
                <p class="mt-1 text-xs text-slate-500">Shown to creators when they see your campaigns.</p>
                <div class="mt-4 flex items-center gap-4">
                    <div class="grid h-16 w-16 place-items-center overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 text-lg font-black text-white">
                        @if($workspace->logo_path)
                            <img src="{{ $workspace->logo_path }}" class="h-16 w-16 object-cover" alt="">
                        @else
                            {{ strtoupper(substr($workspace->name, 0, 2)) }}
                        @endif
                    </div>
                    <input type="file" name="logo" accept="image/*" class="block w-full text-xs">
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Plan</h3>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl font-black capitalize text-slate-900">{{ $workspace->plan }}</span>
                    <span class="text-xs text-slate-500">· {{ $workspace->plan_status }}</span>
                </div>
                <a href="{{ route('pricing') }}" class="mt-4 inline-block text-sm font-semibold text-violet-700 hover:text-violet-900">Change plan →</a>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Preview</h3>
                <div class="mt-3 rounded-xl bg-slate-50 p-3 text-sm">
                    <div class="text-xs text-slate-500">Sample creator fee</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $workspace->formatMoney(15000) }}</div>
                    <div class="text-xs text-slate-500">{{ $workspace->currency }} · {{ \App\Models\Workspace::symbolFor($workspace->currency) }}</div>
                </div>
            </div>
        </aside>
    </form>
</x-layouts.app>
