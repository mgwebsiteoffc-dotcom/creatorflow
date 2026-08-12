<x-layouts.app panel="guest" title="Invitation no longer valid">
    <section class="mx-auto max-w-lg px-4 py-24 text-center">
        <div class="mx-auto mb-6 grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br from-slate-100 to-slate-200 text-slate-500 shadow-inner">
            <x-icon name="clock" class="h-7 w-7" />
        </div>
        <h1 class="text-2xl font-black text-slate-900">This invitation is no longer valid</h1>
        <p class="mt-3 text-sm text-slate-600">
            It may have expired or been withdrawn by the brand. You can browse open campaigns and apply directly from the marketplace.
        </p>
        <div class="mt-6 flex flex-col items-center gap-2 sm:flex-row sm:justify-center">
            <a href="{{ url('/') }}" class="btn-secondary">Back to home</a>
            @auth
                <a href="{{ route('creator.marketplace') }}" class="btn-gradient">Browse campaigns</a>
            @else
                <a href="{{ route('login') }}" class="btn-gradient">Sign in</a>
            @endauth
        </div>
    </section>
</x-layouts.app>
