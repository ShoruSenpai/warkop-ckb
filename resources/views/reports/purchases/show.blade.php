<x-layout title="Detail Invoice Pembelian">
    <div
        class="mx-auto w-full max-w-[1440px] space-y-4"
        data-purchase-detail
        data-purchase-id="{{ $purchaseId }}"
    >
        <header>
            <a
                href="{{ route('reports.purchases.invoices') }}"
                class="inline-flex min-h-11 cursor-pointer items-center gap-2 text-xs font-semibold text-ckb-primary transition hover:text-ckb-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
                Kembali ke rekap invoice
            </a>
            <div class="mt-3 flex flex-wrap items-end gap-2" data-status-controls hidden>
                <label class="flex min-h-11 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant">
                    Ubah status
                    <select data-status-select class="min-h-11 cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent">
                        <option value="pending">Menunggu</option>
                        <option value="received">Diterima</option>
                    </select>
                </label>
                <button type="button" data-status-save class="inline-flex min-h-11 cursor-pointer items-center rounded-lg bg-ckb-primary px-4 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none">
                    Simpan status
                </button>
            </div>
            <p class="mt-4 text-[10px] font-bold uppercase tracking-[0.14em] text-ckb-on-surface-variant">Rincian pembelian</p>
            <h1
                class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
            >
                Detail Invoice
            </h1>
            <p data-detail-state class="mt-1 text-xs text-ckb-on-surface-variant" role="status">Memuat detail invoice...</p>
        </header>

        <div data-detail-content class="space-y-4" hidden>
            <section
                class="grid grid-cols-1 gap-3 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface sm:grid-cols-2 lg:grid-cols-4"
                aria-label="Informasi invoice"
            >
                <div>
                    <p class="text-xs text-ckb-on-surface-variant">Nomor invoice</p>
                    <p data-purchase-field="invoice_number" class="mt-1 text-xs font-semibold text-ckb-primary sm:text-sm"></p>
                </div>
                <div>
                    <p class="text-xs text-ckb-on-surface-variant">Nama supplier</p>
                    <p data-purchase-field="supplier_name" class="mt-1 text-xs font-semibold text-ckb-primary sm:text-sm"></p>
                </div>
                <div>
                    <p class="text-xs text-ckb-on-surface-variant">Tanggal pembelian</p>
                    <p data-purchase-field="purchase_date" class="mt-1 text-xs font-semibold text-ckb-primary sm:text-sm"></p>
                </div>
                <div>
                    <p class="text-xs text-ckb-on-surface-variant">Status</p>
                    <p data-purchase-field="status" class="mt-1 text-xs font-semibold text-ckb-primary sm:text-sm"></p>
                </div>
            </section>

            <section
                class="overflow-hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface shadow-surface"
                aria-labelledby="invoice-items-title"
            >
                <div
                    class="flex flex-wrap items-end justify-between gap-3 border-b border-ckb-outline-variant/50 px-4 py-3"
                >
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Isi invoice</p>
                        <h2
                            id="invoice-items-title"
                            class="mt-1 text-sm font-bold text-ckb-primary"
                        >
                            Item pembelian
                        </h2>
                    </div>
                    <p data-purchase-total class="text-base font-bold text-ckb-primary sm:text-lg"></p>
                </div>
                <div class="overflow-x-auto">
                    <table
                        class="w-full min-w-[640px] border-collapse text-left"
                    >
                        <thead
                            class="border-b border-ckb-outline-variant/50 bg-ckb-surface-container-low text-[10px] font-medium uppercase tracking-wider text-ckb-on-surface-variant"
                        >
                            <tr>
                                <th scope="col" class="px-3 py-2.5">
                                    Nama barang
                                </th>
                                <th scope="col" class="px-3 py-2.5">Jumlah</th>
                                <th scope="col" class="px-3 py-2.5 text-right">
                                    Harga satuan
                                </th>
                                <th scope="col" class="px-3 py-2.5 text-right">
                                    Subtotal
                                </th>
                            </tr>
                        </thead>
                        <tbody data-invoice-items aria-live="polite"></tbody>
                    </table>
                </div>
                <template id="invoice-item-row-template"
                    ><x-report.purchase.invoice-item-row
                /></template>
            </section>
        </div>
    </div>

    <x-slot:script>
        @vite ("resources/js/reports/purchase/show.js")
    </x-slot:script>
</x-layout>
