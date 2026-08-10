<x-layouts.app panel="guest" title="Sign in">
    <div class="mx-auto max-w-md pt-6 md:pt-20">
        <div class="card p-6 md:p-8">
            <a href="{{ url('/') }}" class="mb-6 flex items-center gap-2 font-bold">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-600 text-white">CF</span>
                CreatorFlow
            </a>
            <h1 class="text-2xl font-bold">Welcome back</h1>
            <p class="mt-1 text-sm text-slate-500">Sign in to your dashboard.</p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf
                <div>
                    <label class="label">Email</label>
                    <input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>
                <div>
                    <label class="label">Password</label>
                    <input class="input" type="password" name="password" required>
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded"> Remember me
                </label>
                <button class="btn-primary w-full">Sign in</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                New here?
                <a href="{{ route('register') }}" class="font-semibold text-violet-600">Create an account</a>
            </p>
        </div>
    </div>
</x-layouts.app>
