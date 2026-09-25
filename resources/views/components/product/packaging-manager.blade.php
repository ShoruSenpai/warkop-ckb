<section
    id="packaging-section"
    class="
        hidden
        rounded-xl
        border border-ckb-outline-variant/40
        bg-ckb-surface-container-lowest
        p-6
    "
>

    <div class="mb-5 flex items-start justify-between gap-4">

        <div>
            <h3 class="font-bold text-ckb-primary">
                Satuan Pembelian
            </h3>

            <p class="mt-1 text-xs text-ckb-on-surface-variant">
                Atur satuan yang dapat digunakan saat pembelian supplier.
            </p>
        </div>

        <button
            type="button"
            id="btn-add-packaging"
            class="
                inline-flex shrink-0
                items-center gap-2
                rounded-lg
                bg-ckb-secondary
                px-3 py-2
                text-xs font-semibold
                text-ckb-on-secondary
                hover:opacity-90
            "
        >
            <i class="fas fa-plus"></i>
            Tambah Satuan
        </button>

    </div>

    <div
        id="packaging-list"
        class="space-y-2"
    ></div>

    <div
        id="packaging-empty"
        class="
            rounded-lg
            bg-ckb-surface-container-low
            px-4 py-8
            text-center
        "
    >
        <i class="fas fa-box-open text-xl text-ckb-outline"></i>

        <p class="mt-2 text-sm font-semibold text-ckb-primary">
            Belum ada satuan pembelian
        </p>

        <p class="mt-1 text-xs text-ckb-on-surface-variant">
            Tambahkan satuan seperti pcs, dus, karton, dan sebagainya.
        </p>
    </div>

</section>

{{-- Modal kecil khusus packaging --}}
<div
    id="packaging-modal"
    class="
        fixed inset-0 z-50 hidden
        items-center justify-center
        bg-ckb-inverse-surface/60
        px-4
    "
>

    <div
        class="
            w-full max-w-md
            rounded-2xl
            bg-ckb-surface-container-lowest
            shadow-xl
        "
    >

        <div class="flex items-center justify-between border-b border-ckb-outline-variant/30 px-5 py-4">

            <div>
                <h3
                    id="packaging-modal-title"
                    class="font-bold text-ckb-primary"
                >
                    Tambah Satuan
                </h3>

                <p class="mt-1 text-xs text-ckb-on-surface-variant">
                    Tentukan konversi ke satuan dasar produk.
                </p>
            </div>

            <button
                type="button"
                data-modal-close
                class="text-ckb-outline hover:text-ckb-primary"
            >
                <i class="fas fa-times"></i>
            </button>

        </div>

        <div class="space-y-4 p-5">

            <div>

                <label class="mb-1 block text-xs font-semibold text-ckb-primary">
                    Satuan Pembelian *
                </label>

                <input
                    type="text"
                    id="packaging-unit"
                    maxlength="50"
                    placeholder="Contoh: dus"
                    class="
                        w-full rounded-lg
                        border border-ckb-outline-variant/60
                        px-3 py-2.5 text-sm
                        outline-none
                        focus:border-ckb-primary
                    "
                >

            </div>

            <div>

                <label class="mb-1 block text-xs font-semibold text-ckb-primary">
                    Konversi *
                </label>

                <input
                    type="number"
                    id="packaging-conversion"
                    min="0.001"
                    step="0.001"
                    placeholder="Contoh: 24"
                    class="
                        w-full rounded-lg
                        border border-ckb-outline-variant/60
                        px-3 py-2.5 text-sm
                        outline-none
                        focus:border-ckb-primary
                    "
                >

                <p class="mt-1 text-[11px] text-ckb-outline">
                    Contoh: 1 dus = 24 pcs.
                </p>

            </div>

        </div>

        <div class="flex justify-end gap-3 border-t border-ckb-outline-variant/30 px-5 py-4">

            <button
                type="button"
                data-modal-close
                class="
                    rounded-lg
                    border border-ckb-outline-variant
                    px-4 py-2
                    text-sm font-semibold
                    text-ckb-on-surface-variant
                "
            >
                Batal
            </button>

            <button
                type="button"
                id="btn-save-packaging"
                class="
                    rounded-lg
                    bg-ckb-primary
                    px-4 py-2
                    text-sm font-semibold
                    text-ckb-on-primary
                "
            >
                Simpan
            </button>

        </div>

    </div>

</div>
