<section
    id="recipe-section"
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
                Resep Produk
            </h3>

            <p class="mt-1 text-xs text-ckb-on-surface-variant">
                Tentukan bahan baku dan kebutuhan per satu produk.
            </p>
        </div>

        <button
            type="button"
            id="btn-add-recipe"
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
            Tambah Bahan
        </button>

    </div>

    <div
        id="recipe-list"
        class="space-y-3"
    ></div>

    <div
        id="recipe-empty"
        class="
            rounded-lg
            bg-ckb-surface-container-low
            px-4 py-8
            text-center
        "
    >
        <i class="fas fa-flask text-xl text-ckb-outline"></i>

        <p class="mt-2 text-sm font-semibold text-ckb-primary">
            Belum ada bahan dalam resep
        </p>

        <p class="mt-1 text-xs text-ckb-on-surface-variant">
            Tambahkan minimal satu bahan baku.
        </p>
    </div>

</section>
