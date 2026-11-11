<x-layout title="Pembelian Supplier">
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-xl font-bold text-ckb-on-surface">
                Input Pembelian Supplier
            </h2>

            <p class="text-sm text-ckb-on-surface-variant">Catat nota belanja dan update stok secara otomatis</p>
        </div>
    </div>

    <form id="form-purchase">
        <x-supplier.invoice-info />
        <div class="bg-ckb-surface rounded-xl shadow-surface border border-ckb-outline-variant/50 p-4">
            <div
                class="flex justify-between items-center mb-4 border-b border-ckb-outline-variant/50 pb-2"
            >
                <h3 class="font-bold text-ckb-primary">Daftar Item Belanja</h3>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="btn-refresh-purchase"
                        class="border border-ckb-outline-variant rounded-lg px-3 py-1.5 text-sm text-ckb-on-surface hover:bg-ckb-surface-container-low focus:ring-1 focus:ring-ckb-accent focus:outline-none transition"
                        title="Refresh Data"
                    >
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button
                        type="button"
                        id="btn-add-row"
                        class="text-sm font-semibold text-ckb-primary hover:text-ckb-accent bg-ckb-surface-container-low px-3 py-1.5 rounded-lg border border-ckb-outline-variant transition"
                    >
                        + Tambah Baris
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table id="purchase-table" class="w-full text-left">
                    <thead>
                        <tr
                            class="text-[10px] text-ckb-on-surface-variant uppercase tracking-wider border-b border-ckb-outline-variant/50"
                        >
                            <th class="pb-2 w-64">Nama Barang *</th>
                            <th class="pb-2 w-32">Satuan Beli *</th>
                            <th class="pb-2 w-20">Jumlah *</th>
                            <th class="pb-2 w-44">Konversi</th>
                            <th class="pb-2 w-36">Harga Satuan *</th>
                            <th class="pb-2 w-36">Subtotal</th>
                            <th class="pb-2 w-10 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="item-list"></tbody>
                </table>
            </div>
            <x-supplier.summary-footer />
        </div>
    </form>

    <template id="row-template">
        <x-supplier.item-row />
    </template>

    <x-slot:script>
        @vite ("resources/js/supplier/index.js")
    </x-slot:script>
</x-layout>
