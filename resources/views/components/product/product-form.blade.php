@props ([
    "mode" => "create",
    "productId" => null,
])

<div
    id="product-form-wrapper"
    data-mode="{{ $mode }}"
    data-product-id="{{ $productId }}"
    data-index-url="{{ route('products.index') }}"
    class="space-y-4"
>
    <form id="product-form" class="space-y-4">
        {{-- Informasi utama --}}
        <section
            class="rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface"
        >
            <div class="mb-4">
                <h3 class="text-sm font-bold text-ckb-primary">
                    Informasi Produk
                </h3>

                <p class="mt-1 text-xs text-ckb-on-surface-variant">Informasi dasar yang akan ditampilkan pada sistem.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
                        maxlength="50"
                        required
                        placeholder="Contoh: Kopi Susu Gula Aren"
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                    />
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
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
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
                            class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-semibold text-ckb-outline"
                        >
                            Rp
                        </span>

                        <input
                            type="number"
                            id="product-price"
                            min="0"
                            max="999999999"
                            step="1"
                            required
                            placeholder="10.000"
                            class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface py-2 pl-10 pr-3 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                        />
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
                        maxlength="500"
                        placeholder="Deskripsi produk..."
                        class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                    ></textarea>
                </div>

                @if ($mode === "create")
                    <div>
                        <div>
                            <label
                                for="product-image"
                                class="mb-1 block text-xs font-semibold text-ckb-primary"
                            >
                                Gambar Produk
                            </label>

                            <input
                                type="file"
                                id="product-image"
                                name="image"
                                accept="image/jpeg,image/png,image/webp"
                                class="block min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface sm:text-sm"
                            />

                            <p class="mt-1 text-xs text-ckb-on-surface-variant">Format JPG, PNG, atau WebP.</p>

                            <div id="product-image-preview" class="mt-3 hidden">
                                <img
                                    id="product-image-preview-img"
                                    src=""
                                    alt="Preview gambar produk"
                                    class="h-32 w-32 rounded-xl border border-ckb-outline-variant/50 object-cover"
                                />
                            </div>
                        </div>
                    </div>
                @else
                    <div class="hidden md:block" aria-hidden="true"></div>
                @endif

                <div class="flex items-end">
                    <label
                        class="flex min-h-11 w-full cursor-pointer items-center gap-3 rounded-lg border border-ckb-outline-variant/50 bg-ckb-surface-container-low px-3 py-2"
                    >
                        <input
                            type="checkbox"
                            id="product-recommended"
                            class="h-4 w-4 rounded border-ckb-outline text-ckb-primary focus:ring-ckb-accent"
                        />

                        <span>
                            <span
                                class="block text-xs font-semibold text-ckb-primary sm:text-sm"
                            >
                                Produk rekomendasi
                            </span>

                            <span
                                class="block text-xs text-ckb-on-surface-variant"
                            >
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
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a
                href="{{ route('products.index') }}"
                class="inline-flex min-h-11 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface px-4 py-2 text-xs font-semibold text-ckb-on-surface-variant transition hover:bg-ckb-surface-container-low focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
            >
                Batal
            </a>

            <button
                type="submit"
                id="btn-save-product"
                class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-primary px-4 py-2 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
            >
                <i class="fas fa-save"></i>
                <span>Simpan Produk</span>
            </button>
        </div>
    </form>
</div>
