<div class="mx-auto max-w-md">
    <div class="rounded-xl border border-gray-200 bg-white p-8 shadow-sm">
        <h1 class="mb-1 text-2xl font-bold">Criar conta</h1>
        <p class="mb-6 text-sm text-gray-500">Abra sua carteira financeira.</p>

        <form wire:submit="register" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                <input id="name" type="text" wire:model="name" autocomplete="name"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                <input id="email" type="email" wire:model="email" autocomplete="username"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Senha</label>
                <input id="password" type="password" wire:model="password" autocomplete="new-password"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar senha</label>
                <input id="password_confirmation" type="password" wire:model="password_confirmation" autocomplete="new-password"
                       class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
            </div>

            <button type="submit" wire:loading.attr="disabled" wire:target="register"
                    class="w-full rounded-md bg-indigo-600 px-4 py-2 font-medium text-white transition hover:bg-indigo-500 disabled:opacity-60">
                <span wire:loading.remove wire:target="register">Criar conta</span>
                <span wire:loading wire:target="register">Criando...</span>
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-gray-600">
            Já tem conta?
            <a href="{{ route('login') }}" wire:navigate class="font-medium text-indigo-600 hover:underline">Entrar</a>
        </p>
    </div>
</div>
