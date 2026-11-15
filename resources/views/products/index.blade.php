<x-layout title="Manajemen Produk">
    <div class="space-y-4">
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"
        >
            <div class="min-w-0">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-ckb-on-surface-variant">Inventori Operasional</p>
                <h2
                    class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
                >
                    Master Produk
                </h2>

                <p
                    id="summary-text"
                    class="mt-1 text-xs text-ckb-on-surface-variant"
                >Memuat data produk...</p>
            </div>

            <div class="flex w-full flex-wrap gap-2 sm:w-auto sm:shrink-0">
                @if (auth()->user()?->role === "owner")
                    <button
                        type="button"
                        id="btn-add-category"
                        class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg border border-ckb-primary px-3.5 py-2 text-xs font-semibold text-ckb-primary transition-colors hover:bg-ckb-primary/10 sm:text-sm"
                    >
                        <i class="fas fa-tags" aria-hidden="true"></i>
                        Tambah Kategori
                    </button>
                @endif
                <a
                    id="btn-add-product"
                    href="{{ route('products.create') }}"
                    class="inline-flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-primary px-3.5 py-2 text-xs font-semibold text-ckb-on-primary transition hover:bg-ckb-accent sm:text-sm"
                >
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div>
            <div
                class="grid grid-cols-1 gap-3 lg:grid-cols-[minmax(0,1fr)_minmax(10rem,auto)_minmax(9rem,auto)]"
            >
                <div class="flex min-w-0 gap-2">
                    <div class="relative min-w-0 flex-1">
                        <i
                            class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-ckb-outline"
                            aria-hidden="true"
                        ></i>

                        <input
                            type="text"
                            id="search-input"
                            placeholder="Cari produk..."
                            class="min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 pl-9 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                        />
                    </div>

                    <button
                        type="button"
                        id="btn-refresh-products"
                        title="Muat ulang produk"
                        aria-label="Muat ulang produk"
                        class="inline-flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface text-xs text-ckb-primary transition hover:bg-ckb-surface-container-low disabled:cursor-wait disabled:opacity-60 motion-reduce:transition-none"
                    >
                        <i class="fas fa-rotate-right" aria-hidden="true"></i>
                    </button>
                </div>

                <select
                    id="category-filter"
                    class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                >
                    <option value="">Semua Kategori</option>
                </select>

                <select
                    id="status-filter"
                    class="min-h-11 w-full cursor-pointer rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
                >
                    <option value="">Semua Status</option>
                    <option value="available">Tersedia</option>
                    <option value="sold_out">Habis</option>
                    <option value="disabled">Nonaktif</option>
                </select>
            </div>
        </div>

        {{-- Table --}}
        <div
            class="overflow-hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface shadow-surface"
        >
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px] border-collapse text-left">
                    <thead class="bg-ckb-surface-container-low">
                        <tr
                            class="border-b border-ckb-outline-variant/50 text-[10px] uppercase tracking-wider text-ckb-on-surface-variant"
                        >
                            <th class="px-3 py-2.5 font-medium">Produk</th>
                            <th class="px-3 py-2.5 font-medium">Kategori</th>
                            <th class="px-3 py-2.5 font-medium">Harga</th>
                            <th class="px-3 py-2.5 font-medium">Jenis Stok</th>
                            <th class="px-3 py-2.5 font-medium">Stok</th>
                            <th class="px-3 py-2.5 font-medium">Status</th>
                            <th class="px-3 py-2.5 text-right font-medium">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody id="product-list">
                        <tr>
                            <td
                                colspan="7"
                                class="px-3 py-6 text-center text-xs text-ckb-on-surface-variant"
                            >
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Memuat produk...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-product.category-modal />

    <x-slot:script>
        @vite ("resources/js/product/index.js")
    </x-slot:script>
</x-layout>
