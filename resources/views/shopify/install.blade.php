<x-layouts.app panel="guest"
    title="Install CreatorPlex on Shopify"
    metaDescription="Connect your Shopify store to CreatorPlex — India's AI-powered influencer marketing platform. Free plan available.">

    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="aurora-3"></div>
        <div class="dotted absolute inset-0 -z-10"></div>

        <div class="mx-auto max-w-5xl px-4 pb-20 pt-16 md:pt-24">
            <div class="grid gap-10 md:grid-cols-2 md:items-center">
                <div class="reveal">
                    <span class="chip"><span class="chip-dot"></span>Shopify app · Free to start</span>
                    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                        Connect your <span class="text-gradient">Shopify store</span>to CreatorPlex
                    </h1>
                    <p class="mt-4 text-lg text-slate-600">
                        Sync products, seed creators in bulk, and attribute revenue — all inside one dashboard. Setup takes under 60 seconds.
                    </p>

                    <ul class="mt-6 space-y-3 text-sm">
                        <li class="flex items-start gap-2"><span class="mt-0.5 grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white"></span>Auto-sync every product + variant + inventory</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white"></span>Unique discount codes per creator</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white"></span>Attribute every ₹ to the creator who drove it</li>
                        <li class="flex items-start gap-2"><span class="mt-0.5 grid h-5 w-5 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white"></span>Auto-create orders for barter (seeding) campaigns</li>
                    </ul>
                </div>

                <div class="reveal">
                    <div class="g-border p-1 shadow-xl">
                        <div class="rounded-[calc(1.25rem-1px)] bg-white p-6">
                            <h2 class="text-lg font-bold text-slate-900">Enter your Shopify store</h2>
                            <p class="mt-1 text-xs text-slate-500">You'll be redirected to Shopify to approve the app.</p>

                            <form method="POST" action="{{ route('shopify.install') }}" class="mt-5 space-y-3">
                                @csrf
                                <div>
                                    <label class="label">Store URL</label>
                                    <div class="flex overflow-hidden rounded-xl border border-slate-200 focus-within:border-violet-400">
                                        <input class="flex-1 border-0 bg-transparent px-3 py-2.5 text-sm focus:outline-none"
                                               type="text" name="shop" required
                                               autocomplete="off"
                                               placeholder="mystore"
                                               value="{{ old('shop') }}">
                                        <span class="grid place-items-center bg-slate-50 px-3 text-xs font-medium text-slate-500">.myshopify.com</span>
                                    </div>
                                    @error('shop')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                    <p class="mt-1 text-xs text-slate-400">You can also paste the full URL like <code class="rounded bg-slate-100 px-1">mystore.myshopify.com</code>.</p>
                                </div>

                                <button type="submit" class="btn-gradient w-full">Install on Shopify →</button>
                            </form>

                            <div class="mt-4 flex items-center gap-2 text-xs text-slate-500">
                                <svg class="h-4 w-4 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.7-9.3l-4 4a1 1 0 01-1.4 0l-2-2a1 1 0 111.4-1.4L9 10.6l3.3-3.3a1 1 0 011.4 1.4z" clip-rule="evenodd"/></svg>
                                OAuth secured · we never see your Shopify password
                            </div>

                            <div class="mt-6 border-t border-slate-100 pt-4 text-center">
                                <p class="text-xs text-slate-500">Don't have Shopify? Start on the web brand panel instead.</p>
                                <a href="{{ route('register') }}" class="btn-secondary mt-2 w-full !py-2">Create free account</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
