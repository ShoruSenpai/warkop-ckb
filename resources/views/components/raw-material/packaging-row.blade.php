<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-ckb-secondary">Inventori Bahan Baku</h3>

        <div class="flex items-center gap-2">
            <button type="button" id="btn-get-materials"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-50 focus:ring-1 focus:ring-ckb-accent focus:outline-none transition"
                    title="Refresh Data">
                <i class="fas fa-sync-alt"></i>
            </button>

            <input id="search-input" type="text" oninput="filterTable()"
                   placeholder="Cari bahan..."
                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-1 focus:ring-ckb-accent focus:outline-none">
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
            <tr class="text-xs text-gray-400 border-b border-gray-100 uppercase tracking-wider">
                <th class="py-3 font-medium">ID</th>
                <th class="py-3 font-medium">Nama Bahan Baku</th>
                <th class="py-3 font-medium">Satuan Dasar</th>
                <th class="py-3 font-medium">Stok Saat Ini</th>
                <th class="py-3 font-medium">Status</th>
                <th class="py-3 font-medium text-right">Aksi</th>
            </tr>
            </thead>

            <tbody id="raw-material-list">
            <tr>
                <td colspan="6" class="py-6 text-center text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Memuat data bahan baku...
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
