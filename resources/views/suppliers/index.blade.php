<x-layout title="Pembelian Supplier">
    <div
        class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
    >
        <div class="min-w-0">
            <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-ckb-on-surface-variant">Inventori Operasional</p>
            <h2
                class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
            >
                Input Pembelian Supplier
            </h2>

            <p class="mt-1 text-xs text-ckb-on-surface-variant">Catat nota belanja dan update stok secara otomatis.</p>
        </div>
    </div>

    <form id="form-purchase" class="space-y-4">
        <x-supplier.invoice-info />
        <div
            class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface"
        >
            <div
                class="mb-4 flex flex-col gap-3 border-b border-ckb-outline-variant/50 pb-3 sm:flex-row sm:items-center sm:justify-between"
            >
                <h3 class="text-sm font-bold text-ckb-primary">
                    Daftar Item Belanja
                </h3>
                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        id="btn-refresh-purchase"
                        class="inline-flex min-h-11 min-w-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 text-xs text-ckb-primary transition hover:bg-ckb-surface-container-low focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
                        title="Refresh Data"
                        aria-label="Muat ulang data pembelian"
                    >
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button
                        type="button"
                        id="btn-add-row"
                        class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-low px-3.5 py-2 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container hover:text-ckb-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
                    >
                        + Tambah Baris
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table
                    id="purchase-table"
                    class="w-full min-w-[960px] border-collapse text-left"
                >
                    <thead>
                        <tr
                            class="border-b border-ckb-outline-variant/50 text-[10px] uppercase tracking-wider text-ckb-on-surface-variant"
                        >
                            <th class="w-64 px-3 py-2.5 font-medium">
                                Nama Barang *
                            </th>
                            <th class="w-32 px-3 py-2.5 font-medium">
                                Satuan Beli *
                            </th>
                            <th class="w-20 px-3 py-2.5 font-medium">
                                Jumlah *
                            </th>
                            <th class="w-44 px-3 py-2.5 font-medium">
                                Konversi
                            </th>
                            <th class="w-36 px-3 py-2.5 font-medium">
                                Harga Satuan *
                            </th>
                            <th class="w-36 px-3 py-2.5 font-medium">
                                Subtotal
                            </th>
                            <th
                                class="w-10 px-3 py-2.5 text-center font-medium"
                            >
                                Aksi
                            </th>
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
