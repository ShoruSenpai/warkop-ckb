<section
    class="min-w-0 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface-container-lowest p-4 shadow-surface xl:col-span-2"
    aria-labelledby="sales-trend-title"
    data-sales-report-card="trend-chart"
>
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div class="min-w-0 space-y-3">
            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-ckb-on-surface-variant">Tren penjualan</p>
            <h2
                id="sales-trend-title"
                data-chart-title
                class="mt-1 text-sm font-bold text-ckb-primary"
            >
                Omzet penjualan
            </h2>
            <x-report.segmented-filter
                group="sales-trend-period"
                label="Periode grafik penjualan"
                :options="[
                    'today' => 'Hari Ini',
                    'week' => 'Minggu Ini',
                    'month' => 'Bulan Ini',
                    'year' => 'Tahun Ini',
                    'custom' => 'Kustom',
                ]"
                selected="month"
            />
            <div
                data-custom-period
                class="grid w-full grid-cols-2 gap-2 sm:w-auto"
                hidden
            >
                <label
                    class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
                >
                    Dari tanggal
                    <input
                        type="date"
                        data-period-start
                        class="min-h-11 min-w-0 rounded-lg border border-ckb-outline-variant bg-ckb-surface px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
                    />
                </label>
                <label
                    class="flex min-w-0 flex-col gap-1 text-[10px] font-semibold text-ckb-on-surface-variant"
                >
                    Sampai tanggal
                    <input
                        type="date"
                        data-period-end
                        class="min-h-11 min-w-0 rounded-lg border border-ckb-outline-variant bg-ckb-surface px-2 text-xs text-ckb-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent"
                    />
                </label>
            </div>
        </div>
        <x-report.segmented-filter
            group="sales-trend-metric"
            label="Metrik tren penjualan"
            :options="['revenue' => 'Omzet', 'transactions' => 'Transaksi']"
            selected="revenue"
        />
    </div>

    <p class="mt-2 text-[10px] text-ckb-on-surface-variant">Perbandingan periode sebelumnya belum tersedia.</p>

    <div
        data-chart-panel
        data-chart-state="empty"
        class="mt-4"
        aria-busy="false"
    >
        <div class="relative h-56 sm:h-60">
            <canvas
                data-sales-chart
                role="img"
                aria-label="Grafik garis tren penjualan"
                aria-describedby="sales-chart-state"
            ></canvas>
            <div
                data-chart-empty
                class="pointer-events-none absolute inset-0 flex items-center justify-center px-4 text-center"
            >
                <p data-chart-empty-message class="max-w-sm rounded-lg bg-ckb-surface-container-low px-4 py-3 text-xs text-ckb-on-surface-variant">Grafik akan menampilkan data setelah sumber transaksi tersedia.</p>
            </div>
        </div>
        <p
            id="sales-chart-state"
            data-chart-status
            class="mt-2 min-h-5 text-center text-xs text-ckb-on-surface-variant"
            role="status"
        >Belum ada data penjualan pada periode ini.</p>
        <div class="flex items-center justify-center gap-2 text-xs text-ckb-on-error-container" data-chart-error hidden>
            <span>Grafik penjualan tidak dapat dimuat.</span>
            <button type="button" data-sales-retry="trend-chart" class="min-h-9 cursor-pointer rounded-lg border border-ckb-outline-variant px-3 font-semibold">Coba lagi</button>
        </div>
    </div>
</section>
