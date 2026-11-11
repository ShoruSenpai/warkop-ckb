<div
    id="category-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
>
    {{-- Modal backdrop --}}
    <div
        class="absolute inset-0 bg-ckb-inverse-surface/40"
        data-action="close-category-modal"
    ></div>

    {{-- Modal content --}}
    <div class="relative w-full max-w-md rounded-2xl bg-ckb-surface shadow-floating">
        {{-- Header --}}
        <div
            class="flex items-center justify-between px-6 py-4 border-b border-ckb-outline-variant/50"
        >
            <div>
                <h2 class="text-lg font-bold text-ckb-on-surface">Tambah Kategori</h2>
                <p class="text-xs text-ckb-on-surface-variant mt-1">Tambahkan kategori baru untuk produk.</p>
            </div>

            <button
                type="button"
                data-action="close-category-modal"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-ckb-on-surface-variant hover:text-ckb-on-surface hover:bg-ckb-surface-container transition-colors"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="category-form">
            <div class="px-6 py-5">
                <label
                    for="category-name"
                    class="block text-sm font-semibold text-ckb-on-surface mb-2"
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
                    class="w-full rounded-xl border border-ckb-outline-variant px-4 py-2.5 text-sm outline-none transition focus:border-ckb-accent focus:ring-2 focus:ring-ckb-accent/20"
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
                    class="px-4 py-2 rounded-lg border border-ckb-outline-variant text-sm font-medium text-ckb-on-surface hover:bg-ckb-surface-container-low transition-colors"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="btn-save-category"
                    class="px-4 py-2 rounded-lg bg-ckb-primary text-ckb-on-primary text-sm font-medium hover:bg-ckb-accent hover:text-ckb-on-accent transition-colors"
                >
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
