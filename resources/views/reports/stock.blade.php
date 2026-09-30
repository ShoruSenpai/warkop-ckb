<x-layout title="Laporan Stok">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-ckb-primary">Laporan Stok & Bahan Baku</h2>
                <p class="mt-1 text-sm text-ckb-on-surface-variant">Ringkasan stok bahan baku, batas minimum restok, dan status persediaan.</p>
            </div>
            <button type="button" id="btn-refresh-stock" class="px-4 py-2 bg-ckb-primary text-white rounded-lg text-xs font-semibold hover:bg-ckb-accent self-start sm:self-auto">
                <i class="fas fa-sync-alt mr-1"></i> Refresh Data
            </button>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Total Jenis Bahan</p>
                <h3 id="card-total-materials" class="text-xl font-bold text-gray-800">0</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-green-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Stok Aman (Normal)</p>
                <h3 id="card-normal-count" class="text-xl font-bold text-green-600">0</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Stok Kritis</p>
                <h3 id="card-critical-count" class="text-xl font-bold text-amber-600">0</h3>
            </div>
            <div class="bg-white p-4 rounded-xl border border-red-100 shadow-sm">
                <p class="text-xs text-gray-500 mb-1">Stok Habis</p>
                <h3 id="card-empty-count" class="text-xl font-bold text-red-600">0</h3>
            </div>
        </div>

        {{-- Filter & Search --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="stock-search" placeholder="Cari nama bahan baku..." class="flex-1 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm outline-none focus:border-ckb-primary" />
            <select id="stock-status-filter" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 outline-none focus:border-ckb-primary">
                <option value="">Semua Status</option>
                <option value="kritis">Stok Kritis</option>
                <option value="habis">Stok Habis</option>
                <option value="normal">Stok Normal</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 space-y-4">
            <h3 class="font-bold text-base text-ckb-primary">Status Bahan Baku</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Bahan Baku</th>
                            <th class="px-4 py-3 font-semibold">Stok Saat Ini</th>
                            <th class="px-4 py-3 font-semibold">Batas Minimum</th>
                            <th class="px-4 py-3 font-semibold">Satuan Dasar</th>
                            <th class="px-4 py-3 font-semibold">Kemasan Tersedia</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody id="stock-materials-list">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-400">
                                <i class="fas fa-spinner fa-spin mr-2"></i> Memuat laporan stok...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-slot:script>
        @vite("resources/js/reports/stock.js")
    </x-slot:script>
</x-layout>
