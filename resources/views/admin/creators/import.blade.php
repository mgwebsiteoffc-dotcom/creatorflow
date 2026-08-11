<x-layouts.admin title="Bulk import creators">
    <a href="{{ route('admin.creators.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Creators</a>
    <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Bulk-import creators</h1>
    <p class="mt-1 text-sm text-slate-500">Upload a CSV. Each row becomes a creator (and a shell user account if an email is provided).</p>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <form method="POST" action="{{ route('admin.creators.import.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="rounded-2xl border-2 border-dashed border-violet-300 bg-gradient-to-br from-violet-50 to-pink-50 p-8 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl text-xl text-white shadow-lg" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899);">📥</div>
                    <p class="mt-4 text-sm font-bold text-slate-900">Choose a CSV file</p>
                    <p class="mt-1 text-xs text-slate-500">First row must be a header. Max 5 MB.</p>
                    <input type="file" name="file" accept=".csv,text/csv" required class="mx-auto mt-4 block w-full max-w-xs text-xs">
                </div>
                <button class="btn-primary w-full">Import creators</button>
            </form>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-sm font-bold text-slate-900">Expected columns</h3>
            <div class="mt-3 space-y-1 text-xs text-slate-600">
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">name</code> — required</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">email</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">country</code> · 2-letter ISO</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">city</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">bio</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">niches</code> · comma-separated</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">followers</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">engagement_rate</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">rate_ugc_cents</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">instagram_handle</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">tiktok_handle</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">youtube_handle</code></div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">accepts_paid</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5">accepts_barter</code> · true/false</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">gender</code> · female/male/non_binary/other</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">age_range</code> · 13-17, 18-24, 25-34, 35-44, 45-54, 55+</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">state</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5">languages</code> · comma list (en,hi)</div>
                <div><code class="rounded bg-slate-100 px-1.5 py-0.5">audience_female_pct</code>, <code class="rounded bg-slate-100 px-1.5 py-0.5">audience_male_pct</code></div>
            </div>

            <p class="mt-4 text-xs text-slate-500">Tier (nano / micro / mid / macro / mega) is auto-derived from <code>followers</code>. City must match one of Delhi, Mumbai, Bangalore, Hyderabad, Chennai, Pune, Kolkata, Ahmedabad, Jaipur, Gurugram, Noida, Lucknow, Chandigarh, Indore, Kochi, Goa, Surat, Bhopal.</p>

            <div class="mt-4 rounded-xl bg-slate-50 p-3 font-mono text-[11px] leading-relaxed text-slate-700">
                name,email,country,city,gender,age_range,languages,niches,followers,engagement_rate,instagram_handle<br>
                Aria Kim,aria@x.com,IN,Delhi,female,25-34,"en,hi",Beauty,62000,8.1,ariak<br>
                Theo V,theo@x.com,IN,Mumbai,male,25-34,"en,mr",Lifestyle,1200000,5.4,theovlog
            </div>
        </div>
    </div>
</x-layouts.admin>
