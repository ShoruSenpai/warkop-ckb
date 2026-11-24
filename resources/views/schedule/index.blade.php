<x-layout title="Jadwal Karyawan">
    <div
        id="schedule-app"
        data-role="{{ auth()->user()->role }}"
        data-current-date="{{ now()->toDateString() }}"
        class="mx-auto max-w-[1600px] space-y-4"
    >
        <section
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
            <div class="min-w-0">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-ckb-on-surface-variant">Operasional Tim</p>
                <h1
                    class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
                >
                    Jadwal Karyawan
                </h1>
                <p class="mt-1 text-xs text-ckb-on-surface-variant">Atur dan pantau penugasan kerja karyawan setiap minggu.</p>
            </div>

            @if (auth()->user()->role === "owner")
                <button
                    type="button"
                    id="btn-add-schedule"
                    class="inline-flex min-h-11 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-primary px-3.5 py-2 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent hover:text-ckb-on-accent sm:text-sm motion-reduce:transition-none"
                >
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    Tambah Jadwal
                </button>
            @endif
        </section>

        <aside
            class="flex items-start gap-3 rounded-xl border border-ckb-warning/30 bg-ckb-warning-container px-3.5 py-3 text-ckb-on-warning-container"
            role="note"
        >
            <i
                class="fas fa-info-circle mt-0.5 shrink-0"
                aria-hidden="true"
            ></i>
            <div>
                <p class="text-xs font-semibold">Mode pratinjau</p>
                <p class="mt-0.5 text-xs">Data contoh berjalan di browser. Perubahan jadwal hanya tersimpan sementara dan tidak masuk ke database.</p>
            </div>
        </aside>

        <section
            aria-label="Ringkasan jadwal"
            class="grid grid-cols-2 gap-3 xl:grid-cols-5"
        >
            <article
                class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3.5 shadow-surface"
            >
                <p class="text-[11px] font-medium text-ckb-on-surface-variant">Karyawan Terjadwal</p>
                <p id="summary-employees" class="mt-2 text-2xl font-bold leading-none text-ckb-primary">—</p>
                <p class="mt-1 text-[10px] text-ckb-on-surface-variant">orang periode aktif</p>
            </article>
            <article
                class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3.5 shadow-surface"
            >
                <p class="text-[11px] font-medium text-ckb-on-surface-variant">Total Penugasan</p>
                <p id="summary-assignments" class="mt-2 text-2xl font-bold leading-none text-ckb-primary">—</p>
                <p class="mt-1 text-[10px] text-ckb-on-surface-variant">jadwal periode aktif</p>
            </article>
            <article
                class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3.5 shadow-surface"
            >
                <p class="text-[11px] font-medium text-ckb-on-surface-variant">Penugasan Barista</p>
                <p id="summary-barista" class="mt-2 text-2xl font-bold leading-none text-ckb-accent">—</p>
                <p class="mt-1 text-[10px] text-ckb-on-surface-variant">periode aktif</p>
            </article>
            <article
                class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3.5 shadow-surface"
            >
                <p class="text-[11px] font-medium text-ckb-on-surface-variant">Penugasan Kasir</p>
                <p id="summary-cashier" class="mt-2 text-2xl font-bold leading-none text-ckb-success">—</p>
                <p class="mt-1 text-[10px] text-ckb-on-surface-variant">periode aktif</p>
            </article>
            <article
                class="col-span-2 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3.5 shadow-surface xl:col-span-1"
            >
                <p class="text-[11px] font-medium text-ckb-on-surface-variant">Penugasan Kitchen</p>
                <p id="summary-kitchen" class="mt-2 text-2xl font-bold leading-none text-ckb-warning">—</p>
                <p class="mt-1 text-[10px] text-ckb-on-surface-variant">periode aktif</p>
            </article>
        </section>

        <section
            class="space-y-3 rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-3.5 shadow-surface sm:p-4"
            aria-label="Navigasi dan filter jadwal"
        >
            <div
                class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
            >
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        id="btn-prev-week"
                        aria-label="Minggu sebelumnya"
                        class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface text-ckb-primary transition hover:bg-ckb-surface-container-low motion-reduce:transition-none"
                    >
                        <i class="fas fa-arrow-left" aria-hidden="true"></i>
                    </button>
                    <button
                        type="button"
                        id="btn-current-week"
                        class="inline-flex min-h-11 items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 text-xs font-semibold text-ckb-primary transition hover:bg-ckb-surface-container-low motion-reduce:transition-none"
                    >
                        Minggu ini
                    </button>
                    <button
                        type="button"
                        id="btn-next-week"
                        aria-label="Minggu berikutnya"
                        class="inline-flex size-11 shrink-0 items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface text-ckb-primary transition hover:bg-ckb-surface-container-low motion-reduce:transition-none"
                    >
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </button>
                    <h2
                        id="week-label"
                        class="ml-1 text-sm font-bold text-ckb-on-surface sm:ml-2"
                    >
                        Memuat minggu...
                    </h2>
                </div>

                <div
                    class="grid min-w-0 grid-cols-1 gap-2 sm:grid-cols-[minmax(12rem,1fr)_minmax(10rem,auto)] lg:w-[min(100%,32rem)]"
                >
                    <label class="relative min-w-0">
                        <span class="sr-only"
                            >Cari nama atau kode karyawan</span
                        >
                        <i
                            class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-ckb-outline"
                            aria-hidden="true"
                        ></i>
                        <input
                            type="search"
                            id="schedule-search"
                            placeholder="Cari karyawan..."
                            class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 pl-9 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                        />
                    </label>
                    <label>
                        <span class="sr-only">Filter posisi karyawan</span>
                        <select
                            id="position-filter"
                            class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                        >
                            <option value="">Semua Posisi</option>
                        </select>
                    </label>
                </div>
            </div>

            <div
                id="active-filter"
                class="hidden items-center gap-2 border-t border-ckb-outline-variant/40 pt-3 text-[11px] text-ckb-on-surface-variant"
            >
                <i class="fas fa-filter" aria-hidden="true"></i>
                <span id="active-filter-label"></span>
                <button
                    type="button"
                    id="btn-clear-filters"
                    class="ml-auto min-h-9 px-2 font-semibold text-ckb-accent hover:underline"
                >
                    Hapus filter
                </button>
            </div>
        </section>

        <div
            id="schedule-status"
            class="hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-8 text-center"
            role="status"
            aria-live="polite"
        ></div>

        <section
            id="schedule-week"
            class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7"
            aria-label="Jadwal kerja minggu ini"
        >
            <div
                class="col-span-full rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-10 text-center text-sm text-ckb-on-surface-variant"
                role="status"
            >
                <i class="fas fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                Memuat jadwal...
            </div>
        </section>
    </div>

    @if (auth()->user()->role === "owner")
        <x-schedule.form-modal />
    @endif
    <x-schedule.detail-modal :is-owner="auth()->user()->role === 'owner'" />

    <x-slot:script>
        @vite ("resources/js/schedule/index.js")
    </x-slot:script>
</x-layout>
