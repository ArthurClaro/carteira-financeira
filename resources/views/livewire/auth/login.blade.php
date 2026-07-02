<div class="mx-auto mt-8 max-w-md">
    <div class="animate-fade-up mb-6 flex items-center justify-center gap-2 text-xl font-semibold text-brand-800">
        <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 8.25A2.25 2.25 0 0 1 5.25 6h13.5A2.25 2.25 0 0 1 21 8.25v8.5A2.25 2.25 0 0 1 18.75 19H5.25A2.25 2.25 0 0 1 3 16.75v-8.5Z"/>
            <path stroke-linecap="round" d="M3 10h18"/>
            <circle cx="17" cy="14.5" r="1.4" fill="currentColor" stroke="none"/>
        </svg>
        {{ config('app.name') }}
    </div>

    <div class="animate-fade-up stagger-1 rounded-xl border border-brand-100 bg-white p-8 shadow-sm">
        <h1 class="mb-1 text-2xl font-bold">Entrar</h1>
        <p class="mb-6 text-sm text-gray-500">Acesse sua carteira financeira.</p>

        <form wire:submit="login" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" wire:model="email" autocomplete="username"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                @error('email') <p class="animate-slide-in mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input id="password" type="password" wire:model="password" autocomplete="current-password"
                       class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm transition focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/30">
                @error('password') <p class="animate-slide-in mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                Manter conectado
            </label>

            <button type="submit" wire:loading.attr="disabled" wire:target="login"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-brand-700 px-4 py-2 font-medium text-white shadow-sm transition-all hover:-translate-y-px hover:bg-brand-600 hover:shadow active:translate-y-0 active:scale-[.98] disabled:opacity-60 disabled:hover:translate-y-0">
                <svg wire:loading wire:target="login" class="size-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                </svg>
                <span wire:loading.remove wire:target="login">Entrar</span>
                <span wire:loading wire:target="login">Entrando...</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Não tem conta?
            <a href="{{ route('register') }}" wire:navigate class="font-medium text-brand-700 hover:underline">Criar conta</a>
        </p>
    </div>
</div>
