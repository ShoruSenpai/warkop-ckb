<x-layout title="Laporan Penjualan">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-ckb-primary">Laporan Penjualan</h2>
                <p class="mt-1 text-sm text-ckb-on-surface-variant">Analisis omzet penjualan, tren transaksi, dan produk paling laris.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="date" id="sales-start-date" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 outline-none focus:border-ckb-primary" />
                <span class="text-xs text-gray-400">s/d</span>
                <input type="date" id="sales-end-date" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 outline-none focus:border-ckb-primary" />
                <button type="button" id="btn-refresh-sales" class="px-3 py-2 bg-ckb-primary text-white rounded-lg text-xs font-semibold hover:bg-ckb-accent">
                    Filter
                </button>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Omzet Kotor</p>
                <h3 id="card-gross-revenue" class="text-xl font-bold text-gray-800">Rp 0</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Diskon</p>
                <h3 id="card-discount-amount" class="text-xl font-bold text-red-500">Rp 0</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Omzet Bersih</p>
                <h3 id="card-net-revenue" class="text-xl font-bold text-ckb-primary">Rp 0</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Rata-rata Transaksi</p>
                <h3 id="card-avg-order" class="text-xl font-bold text-gray-800">Rp 0</h3>
            </div>
        </div>

        {{-- Top Selling Products --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <h3 class="font-bold text-base text-ckb-primary">Produk Terlaris (Top 10)</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Ranking</th>
                            <th class="px-4 py-3 font-semibold">Nama Produk</th>
                            <th class="px-4 py-3 font-semibold">Kategori</th>
                            <th class="px-4 py-3 font-semibold">Total Terjual</th>
                            <th class="px-4 py-3 text-right font-semibold">Total Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="top-products-list">
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat produk terlaris...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-slot:script>
        @vite("resources/js/reports/sales.js")
    </x-slot:script>
</x-layout>
