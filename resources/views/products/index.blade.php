<x-layout title="Master Produk">

    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-2xl font-bold text-ckb-primary">
                    Master Produk
                </h2>

                <p
                    id="summary-text"
                    class="mt-1 text-sm text-ckb-on-surface-variant"
                >
                    Memuat data produk...
                </p>
            </div>


            <div class="flex items-center gap-2">
                  @if(auth()->user()?->role === 'owner')
                     <button
                            type="button"
                            id="btn-add-category"
                           class="px-4 py-2 rounded-lg border border-ckb-primary text-ckb-primary hover:bg-ckb-primary/10 transition-colors"
                        >
                           <i class="fas fa-tags mr-2"></i>
                            Tambah Kategori
                      </button>
                   @endif
                <a
                    href="{{ route('products.create') }}"
                    class="
                        inline-flex items-center justify-center gap-2
                        rounded-lg
                        bg-ckb-primary
                        px-4 py-2.5
                        text-sm font-semibold
                        text-ckb-on-primary
                        transition
                        hover:opacity-90
                    "
                >
                    <i class="fas fa-plus"></i>
                    Tambah Produk
                </a>
            </div>
        </div>

        {{-- Filter --}}
        <div
            class="
                rounded-xl
                border border-ckb-outline-variant/40
                bg-ckb-surface-container-lowest
                p-4
            "
        >

            <div class="grid grid-cols-1 gap-3 md:grid-cols-[1fr_auto_auto]">

                <div class="relative">

                    <i
                        class="
                            fas fa-search
                            pointer-events-none
                            absolute left-3 top-1/2
                            -translate-y-1/2
                            text-sm text-ckb-outline
                        "
                    ></i>

                    <input
                        type="text"
                        id="search-input"
                        placeholder="Cari produk..."
                        class="
                            w-full
                            rounded-lg
                            border border-ckb-outline-variant/60
                            bg-ckb-surface-container-lowest
                            py-2.5 pl-9 pr-3
                            text-sm text-ckb-on-surface
                            outline-none
                            focus:border-ckb-primary
                            focus:ring-2
                            focus:ring-ckb-primary/10
                        "
                    >

                </div>

                <select
                    id="category-filter"
                    class="
                        rounded-lg
                        border border-ckb-outline-variant/60
                        bg-ckb-surface-container-lowest
                        px-3 py-2.5
                        text-sm text-ckb-on-surface
                        outline-none
                        focus:border-ckb-primary
                    "
                >
                    <option value="">Semua Kategori</option>
                </select>

                <select
                    id="status-filter"
                    class="
                        rounded-lg
                        border border-ckb-outline-variant/60
                        bg-ckb-surface-container-lowest
                        px-3 py-2.5
                        text-sm text-ckb-on-surface
                        outline-none
                        focus:border-ckb-primary
                    "
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
            class="
                overflow-hidden
                rounded-xl
                border border-ckb-outline-variant/40
                bg-ckb-surface-container-lowest
            "
        >

            <div class="overflow-x-auto">

                <table class="w-full min-w-[800px] text-left">

                    <thead class="bg-ckb-surface-container-low">

                    <tr
                        class="
                            border-b border-ckb-outline-variant/40
                            text-xs uppercase tracking-wider
                            text-ckb-on-surface-variant
                        "
                    >
                        <th class="px-4 py-3 font-semibold">Produk</th>
                        <th class="px-4 py-3 font-semibold">Kategori</th>
                        <th class="px-4 py-3 font-semibold">Harga</th>
                        <th class="px-4 py-3 font-semibold">Jenis Stok</th>
                        <th class="px-4 py-3 font-semibold">Stok</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 text-right font-semibold">Aksi</th>
                    </tr>

                    </thead>

                    <tbody id="product-list">

                    <tr>
                        <td
                            colspan="7"
                            class="px-4 py-10 text-center text-sm text-ckb-outline"
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
        @vite('resources/js/product/index.js')
    </x-slot:script>
</x-layout>
