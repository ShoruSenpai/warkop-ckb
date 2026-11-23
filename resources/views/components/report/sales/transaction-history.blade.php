<section
    class="min-w-0 overflow-hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface-container-lowest shadow-surface"
    aria-labelledby="sales-history-title"
    data-sales-report-card="transaction-history"
>
    <div
        class="flex flex-col gap-3 border-b border-ckb-outline-variant/50 px-4 py-3 sm:flex-row sm:items-end sm:justify-between"
    >
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Transaksi</p>
            <h2
                id="sales-history-title"
                class="mt-1 text-sm font-bold text-ckb-primary"
            >
                Riwayat Penjualan
            </h2>
            <p
                data-transaction-summary
                class="mt-1 text-xs text-ckb-on-surface-variant"
                role="status"
            >Data transaksi belum tersedia dari backend.</p>
        </div>
        <div class="flex flex-wrap gap-2 self-start sm:self-auto">
            <button
                type="button"
                data-reset-filters
                class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container-low focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
            >
                Reset filter
            </button>
            <button
                type="button"
                data-apply-filters
                class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-primary px-3 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-primary/90 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
            >
                Terapkan filter
            </button>
        </div>
    </div>

    <div
        class="grid grid-cols-1 gap-3 border-b border-ckb-outline-variant/50 bg-ckb-surface p-4 sm:grid-cols-2 xl:grid-cols-8"
    >
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant sm:col-span-2"
        >
            Cari invoice atau kasir
            <span class="relative">
                <i
                    class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-ckb-outline"
                    aria-hidden="true"
                ></i>
                <input
                    type="search"
                    data-filter-search
                    data-filter-control
                    placeholder="Nomor invoice atau nama kasir"
                    class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest pl-9 pr-3 text-xs text-ckb-primary placeholder:text-ckb-on-surface-variant focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
                />
            </span>
        </label>
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
        >
            Dari tanggal
            <input
                type="date"
                data-filter-start
                data-filter-control
                class="min-h-11 min-w-0 rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
            />
        </label>
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
        >
            Sampai tanggal
            <input
                type="date"
                data-filter-end
                data-filter-control
                class="min-h-11 min-w-0 rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
            />
        </label>
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
        >
            Status transaksi
            <select
                data-filter-status
                data-filter-control
                class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
            >
                <option value="">Semua status</option>
            </select>
        </label>
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
        >
            Status pembayaran
            <select
                data-filter-payment-status
                data-filter-control
                class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
            >
                <option value="">Semua status pembayaran</option>
            </select>
        </label>
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
        >
            Metode pembayaran
            <select
                data-filter-payment
                data-filter-control
                class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
            >
                <option value="">Semua metode</option>
            </select>
        </label>
        <label
            class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
        >
            Kasir
            <select
                data-filter-cashier
                data-filter-control
                class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-lowest px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
            >
                <option value="">Semua kasir</option>
            </select>
        </label>
    </div>

    <p
        data-filter-draft-notice
        hidden
        class="border-b border-ckb-outline-variant/50 bg-ckb-secondary-container/50 px-4 py-2 text-xs text-ckb-on-surface-variant"
        role="status"
    >Filter berubah dan belum diterapkan. Klik “Terapkan filter” untuk memuat riwayat.</p>

    <div
        data-transaction-panel
        data-transaction-state="empty"
        aria-busy="false"
    >
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] border-collapse text-left">
                <thead
                    class="border-b border-ckb-outline-variant/50 bg-ckb-surface-container-low text-[10px] font-medium uppercase tracking-wider text-ckb-on-surface-variant"
                >
                    <tr>
                        <th scope="col" class="px-3 py-3">Tanggal dan waktu</th>
                        <th scope="col" class="px-3 py-3">Nomor invoice</th>
                        <th scope="col" class="px-3 py-3">Kasir</th>
                        <th scope="col" class="px-3 py-3">Jenis pesanan</th>
                        <th scope="col" class="px-3 py-3">Metode pembayaran</th>
                        <th scope="col" class="px-3 py-3 text-right">
                            Total transaksi
                        </th>
                        <th scope="col" class="px-3 py-3">Status transaksi</th>
                        <th scope="col" class="px-3 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody data-transaction-list aria-live="polite">
                    <tr data-transaction-empty>
                        <td colspan="8" class="px-4 py-10 text-center">
                            <i
                                class="fas fa-file-invoice-dollar mb-3 text-2xl text-ckb-outline"
                                aria-hidden="true"
                            ></i>
                            <p class="text-xs font-semibold text-ckb-primary">Belum ada data riwayat penjualan.</p>
                            <p class="mt-1 text-xs text-ckb-on-surface-variant">Daftar transaksi akan tampil setelah sumber laporan tersedia.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            data-transaction-pagination
            class="flex flex-col gap-3 border-t border-ckb-outline-variant/50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                <p data-pagination-status class="text-xs text-ckb-on-surface-variant">0 transaksi</p>
                <label
                    class="inline-flex min-h-11 items-center gap-2 text-xs text-ckb-on-surface-variant"
                >
                    Baris per halaman
                    <select
                        data-page-size
                        data-filter-control
                        class="min-h-9 cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface px-2 text-xs font-semibold text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
                    >
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </label>
            </div>
            <div class="flex gap-2">
                <button
                    type="button"
                    data-page-previous
                    disabled
                    class="min-h-11 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container-low disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                >
                    Sebelumnya
                </button>
                <button
                    type="button"
                    data-page-next
                    disabled
                    class="min-h-11 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container-low disabled:cursor-not-allowed disabled:opacity-50 motion-reduce:transition-none"
                >
                    Berikutnya
                </button>
            </div>
        </div>
    </div>

    <div
        data-transaction-loading
        class="hidden border-t border-ckb-outline-variant/50 px-4 py-8 text-center text-xs text-ckb-on-surface-variant"
        role="status"
    >
        Memuat riwayat penjualan...
    </div>
    <div
        data-transaction-error
        class="hidden border-t border-ckb-outline-variant/50 bg-ckb-error-container px-4 py-4 text-xs text-ckb-on-error-container"
        role="alert"
    >
        <span data-transaction-error-message
            >Riwayat penjualan tidak dapat dimuat.</span
        >
        <button
            type="button"
            data-sales-retry="transaction-history"
            class="ml-2 min-h-9 cursor-pointer rounded-lg border border-ckb-on-error-container/30 px-3 font-semibold underline"
        >
            Coba lagi
        </button>
    </div>

    <template id="sales-transaction-row-template">
        <x-report.sales.transaction-row />
    </template>
</section>
