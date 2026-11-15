<div
    id="category-modal"
    role="dialog"
    aria-modal="true"
    aria-hidden="true"
    aria-labelledby="category-modal-title"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
>
    {{-- Modal backdrop --}}
    <div
        class="absolute inset-0 bg-ckb-inverse-surface/40"
        data-action="close-category-modal"
        data-modal-close
    ></div>

    {{-- Modal content --}}
    <div
        class="relative w-full max-w-md rounded-2xl bg-ckb-surface shadow-floating"
    >
        {{-- Header --}}
        <div
            class="flex items-center justify-between px-6 py-4 border-b border-ckb-outline-variant/50"
        >
            <div>
                <h2
                    id="category-modal-title"
                    class="text-lg font-bold text-ckb-on-surface"
                >
                    Tambah Kategori
                </h2>
                <p class="text-xs text-ckb-on-surface-variant mt-1">Tambahkan kategori baru untuk produk.</p>
            </div>

            <button
                type="button"
                data-action="close-category-modal"
                data-modal-close
                aria-label="Tutup dialog kategori"
                class="flex size-11 items-center justify-center rounded-lg text-ckb-on-surface-variant hover:text-ckb-on-surface hover:bg-ckb-surface-container transition-colors"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="category-form">
            <div class="px-6 py-5">
                <label
                    for="category-name"
                    class="mb-1 block text-xs font-semibold text-ckb-primary"
                >
                    Nama Kategori
                </label>

                <input
                    type="text"
                    id="category-name"
                    name="name"
                    maxlength="50"
                    autocomplete="off"
                    required
                    placeholder="Contoh: Makanan"
                    class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                />

                <p
                    id="category-error"
                    class="hidden mt-2 text-xs text-ckb-error"
                ></p>
            </div>

            {{-- Footer --}}
            <div
                class="flex items-center justify-end gap-2 px-6 py-4 border-t border-ckb-outline-variant/50"
            >
                <button
                    type="button"
                    data-action="close-category-modal"
                    data-modal-close
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface px-4 py-2 text-xs font-semibold text-ckb-on-surface transition-colors hover:bg-ckb-surface-container-low focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="btn-save-category"
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg bg-ckb-primary px-4 py-2 text-xs font-semibold text-ckb-on-primary transition-colors hover:bg-ckb-accent hover:text-ckb-on-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
                >
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
