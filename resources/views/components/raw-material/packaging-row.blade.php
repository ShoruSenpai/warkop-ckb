<div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
    <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm font-bold text-ckb-primary">Inventori Bahan Baku</h3>
            <p class="mt-0.5 text-[11px] text-gray-500">Pantau stok dan status bahan</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                type="button"
                id="btn-get-materials"
                class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-gray-200 text-xs text-gray-600 hover:bg-gray-50 focus:ring-1 focus:ring-ckb-accent focus:outline-none transition"
                title="Refresh Data"
            >
                <i class="fas fa-sync-alt"></i>
            </button>

            <input
                id="search-input"
                type="text"
                placeholder="Cari bahan..."
                class="min-w-0 flex-1 rounded-lg border border-gray-200 px-3 py-2 text-xs focus:ring-1 focus:ring-ckb-accent focus:outline-none sm:w-48 sm:flex-none"
            />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[680px] border-collapse text-left">
            <thead>
                <tr
                    class="border-b border-gray-100 text-[10px] uppercase tracking-wider text-gray-400"
                >
                    <th class="px-3 py-2.5 font-medium">ID</th>
                    <th class="px-3 py-2.5 font-medium">Nama Bahan Baku</th>
                    <th class="px-3 py-2.5 font-medium">Satuan Dasar</th>
                    <th class="px-3 py-2.5 font-medium">Stok Saat Ini</th>
                    <th class="px-3 py-2.5 font-medium">Stok Minimum</th>
                    <th class="px-3 py-2.5 font-medium">Status</th>
                    <th class="px-3 py-2.5 text-right font-medium">Aksi</th>
                </tr>
            </thead>

            <tbody id="raw-material-list">
                <tr>
                    <td colspan="7" class="px-3 py-6 text-center text-xs text-gray-400">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Memuat data bahan baku...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
