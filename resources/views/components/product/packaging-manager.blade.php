<section
    id="packaging-section"
    class="hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface"
>
    <div
        class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
    >
        <div>
            <h3 class="text-sm font-bold text-ckb-primary">Satuan Pembelian</h3>

            <p class="mt-1 text-xs text-ckb-on-surface-variant">Atur satuan yang dapat digunakan saat pembelian supplier.</p>
        </div>

        <button
            type="button"
            id="btn-add-packaging"
            class="inline-flex min-h-11 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-secondary px-3.5 py-2 text-xs font-semibold text-ckb-on-secondary transition hover:bg-ckb-secondary-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
        >
            <i class="fas fa-plus"></i>
            Tambah Satuan
        </button>
    </div>

    <div id="packaging-list" class="space-y-2"></div>

    <div
        id="packaging-empty"
        class="rounded-lg bg-ckb-surface-container-low px-4 py-8 text-center"
    >
        <i class="fas fa-box-open text-xl text-ckb-outline"></i>

        <p class="mt-2 text-sm font-semibold text-ckb-primary">Belum ada satuan pembelian</p>

        <p class="mt-1 text-xs text-ckb-on-surface-variant">Tambahkan satuan seperti pcs, dus, karton, dan sebagainya.</p>
    </div>
</section>

{{-- Modal kecil khusus packaging --}}
<div
    id="packaging-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="packaging-modal-title"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-ckb-inverse-surface/60 px-4"
>
    <div
        class="max-h-[calc(100dvh-2rem)] w-full max-w-md overflow-y-auto rounded-2xl bg-ckb-surface-container-lowest shadow-floating"
    >
        <div
            class="flex items-center justify-between border-b border-ckb-outline-variant/30 px-4 py-4 sm:px-6"
        >
            <div>
                <h3
                    id="packaging-modal-title"
                    class="text-lg font-bold text-ckb-primary"
                >
                    Tambah Satuan
                </h3>

                <p class="mt-1 text-xs text-ckb-on-surface-variant">Tentukan konversi ke satuan dasar produk.</p>
            </div>

            <button
                type="button"
                data-modal-close
                aria-label="Tutup dialog satuan pembelian"
                class="flex size-11 items-center justify-center rounded-lg text-ckb-outline hover:bg-ckb-surface-container-low hover:text-ckb-primary"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div id="packaging-form" class="space-y-4 p-4 sm:p-6">
            <div>
                <label
                    for="packaging-unit"
                    class="mb-1 block text-xs font-semibold text-ckb-primary"
                >
                    Satuan Pembelian *
                </label>

                <input
                    type="text"
                    id="packaging-unit"
                    name="purchase_unit"
                    maxlength="20"
                    placeholder="Contoh: dus"
                    class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                />
            </div>

            <div>
                <label
                    for="packaging-conversion"
                    class="mb-1 block text-xs font-semibold text-ckb-primary"
                >
                    Konversi *
                </label>

                <input
                    type="number"
                    id="packaging-conversion"
                    name="conversion_factor"
                    min="1"
                    max="999999"
                    step="1"
                    placeholder="Contoh: 24"
                    class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                />

                <p class="mt-1 text-xs text-ckb-on-surface-variant">Contoh: 1 dus = 24 pcs.</p>
            </div>

            <div
                class="flex flex-col-reverse justify-end gap-2 border-t border-ckb-outline-variant/30 px-4 py-4 sm:flex-row sm:gap-3 sm:px-6"
            >
                <button
                    type="button"
                    data-modal-close
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant px-4 py-2 text-xs font-semibold text-ckb-on-surface-variant transition hover:bg-ckb-surface-container-low focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
                >
                    Batal
                </button>

                <button
                    type="button"
                    id="btn-save-packaging"
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-ckb-primary px-4 py-2 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
                >
                    Simpan
                </button>
            </div>
        </div>
    </div>
</div>
