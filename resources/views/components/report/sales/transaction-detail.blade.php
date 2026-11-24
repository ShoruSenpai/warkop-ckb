<dialog
    data-transaction-dialog
    aria-labelledby="transaction-detail-title"
    class="m-auto max-h-[min(90dvh,760px)] w-[calc(100%-2rem)] max-w-3xl overflow-hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface-container-lowest p-0 text-ckb-on-surface shadow-floating backdrop:bg-ckb-inverse-surface/50"
>
    <div
        class="flex items-start justify-between gap-4 border-b border-ckb-outline-variant/50 px-4 py-4 sm:px-6"
    >
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Rincian transaksi</p>
            <h2
                id="transaction-detail-title"
                class="mt-1 text-base font-bold text-ckb-primary"
            >
                Detail Penjualan
            </h2>
            <p data-detail-invoice class="mt-1 text-xs text-ckb-on-surface-variant">—</p>
            <button
                type="button"
                data-detail-error
                hidden
                class="mt-2 min-h-9 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold"
            >Coba lagi</button>
        </div>
        <button
            type="button"
            data-detail-close
            aria-label="Tutup detail transaksi"
            class="inline-flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-lg text-ckb-on-surface-variant transition hover:bg-ckb-surface-container-low hover:text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
        >
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <div class="max-h-[calc(min(90dvh,760px)-76px)] overflow-y-auto p-4 sm:p-6">
        <dl
            class="grid grid-cols-1 gap-x-5 gap-y-4 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">
                    Tanggal transaksi
                </dt>
                <dd
                    data-detail-field="transaction_time"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                ></dd>
            </div>
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">Kasir</dt>
                <dd
                    data-detail-field="cashier"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                ></dd>
            </div>
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">
                    Jenis pesanan
                </dt>
                <dd
                    data-detail-field="order_type"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                >
                    —
                </dd>
            </div>
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">
                    Metode pembayaran
                </dt>
                <dd
                    data-detail-field="payment_method"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                ></dd>
            </div>
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">
                    Status transaksi
                </dt>
                <dd
                    data-detail-field="status"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                ></dd>
            </div>
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">Status pembayaran</dt>
                <dd
                    data-detail-field="payment_status"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                ></dd>
            </div>
            <div>
                <dt class="text-xs text-ckb-on-surface-variant">Potongan</dt>
                <dd
                    data-detail-field="discount_amount"
                    class="mt-1 text-xs font-semibold text-ckb-primary"
                ></dd>
            </div>
        </dl>

        <section
            class="mt-4 overflow-hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface shadow-surface"
            aria-labelledby="transaction-items-title"
        >
            <div
                class="flex flex-wrap items-end justify-between gap-3 border-b border-ckb-outline-variant/50 px-4 py-3"
            >
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Detail item</p>
                    <h3
                        id="transaction-items-title"
                        class="mt-1 text-sm font-bold text-ckb-primary"
                    >
                        Produk dalam transaksi
                    </h3>
                </div>
                <p data-detail-subtotal class="text-xs font-semibold text-ckb-on-surface-variant"></p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] border-collapse text-left">
                    <thead
                        class="border-b border-ckb-outline-variant/50 bg-ckb-surface-container-low text-[10px] font-medium uppercase tracking-wider text-ckb-on-surface-variant"
                    >
                        <tr>
                            <th scope="col" class="px-3 py-2.5">Produk</th>
                            <th scope="col" class="px-3 py-2.5 text-right">
                                Qty
                            </th>
                            <th scope="col" class="px-3 py-2.5 text-right">
                                Harga satuan
                            </th>
                            <th scope="col" class="px-3 py-2.5 text-right">
                                Subtotal
                            </th>
                        </tr>
                    </thead>
                    <tbody data-detail-items></tbody>
                </table>
            </div>
        </section>

        <div
            class="mt-4 flex flex-col gap-2 rounded-xl bg-ckb-primary p-4 text-ckb-on-primary sm:ml-auto sm:max-w-sm"
        >
            <div class="flex items-center justify-between gap-4 text-xs">
                <span>Total kotor</span>
                <span data-detail-gross class="font-semibold"></span>
            </div>
            <div class="flex items-center justify-between gap-4 text-xs">
                <span>Diskon</span>
                <span data-detail-discount class="font-semibold"></span>
            </div>
            <div
                class="flex items-center justify-between gap-4 border-t border-ckb-on-primary/20 pt-2 text-sm font-bold"
            >
                <span>Total akhir</span>
                <span data-detail-total></span>
            </div>
        </div>
    </div>
</dialog>

<template id="sales-detail-item-template">
    <tr
        class="border-b border-ckb-outline-variant/40 text-xs text-ckb-on-surface"
    >
        <td data-detail-item="name" class="px-3 py-3"></td>
        <td data-detail-item="quantity" class="px-3 py-3 text-right"></td>
        <td
            data-detail-item="unit_price"
            class="whitespace-nowrap px-3 py-3 text-right"
        ></td>
        <td
            data-detail-item="subtotal"
            class="whitespace-nowrap px-3 py-3 text-right font-semibold text-ckb-primary"
        ></td>
    </tr>
</template>
