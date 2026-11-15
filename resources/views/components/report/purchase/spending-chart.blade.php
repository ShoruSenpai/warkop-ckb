<section
    class="min-w-0 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface sm:col-span-2 xl:col-span-2"
    aria-labelledby="spending-chart-title"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Tren pengeluaran</p>
            <h3
                id="spending-chart-title"
                class="mt-1 text-sm font-bold text-ckb-primary"
            >
                Pengeluaran pembelian
            </h3>
        </div>
        <x-report.purchase.period-filter
            name="chart"
            label="Periode grafik"
            :options="['month' => '6 Bulan', 'year' => 'Tahun Ini']"
            selected="month"
        />
    </div>

    <div class="mt-4" data-spending-chart aria-busy="true">
        <div class="h-56 sm:h-60">
            <canvas
                data-spending-chart-canvas
                role="img"
                aria-label="Grafik tren pengeluaran pembelian"
                aria-describedby="spending-chart-description"
            ></canvas>
        </div>
        <p id="spending-chart-description" data-chart-state class="mt-2 min-h-5 text-center text-xs text-ckb-on-surface-variant" role="status">Memuat tren pengeluaran...</p>
    </div>
</section>
