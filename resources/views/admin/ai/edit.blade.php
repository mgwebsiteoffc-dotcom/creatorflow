<x-layouts.admin title="AI settings">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">🤖 AI settings</h1>
            <p class="mt-1 text-sm text-slate-500">Manage the AI provider used for briefs, creator matching, content review and outreach.</p>
        </div>
    </div>

    @if($schemaMissing)
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">platform_settings table not migrated yet.</p><p class="mt-1 text-xs">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code>. Settings can't be saved yet.</p></div>
        </div>
    @elseif($columnsMissing)
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">AI columns need migration.</p><p class="mt-1 text-xs">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code> to add ai_driver, ai_openai_key, ai_openai_model, etc.</p></div>
        </div>
    @endif

    {{-- Status card --}}
    <div class="mt-6 grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-gradient-to-br from-violet-500/5 to-pink-500/5 p-6">
            <div class="flex items-center gap-3">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-lg text-white shadow-sm">✨</div>
                <div class="flex-1">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Current AI provider</p>
                    <p class="mt-0.5 text-lg font-bold text-slate-900">
                        @if($settings->ai_driver === 'openai' && $settings->ai_openai_key)
                            OpenAI · {{ $settings->ai_openai_model ?: 'gpt-4o-mini' }}
                        @elseif($settings->ai_driver === 'openai')
                            <span class="text-amber-600">OpenAI (no key set)</span>
                        @else
                            Offline mock (fake driver)
                        @endif
                    </p>
                </div>
                @if($settings->ai_last_test_status)
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ str_starts_with($settings->ai_last_test_status, 'ok') ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ str_starts_with($settings->ai_last_test_status, 'ok') ? '✓ Connected' : '✗ Error' }}
                        @if($settings->ai_last_tested_at)
                            · {{ $settings->ai_last_tested_at->diffForHumans() }}
                        @endif
                    </span>
                @endif
            </div>
            <p class="mt-3 text-sm text-slate-600">
                @if($settings->ai_driver === 'openai' && $settings->ai_openai_key)
                    Real AI is active. Briefs, outreach messages, matching semantics and content review will use OpenAI.
                @else
                    Currently returning deterministic mock responses — perfect for demos and development. Switch to OpenAI below to enable live briefs.
                @endif
            </p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Test connection</p>
            <p class="mt-2 text-sm text-slate-600">Sends one quick prompt to verify the key works.</p>
            <form method="POST" action="{{ route('admin.ai.test') }}" class="mt-3">
                @csrf
                <button class="btn-secondary w-full">Run test</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.ai.update') }}" class="mt-8 grid gap-6 lg:grid-cols-3">
        @csrf

        {{-- Driver selection --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-3">
            <h2 class="text-lg font-bold text-slate-900">Provider</h2>
            <p class="mt-1 text-xs text-slate-500">Pick a provider. OpenAI (or an OpenAI-compatible endpoint like Azure OpenAI, OpenRouter, Groq) powers live briefs. Offline mock is safe to use with no key — good for dev.</p>

            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <label class="cursor-pointer rounded-2xl border-2 border-slate-200 p-4 has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="ai_driver" value="openai" class="h-4 w-4" @checked($settings->ai_driver === 'openai')>
                        <div>
                            <p class="font-semibold">OpenAI (recommended)</p>
                            <p class="text-xs text-slate-500">Uses OpenAI's chat + embeddings API. Also works with Azure, OpenRouter, Groq — set custom base URL.</p>
                        </div>
                    </div>
                </label>
                <label class="cursor-pointer rounded-2xl border-2 border-slate-200 p-4 has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="ai_driver" value="fake" class="h-4 w-4" @checked($settings->ai_driver !== 'openai')>
                        <div>
                            <p class="font-semibold">Offline mock (fake)</p>
                            <p class="text-xs text-slate-500">Deterministic templated responses. No API cost. Great for staging + development.</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>

        {{-- Credentials --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 lg:col-span-2">
            <h2 class="text-lg font-bold text-slate-900">OpenAI credentials</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="label">API key <span class="text-xs font-normal text-slate-400">(starts with <code>sk-</code>)</span></label>
                    <input class="input font-mono" type="password" name="ai_openai_key" autocomplete="off"
                           placeholder="{{ $settings->ai_openai_key ? $settings->maskedOpenAiKey() : 'sk-…' }}">
                    <p class="mt-1 text-xs text-slate-500">Stored encrypted in the database. Leave blank to keep the existing key. Get a key from <a href="https://platform.openai.com/api-keys" target="_blank" class="text-violet-600 hover:underline">platform.openai.com/api-keys</a>.</p>
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="label">Chat model</label>
                        <input class="input" name="ai_openai_model" value="{{ old('ai_openai_model', $settings->ai_openai_model ?: 'gpt-4o-mini') }}" list="model-list">
                        <datalist id="model-list">
                            <option value="gpt-4o-mini">
                            <option value="gpt-4o">
                            <option value="gpt-4.1-mini">
                            <option value="gpt-4.1">
                            <option value="gpt-3.5-turbo">
                        </datalist>
                        <p class="mt-1 text-xs text-slate-500">Cheapest good default: gpt-4o-mini.</p>
                    </div>
                    <div>
                        <label class="label">Embedding model</label>
                        <input class="input" name="ai_openai_embedding_model" value="{{ old('ai_openai_embedding_model', $settings->ai_openai_embedding_model ?: 'text-embedding-3-small') }}">
                        <p class="mt-1 text-xs text-slate-500">Used for creator ↔ campaign semantic match.</p>
                    </div>
                </div>

                <div>
                    <label class="label">Custom base URL <span class="text-xs font-normal text-slate-400">(optional)</span></label>
                    <input class="input" type="url" name="ai_base_url" value="{{ old('ai_base_url', $settings->ai_base_url) }}" placeholder="https://api.openai.com/v1">
                    <p class="mt-1 text-xs text-slate-500">Point to Azure OpenAI, OpenRouter, Groq or any OpenAI-compatible endpoint. Leave blank for standard OpenAI.</p>
                </div>

                <div>
                    <label class="label">Temperature <span class="text-xs font-normal text-slate-400">(creativity 0.0 – 2.0)</span></label>
                    <input class="input" type="number" step="0.1" min="0" max="2" name="ai_temperature" value="{{ old('ai_temperature', $settings->ai_temperature ?: 0.4) }}">
                    <p class="mt-1 text-xs text-slate-500">Lower = more consistent briefs, higher = more creative variety. Default 0.4.</p>
                </div>
            </div>
        </div>

        {{-- What AI does --}}
        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500">What uses AI</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-700">
                <li class="flex items-start gap-2"><span>📝</span><div><p class="font-semibold">Campaign brief generator</p><p class="text-xs text-slate-500">Public /tools/brief-generator page.</p></div></li>
                <li class="flex items-start gap-2"><span>💬</span><div><p class="font-semibold">Creator outreach messages</p><p class="text-xs text-slate-500">Auto-drafted per invitation.</p></div></li>
                <li class="flex items-start gap-2"><span>🎯</span><div><p class="font-semibold">Semantic creator ↔ campaign matching</p><p class="text-xs text-slate-500">Embeddings power the "why matched" reasons.</p></div></li>
                <li class="flex items-start gap-2"><span>✅</span><div><p class="font-semibold">Content review scoring</p><p class="text-xs text-slate-500">Auto-flags briefs vs deliverable.</p></div></li>
                <li class="flex items-start gap-2"><span>🛡️</span><div><p class="font-semibold">Fraud checks</p><p class="text-xs text-slate-500">Anomaly detection on creator profiles.</p></div></li>
            </ul>
            <p class="mt-4 rounded-xl bg-amber-50 p-3 text-xs text-amber-800">
                <strong>Cost note:</strong> On gpt-4o-mini a full campaign brief runs ~$0.001. A month of active use for a mid brand averages under $2.
            </p>
        </div>

        <div class="lg:col-span-3 flex justify-end gap-2">
            <a href="{{ route('admin.dashboard') }}" class="btn-ghost">Cancel</a>
            <button class="btn-primary">Save AI settings</button>
        </div>
    </form>
</x-layouts.admin>
