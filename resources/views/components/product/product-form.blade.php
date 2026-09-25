@props([
    'mode' => 'create',
    'productId' => null,
])

<div
    id="product-form-wrapper"
    data-mode="{{ $mode }}"
    data-product-id="{{ $productId }}"
    class="space-y-6"
>

    <form
        id="product-form"
        class="space-y-6"
    >

        {{-- Informasi utama --}}
        <section
            class="
                rounded-xl
                border border-ckb-outline-variant/40
                bg-ckb-surface-container-lowest
                p-6
            "
        >

            <div class="mb-5">
                <h3 class="font-bold text-ckb-primary">
                    Informasi Produk
                </h3>

                <p class="mt-1 text-xs text-ckb-on-surface-variant">
                    Informasi dasar yang akan ditampilkan pada sistem.
                </p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                <div class="md:col-span-2">

                    <label
                        for="product-name"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                    >
                        Nama Produk *
                    </label>

                    <input
                        type="text"
                        id="product-name"
                        maxlength="100"
                        required
                        placeholder="Contoh: Kopi Susu Gula Aren"
                        class="
                            w-full rounded-lg
                            border border-ckb-outline-variant/60
                            bg-ckb-surface-container-lowest
                            px-3 py-2.5
                            text-sm text-ckb-on-surface
                            outline-none
                            focus:border-ckb-primary
                            focus:ring-2 focus:ring-ckb-primary/10
                        "
                    >

                </div>

                <div>

                    <label
                        for="product-category"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                    >
                        Kategori *
                    </label>

                    <select
                        id="product-category"
                        required
                        class="
                            w-full rounded-lg
                            border border-ckb-outline-variant/60
                            bg-ckb-surface-container-lowest
                            px-3 py-2.5
                            text-sm text-ckb-on-surface
                            outline-none
                            focus:border-ckb-primary
                        "
                    >
                        <option value="">Memuat kategori...</option>
                    </select>

                </div>

                <div>

                    <label
                        for="product-price"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                    >
                        Harga Jual *
                    </label>

                    <div class="relative">

                        <span
                            class="
                                absolute left-3 top-1/2
                                -translate-y-1/2
                                text-sm font-semibold
                                text-ckb-outline
                            "
                        >
                            Rp
                        </span>

                        <input
                            type="number"
                            id="product-price"
                            min="0"
                            step="100"
                            required
                            placeholder="12000"
                            class="
                                w-full rounded-lg
                                border border-ckb-outline-variant/60
                                bg-ckb-surface-container-lowest
                                py-2.5 pl-10 pr-3
                                text-sm text-ckb-on-surface
                                outline-none
                                focus:border-ckb-primary
                                focus:ring-2 focus:ring-ckb-primary/10
                            "
                        >

                    </div>

                </div>

                <div class="md:col-span-2">

                    <label
                        for="product-description"
                        class="mb-1 block text-xs font-semibold text-ckb-primary"
                    >
                        Deskripsi
                    </label>

                    <textarea
                        id="product-description"
                        rows="3"
                        placeholder="Deskripsi produk..."
                        class="
                            w-full rounded-lg
                            border border-ckb-outline-variant/60
                            bg-ckb-surface-container-lowest
                            px-3 py-2.5
                            text-sm text-ckb-on-surface
                            outline-none
                            focus:border-ckb-primary
                            focus:ring-2 focus:ring-ckb-primary/10
                        "
                    ></textarea>

                </div>

                <div>
                    <div>
                        <label
                            for="product-image"
                            class="block text-sm font-semibold text-ckb-on-surface mb-2"
                        >
                            Gambar Produk
                        </label>

                        <input
                            type="file"
                            id="product-image"
                            name="image"
                            accept="image/jpeg,image/png,image/webp"
                            class="block w-full rounded-lg border border-ckb-outline-variant/60 bg-ckb-surface-container-lowest px-3 py-2.5 text-sm text-ckb-on-surface"
                        >

                        <p class="mt-1 text-xs text-ckb-on-surface-variant">
                            Format JPG, PNG, atau WebP.
                        </p>

                        <div id="product-image-preview" class="hidden mt-3">
                            <img
                                id="product-image-preview-img"
                                src=""
                                alt="Preview gambar produk"
                                class="h-32 w-32 rounded-xl object-cover border border-ckb-outline-variant/40"
                            >
                        </div>
                    </div>
                </div>

                <div class="flex items-end">

                    <label
                        class="
                            flex w-full cursor-pointer
                            items-center gap-3
                            rounded-lg
                            border border-ckb-outline-variant/40
                            bg-ckb-surface-container-low
                            px-3 py-3
                        "
                    >

                        <input
                            type="checkbox"
                            id="product-recommended"
                            class="
                                h-4 w-4
                                rounded
                                border-ckb-outline
                                text-ckb-secondary
                                focus:ring-ckb-secondary
                            "
                        >

                        <span>
                            <span class="block text-sm font-semibold text-ckb-primary">
                                Produk rekomendasi
                            </span>

                            <span class="block text-xs text-ckb-on-surface-variant">
                                Tampilkan sebagai produk unggulan.
                            </span>
                        </span>

                    </label>

                </div>

            </div>

        </section>

        {{-- Stock type --}}
        <x-product.stock-type-selector />

        {{-- Recipe --}}
        <x-product.recipe-builder />

        {{-- Packaging --}}
        <x-product.packaging-manager />

        {{-- Footer action --}}
        <div
            class="
                flex flex-col-reverse gap-3
                sm:flex-row sm:justify-end
            "
        >

            <a
                href="{{ route('products.index') }}"
                class="
                    rounded-lg
                    border border-ckb-outline-variant
                    bg-ckb-surface-container-lowest
                    px-5 py-2.5
                    text-center text-sm font-semibold
                    text-ckb-on-surface-variant
                    hover:bg-ckb-surface-container
                "
            >
                Batal
            </a>

            <button
                type="submit"
                id="btn-save-product"
                class="
                    inline-flex items-center justify-center gap-2
                    rounded-lg
                    bg-ckb-primary
                    px-5 py-2.5
                    text-sm font-semibold
                    text-ckb-on-primary
                    hover:opacity-90
                "
            >
                <i class="fas fa-save"></i>
                <span>Simpan Produk</span>
            </button>

        </div>

    </form>

</div>


