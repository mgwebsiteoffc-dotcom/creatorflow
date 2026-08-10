<x-layouts.app panel="guest" title="Create account">
    <div class="mx-auto max-w-md pt-6 md:pt-16">
        <div class="card p-6 md:p-8">
            <a href="{{ url('/') }}" class="mb-6 flex items-center gap-2 font-bold">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-600 text-white">CF</span>
                CreatorFlow
            </a>
            <h1 class="text-2xl font-bold">Create your account</h1>
            <p class="mt-1 text-sm text-slate-500">Choose how you'll use CreatorFlow.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="label">I am a…</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer rounded-xl border border-slate-200 p-3 text-center has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                            <input type="radio" name="account_type" value="brand" class="sr-only" checked>
                            <div class="text-lg">🛍️</div>
                            <div class="text-sm font-semibold">Brand / Merchant</div>
                        </label>
                        <label class="cursor-pointer rounded-xl border border-slate-200 p-3 text-center has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50">
                            <input type="radio" name="account_type" value="creator" class="sr-only">
                            <div class="text-lg">🎬</div>
                            <div class="text-sm font-semibold">Creator</div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="label">Full name</label>
                    <input class="input" name="name" value="{{ old('name') }}" required autofocus>
                </div>
                <div>
                    <label class="label">Email</label>
                    <input class="input" type="email" name="email" value="{{ old('email') }}" required>
                </div>
                <div>
                    <label class="label">Password</label>
                    <input class="input" type="password" name="password" required>
                </div>
                <div>
                    <label class="label">Confirm password</label>
                    <input class="input" type="password" name="password_confirmation" required>
                </div>

                <button class="btn-primary w-full">Create account</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-violet-600">Sign in</a>
            </p>
        </div>
    </div>
</x-layouts.app>
