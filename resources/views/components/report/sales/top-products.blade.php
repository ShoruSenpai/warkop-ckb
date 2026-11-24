<section
    class="flex min-w-0 flex-col rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface-container-lowest p-4 shadow-surface"
    aria-labelledby="top-products-title"
    data-sales-report-card="top-products"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Produk terlaris</p>
            <h2
                id="top-products-title"
                class="mt-1 text-sm font-bold text-ckb-primary"
            >
                Performa produk
            </h2>
        </div>
        <x-report.segmented-filter
            group="sales-top-products-metric"
            label="Urutkan produk terlaris"
            :options="['quantity' => 'Terjual', 'revenue' => 'Omzet']"
            selected="quantity"
        />
    </div>
    <p data-product-list-summary class="mt-2 text-xs text-ckb-on-surface-variant">Maksimal lima produk</p>
    <button type="button" data-sales-retry="top-products" hidden class="mt-2 min-h-9 cursor-pointer self-start rounded-lg border border-ckb-outline-variant px-3 text-xs font-semibold">Coba lagi</button>
    <ol
        data-top-products
        class="mt-2 flex flex-1 flex-col divide-y divide-ckb-outline-variant/40"
        aria-live="polite"
    >
        <li
            class="flex min-h-40 flex-1 flex-col items-center justify-center px-3 py-6 text-center"
        >
            <i
                class="fas fa-chart-line mb-3 text-xl text-ckb-outline"
                aria-hidden="true"
            ></i>
            <p class="text-xs font-semibold text-ckb-primary">Belum ada data produk terjual.</p>
            <p class="mt-1 text-xs text-ckb-on-surface-variant">Daftar peringkat akan muncul saat sumber transaksi tersedia.</p>
        </li>
    </ol>
</section>
