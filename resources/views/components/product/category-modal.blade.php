<div
    id="category-modal"
    class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
>
    {{-- Modal backdrop --}}
    <div
        class="absolute inset-0 bg-black/40"
        data-action="close-category-modal"
    ></div>

    {{-- Modal content --}}
    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl">
        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Tambah Kategori
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Tambahkan kategori baru untuk produk.
                </p>
            </div>

            <button
                type="button"
                data-action="close-category-modal"
                class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="category-form">
            <div class="px-6 py-5">
                <label
                    for="category-name"
                    class="block text-sm font-semibold text-gray-700 mb-2"
                >
                    Nama Kategori
                </label>

                <input
                    type="text"
                    id="category-name"
                    name="name"
                    maxlength="100"
                    autocomplete="off"
                    placeholder="Contoh: Makanan"
                    class="w-full rounded-xl border border-gray-300 px-4 py-2.5 text-sm outline-none transition focus:border-ckb-primary focus:ring-2 focus:ring-ckb-primary/20"
                >

                <p
                    id="category-error"
                    class="hidden mt-2 text-xs text-red-500"
                ></p>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                <button
                    type="button"
                    data-action="close-category-modal"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="btn-save-category"
                    class="px-4 py-2 rounded-lg bg-ckb-primary text-white text-sm font-medium hover:bg-ckb-primary/90 transition-colors"
                >
                    <i class="fas fa-save mr-2"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
