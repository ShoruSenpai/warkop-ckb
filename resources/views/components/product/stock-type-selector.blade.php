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
            Jenis Stok Produk
        </h3>

        <p class="mt-1 text-xs text-ckb-on-surface-variant">
            Tentukan bagaimana stok produk dikelola.
        </p>
    </div>

    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

        @foreach([
            [
                'value' => 'static',
                'title' => 'Static',
                'description' => 'Stok berasal dari pembelian produk langsung.',
                'icon' => 'fa-box'
            ],
            [
                'value' => 'recipe',
                'title' => 'Recipe',
                'description' => 'Stok dihitung dari bahan baku dan resep.',
                'icon' => 'fa-flask'
            ],
            [
                'value' => 'untracked',
                'title' => 'Untracked',
                'description' => 'Produk tidak menggunakan tracking stok.',
                'icon' => 'fa-ban'
            ],
        ] as $type)

            <label
                class="
                    stock-type-card
                    cursor-pointer
                    rounded-xl
                    border border-ckb-outline-variant/50
                    p-4
                    transition
                    hover:bg-ckb-surface-container-low
                "
            >

                <input
                    type="radio"
                    name="stock_type"
                    value="{{ $type['value'] }}"
                    class="peer sr-only"
                >

                <div class="flex items-start gap-3">

                    <div
                        class="
                            flex h-10 w-10 shrink-0
                            items-center justify-center
                            rounded-lg
                            bg-ckb-surface-container
                            text-ckb-primary
                            peer-checked:bg-ckb-primary
                            peer-checked:text-ckb-on-primary
                        "
                    >
                        <i class="fas {{ $type['icon'] }}"></i>
                    </div>

                    <div>

                        <p class="font-semibold text-ckb-primary">
                            {{ $type['title'] }}
                        </p>

                        <p class="mt-1 text-xs leading-relaxed text-ckb-on-surface-variant">
                            {{ $type['description'] }}
                        </p>

                    </div>

                </div>

                <div
                    class="
                        mt-3 hidden
                        items-center gap-1
                        text-xs font-semibold
                        text-ckb-secondary
                        peer-checked:flex
                    "
                >
                    <i class="fas fa-check-circle"></i>
                    Dipilih
                </div>

            </label>

        @endforeach

    </div>

    <div
        id="untracked-info"
        class="mt-4 hidden rounded-lg bg-ckb-surface-container-low p-4"
    >
        <div class="flex gap-3">

            <i class="fas fa-info-circle mt-0.5 text-ckb-tertiary"></i>

            <div>
                <p class="text-sm font-semibold text-ckb-primary">
                    Stok tidak dilacak
                </p>

                <p class="mt-1 text-xs text-ckb-on-surface-variant">
                    Produk ini tidak memiliki jumlah stok yang ditampilkan
                    kepada kasir.
                </p>
            </div>

        </div>
    </div>

</section>
