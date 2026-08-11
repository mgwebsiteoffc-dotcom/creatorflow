<footer class="relative mt-24 overflow-hidden border-t border-slate-200 bg-slate-950 text-slate-300">
    <div class="pointer-events-none absolute inset-0 opacity-40"
         style="background: radial-gradient(600px 300px at 20% 0%, rgba(124,58,237,.35), transparent 60%), radial-gradient(600px 300px at 80% 100%, rgba(236,72,153,.25), transparent 60%);"></div>

    {{-- Newsletter strip --}}
    <div class="relative border-b border-white/10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-4 py-8 md:flex-row">
            <div>
                <h3 class="text-lg font-black text-white">CreatorPlex Weekly</h3>
                <p class="text-xs text-slate-400">One 5-minute read every Friday. Playbooks, benchmarks, viral drops.</p>
            </div>
            <form onsubmit="event.preventDefault(); alert('Subscribed ✓')" class="flex w-full max-w-md gap-2 md:w-auto">
                <input type="email" required placeholder="you@brand.com" class="input flex-1 !bg-white/95 !text-slate-900">
                <button class="btn-gradient">Subscribe →</button>
            </form>
        </div>
    </div>

    {{-- Main column grid --}}
    <div class="relative mx-auto grid max-w-6xl gap-10 px-4 py-14 md:grid-cols-6">
        {{-- Brand block (2 cols) --}}
        <div class="md:col-span-2">
            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-white">
                <span class="grid h-9 w-9 place-items-center rounded-xl text-white"
                      style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">
                    <span class="text-sm font-black">CF</span>
                </span>
                <span>CreatorPlex</span>
            </a>
            <p class="mt-4 max-w-md text-sm text-slate-400">
                Tech- and AI-powered creator marketing. Launch campaigns in minutes, seed products in bulk,
                and attribute revenue — for Shopify or any brand.
            </p>
            <div class="mt-6 flex gap-3">
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="Instagram">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="3" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="TikTok">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M16 3v3.2a5 5 0 003.2 1.2v3a8 8 0 01-4.2-1.2v6.6a5.5 5.5 0 11-5.5-5.5c.3 0 .6 0 .8.1v3.1a2.5 2.5 0 102 2.4V3h3.7z"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="YouTube">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M21.6 7.2a2.5 2.5 0 00-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 002.4 7.2 26 26 0 002 12a26 26 0 00.4 4.8 2.5 2.5 0 001.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 001.8-1.8A26 26 0 0022 12a26 26 0 00-.4-4.8zM10 15V9l5 3-5 3z"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="X">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.5 3H21l-7.5 8.6L22 21h-6.9l-4.7-6.1L4.9 21H1.4l8-9.2L1.6 3h7l4.3 5.7L17.5 3z"/></svg>
                </a>
                <a href="#" class="grid h-9 w-9 place-items-center rounded-full border border-white/10 text-slate-300 hover:bg-white/10" aria-label="LinkedIn">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M6.5 3.5a2 2 0 100 4 2 2 0 000-4zM4.5 8.5h4V21h-4V8.5zM10.5 8.5h4v2a3.5 3.5 0 013.2-1.8c3.4 0 4 2.2 4 5V21h-4v-5.4c0-1.3 0-3-1.9-3s-2.2 1.4-2.2 2.9V21h-4V8.5z"/></svg>
                </a>
            </div>
        </div>

        {{-- Quick links --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white">Quick links</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('login') }}" class="hover:text-white">Brand login</a></li>
                <li><a href="{{ route('login') }}" class="hover:text-white">Creator login</a></li>
                <li><a href="{{ route('brand.campaigns.create') }}" class="hover:text-white">Guided campaign</a></li>
                <li><a href="{{ route('features') }}" class="hover:text-white">Platform features</a></li>
                <li><a href="{{ route('register') }}" class="hover:text-white">Creators</a></li>
                <li><a href="{{ route('resources') }}" class="hover:text-white">Knowledge base</a></li>
                <li><a href="{{ route('legal.terms') }}" class="hover:text-white">Terms of use</a></li>
                <li><a href="{{ route('legal.privacy') }}" class="hover:text-white">Privacy policy</a></li>
                <li><a href="{{ route('legal.refund') }}" class="hover:text-white">Refund policy</a></li>
                <li><a href="{{ url('/sitemap.xml') }}" class="hover:text-white">Sitemap</a></li>
            </ul>
        </div>

        {{-- Industries --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white">By industry</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('industry.show', 'fashion-and-lifestyle') }}" class="hover:text-white">Fashion &amp; lifestyle</a></li>
                <li><a href="{{ route('industry.show', 'beauty-and-cosmetics') }}" class="hover:text-white">Beauty &amp; cosmetics</a></li>
                <li><a href="{{ route('industry.show', 'food-and-fitness') }}" class="hover:text-white">Food &amp; fitness</a></li>
                <li><a href="{{ route('industry.show', 'tech-and-education') }}" class="hover:text-white">Tech &amp; education</a></li>
                <li><a href="{{ route('industry.show', 'travel-and-hospitality') }}" class="hover:text-white">Travel &amp; hospitality</a></li>
                <li><a href="{{ route('industry.show', 'home-and-decor') }}" class="hover:text-white">Home &amp; decor</a></li>
            </ul>
        </div>

        {{-- Campaign types --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white">Campaign types</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="{{ route('campaign-type.show', 'product-review') }}" class="hover:text-white">Product review</a></li>
                <li><a href="{{ route('campaign-type.show', 'brand-awareness') }}" class="hover:text-white">Brand awareness</a></li>
                <li><a href="{{ route('campaign-type.show', 'store-visit') }}" class="hover:text-white">Store visit</a></li>
                <li><a href="{{ route('campaign-type.show', 'self-managed') }}" class="hover:text-white">Self managed</a></li>
                <li><a href="{{ route('campaign-type.show', 'barter-campaign') }}" class="hover:text-white">Barter campaign</a></li>
                <li><a href="{{ route('campaign-type.show', 'video-shoot') }}" class="hover:text-white">Video shoot</a></li>
            </ul>
        </div>

        {{-- Collaboration --}}
        <div>
            <h4 class="text-xs font-black uppercase tracking-widest text-white">Collaboration</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="#" class="hover:text-white">Agency signup</a></li>
                <li><a href="#" class="hover:text-white">Manager signup</a></li>
                <li><a href="#" class="hover:text-white">Investor connect</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Platform demo</a></li>
                <li><a href="#" class="hover:text-white">Careers</a></li>
                <li><a href="{{ route('contact') }}" class="hover:text-white">Contact us</a></li>
                <li><a href="{{ route('blog.index') }}" class="hover:text-white">Blog</a></li>
                <li><a href="{{ route('shopify.install') }}" class="hover:text-white">Shopify app</a></li>
            </ul>
        </div>
    </div>

    {{-- ============================ HORIZONTAL CITY + POLICY STRIP ============================ --}}
    <div class="relative border-t border-white/10 bg-black/20">
        <div class="mx-auto max-w-6xl px-4 py-6 text-xs leading-relaxed text-slate-400">
            @php
                // Cities we actually have city landing pages for (SeoData::cities).
                $footerCityMap = [
                    'delhi'     => 'Delhi',
                    'mumbai'    => 'Mumbai',
                    'bangalore' => 'Bangalore',
                    'hyderabad' => 'Hyderabad',
                    'chennai'   => 'Chennai',
                    'pune'      => 'Pune',
                    'kolkata'   => 'Kolkata',
                    'ahmedabad' => 'Ahmedabad',
                    'jaipur'    => 'Jaipur',
                    'gurugram'  => 'Gurugram',
                    'india'     => 'Pan-India',
                ];
                $policyLinks = [
                    'Terms of Use'       => route('legal.terms'),
                    'Privacy Policy'     => route('legal.privacy'),
                    'Refund Policy'      => route('legal.refund'),
                    'Cookie Policy'      => route('legal.cookies'),
                    'Shipping Policy'    => route('legal.shipping'),
                    'Content Guidelines' => route('legal.content'),
                    'Creator Agreement'  => route('legal.creator-agreement'),
                    'Sitemap'            => url('/sitemap.xml'),
                ];
            @endphp

            <p class="[&_a]:mx-0.5 [&_a]:hover:text-white [&_a]:underline-offset-2">
                <span class="mr-1 font-semibold uppercase tracking-widest text-slate-300">Influencer marketing in:</span>
                @foreach($footerCityMap as $slug => $name)
                    <a href="{{ route('services.city', ['influencer-marketing-agency', $slug]) }}">{{ $name }}</a>{{ ! $loop->last ? ',' : '' }}
                @endforeach
            </p>
            <p class="mt-2 [&_a]:mx-0.5 [&_a]:hover:text-white">
                <span class="mr-1 font-semibold uppercase tracking-widest text-slate-300">UGC influencers in:</span>
                @foreach($footerCityMap as $slug => $name)
                    <a href="{{ route('services.city', ['ugc-influencers', $slug]) }}">{{ $name }}</a>{{ ! $loop->last ? ',' : '' }}
                @endforeach
            </p>
            <p class="mt-2 [&_a]:mx-0.5 [&_a]:hover:text-white">
                <span class="mr-1 font-semibold uppercase tracking-widest text-slate-300">Barter influencers in:</span>
                @foreach($footerCityMap as $slug => $name)
                    <a href="{{ route('services.city', ['barter-influencers', $slug]) }}">{{ $name }}</a>{{ ! $loop->last ? ',' : '' }}
                @endforeach
            </p>
            <p class="mt-2 [&_a]:mx-0.5 [&_a]:hover:text-white">
                <span class="mr-1 font-semibold uppercase tracking-widest text-slate-300">Micro influencer marketing in:</span>
                @foreach($footerCityMap as $slug => $name)
                    <a href="{{ route('services.city', ['micro-influencer-marketing', $slug]) }}">{{ $name }}</a>{{ ! $loop->last ? ',' : '' }}
                @endforeach
            </p>

            <p class="mt-4 pt-3 border-t border-white/5 [&_a]:mx-0.5 [&_a]:hover:text-white">
                <span class="mr-1 font-semibold uppercase tracking-widest text-slate-300">Policies:</span>
                @foreach($policyLinks as $name => $href)
                    <a href="{{ $href }}">{{ $name }}</a>{{ ! $loop->last ? ',' : '' }}
                @endforeach
            </p>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div class="relative border-t border-white/10">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-slate-400 sm:flex-row">
            <span>© {{ date('Y') }} CreatorPlex. Built for creators &amp; brands.</span>
            <span class="flex items-center gap-4">
                <span class="flex items-center gap-2">
                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-400"></span> All systems operational
                </span>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-white/10 px-2.5 py-1 font-semibold text-slate-300 hover:bg-white/10 hover:text-white">🛡️ Admin</a>
                    @endif
                @endauth
            </span>
        </div>
    </div>
</footer>
