@props(['title'])

<header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 shrink-0">
    <div class="flex items-center flex-1">
        <h2 class="text-lg font-semibold text-ckb-primary mr-8">{{ $title }}</h2>

        <div class="relative w-96 hidden md:block">
            <input type="text" placeholder="Cari produk, bahan baku, supplier..."
                   class="w-full pl-4 pr-10 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-ckb-accent">
        </div>
    </div>

    <div class="flex items-center">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex items-center text-sm text-gray-600 hover:text-ckb-error transition-colors px-3 py-1.5 rounded-md hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Keluar
            </button>
        </form>
    </div>
</header>
