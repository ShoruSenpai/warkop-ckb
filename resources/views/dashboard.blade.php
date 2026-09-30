<x-layout title="Dashboard">
    <div class="space-y-6">
        {{-- Title & Shift info --}}
        <div>
            <h2 class="text-2xl font-bold text-ckb-primary">Dashboard</h2>
            <p class="text-sm text-gray-500">
                {{ \Carbon\Carbon::now()->translatedFormat("l, d M Y") }} — Shift Pagi 06:00-14:00
            </p>
        </div>

        {{-- Dynamic Statistic Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Omzet Hari Ini</p>
                <h3 id="dash-today-omzet" class="text-xl font-bold text-ckb-primary">Rp 0</h3>
                <p id="dash-omzet-diff" class="text-[10px] text-green-600 font-medium mt-1">+0% vs kemarin</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Transaksi</p>
                <h3 id="dash-today-tx" class="text-xl font-bold text-ckb-primary">0</h3>
                <p id="dash-tx-diff" class="text-[10px] text-green-600 font-medium mt-1">+0 dari kemarin</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Produk Aktif</p>
                <h3 id="dash-active-products" class="text-xl font-bold text-ckb-primary">0</h3>
                <p id="dash-total-products" class="text-[10px] text-gray-400 mt-1">dari 0 total</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Bahan Stok Kritis</p>
                <h3 id="dash-critical-stock" class="text-xl font-bold text-ckb-primary">0</h3>
                <a href="{{ route('reports.stock') }}" class="text-[10px] text-red-500 font-medium mt-1 hover:underline block">perlu restok segera →</a>
            </div>
        </div>

        {{-- Area Grafik & Data Laporan --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Chart Box --}}
            <div class="lg:col-span-2 bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                    <h3 class="font-bold text-base text-ckb-primary">Grafik Penjualan (7 Hari Terakhir)</h3>
                    <span class="text-xs text-gray-400">Total Omzet Harian</span>
                </div>
                
                <div id="chart-container" class="h-64 w-full flex items-end justify-between gap-3 pt-6 pb-2 px-4 bg-gray-50/50 rounded-lg">
                    <div class="w-full text-center text-gray-400 text-sm py-20">
                        <i class="fas fa-spinner fa-spin mr-2"></i> Memuat grafik...
                    </div>
                </div>
            </div>

            {{-- Recent Transactions Shortcut --}}
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-4 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-3">
                        <h3 class="font-bold text-base text-ckb-primary">Transaksi Terakhir</h3>
                        <a href="{{ route('reports.transactions') }}" class="text-xs font-semibold text-ckb-secondary hover:underline">Lihat Semua</a>
                    </div>
                    <ul id="recent-tx-list" class="divide-y divide-gray-100 space-y-2">
                        <li class="py-4 text-center text-xs text-gray-400">Memuat transaksi...</li>
                    </ul>
                </div>

                {{-- Quick links --}}
                <div class="pt-3 border-t border-gray-100 grid grid-cols-2 gap-2 text-center text-xs">
                    <a href="{{ route('products.index') }}" class="p-2 rounded bg-gray-50 hover:bg-gray-100 font-semibold text-gray-700">
                        <i class="fas fa-box text-ckb-primary block mb-1"></i> Produk
                    </a>
                    <a href="{{ route('supplier.index') }}" class="p-2 rounded bg-gray-50 hover:bg-gray-100 font-semibold text-gray-700">
                        <i class="fas fa-truck text-ckb-primary block mb-1"></i> Restok
                    </a>
                </div>
            </div>
        </div>
    </div>

    <x-slot:script>
        @vite("resources/js/reports/dashboard.js")
    </x-slot:script>
</x-layout>
