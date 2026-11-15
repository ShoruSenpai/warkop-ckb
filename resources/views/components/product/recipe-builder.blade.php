<section
    id="recipe-section"
    class="hidden rounded-xl border border-ckb-outline-variant/50 bg-ckb-surface p-4 shadow-surface"
>
    <div
        class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
    >
        <div>
            <h3 class="text-sm font-bold text-ckb-primary">Resep Produk</h3>

            <p class="mt-1 text-xs text-ckb-on-surface-variant">Tentukan bahan baku dan kebutuhan per satu produk.</p>
        </div>

        <button
            type="button"
            id="btn-add-recipe"
            class="inline-flex min-h-11 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-lg bg-ckb-secondary px-3.5 py-2 text-xs font-semibold text-ckb-on-secondary transition hover:bg-ckb-secondary-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent sm:text-sm motion-reduce:transition-none"
        >
            <i class="fas fa-plus"></i>
            Tambah Bahan
        </button>
    </div>

    <div id="recipe-list" class="space-y-3"></div>

    <div
        id="recipe-empty"
        class="rounded-lg bg-ckb-surface-container-low px-4 py-8 text-center"
    >
        <i class="fas fa-flask text-xl text-ckb-outline"></i>

        <p class="mt-2 text-sm font-semibold text-ckb-primary">Belum ada bahan dalam resep</p>

        <p class="mt-1 text-xs text-ckb-on-surface-variant">Tambahkan minimal satu bahan baku.</p>
    </div>
</section>
