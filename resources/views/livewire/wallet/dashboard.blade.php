<div class="space-y-8">
    <div class="animate-fade-up">
        <div class="flex items-baseline justify-between">
            <h1 class="text-2xl font-bold">Olá, {{ auth()->user()->name }}</h1>
            <p class="text-sm text-gray-500">{{ now()->translatedFormat('d \d\e F \d\e Y') }}</p>
        </div>

        {{-- Cartão de saldo: pulsa quando qualquer operação dispara 'wallet-updated'. --}}
        <div class="relative mt-4 overflow-hidden rounded-2xl bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 p-6 text-white shadow-lg shadow-brand-900/20"
             x-data="{ pop: false }"
             x-on:wallet-updated.window="pop = false; requestAnimationFrame(() => { pop = true; setTimeout(() => pop = false, 600) })">

            {{-- Guilhoché: anéis concêntricos, como o verso de uma cédula. --}}
            <svg class="pointer-events-none absolute -right-16 -top-24 size-80 text-white/[.07]" viewBox="0 0 200 200" fill="none" aria-hidden="true">
                @for ($r = 15; $r <= 95; $r += 10)
                    <circle cx="100" cy="100" r="{{ $r }}" stroke="currentColor" stroke-width="1"/>
                @endfor
            </svg>

            <div class="relative flex items-start justify-between">
                <p class="text-sm font-medium text-brand-100">Saldo atual</p>
                <span class="rounded-full border border-white/20 bg-white/10 px-2.5 py-0.5 text-xs font-semibold tracking-wide">BRL</span>
            </div>

            <p class="relative mt-2 origin-left text-4xl font-bold tracking-tight tabular-nums {{ $wallet->balance_cents < 0 ? 'text-amber-300' : '' }}"
               :class="pop && 'animate-balance-pop'">
                {{ $wallet->balance()->format() }}
            </p>

            <div class="relative mt-6 flex items-center gap-3">
                {{-- Chip de cartão --}}
                <svg class="h-7 w-9 rounded border border-white/25 bg-gradient-to-br from-amber-200/70 to-amber-400/50" viewBox="0 0 36 28" fill="none" aria-hidden="true">
                    <path d="M12 0v9M24 0v9M12 28v-9M24 28v-9M0 14h12M24 14h12" stroke="rgba(255,255,255,.45)" stroke-width="1.2"/>
                </svg>
                <span class="text-xs font-medium uppercase tracking-[0.18em] text-brand-100">{{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="animate-fade-up stagger-1"><livewire:wallet.deposit-form /></div>
        <div class="animate-fade-up stagger-2"><livewire:wallet.transfer-form /></div>
    </div>

    <div class="animate-fade-up stagger-3">
        <livewire:wallet.transaction-history />
    </div>
</div>
