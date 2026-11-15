<x-layout title="Edit Produk">
    <div class="mx-auto max-w-5xl space-y-4">
        <div class="mb-4 flex items-start gap-3">
            <a
                href="{{ route('products.index') }}"
                class="flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-lg border border-ckb-outline-variant bg-ckb-surface text-xs text-ckb-primary transition hover:bg-ckb-surface-container-low motion-reduce:transition-none"
                title="Kembali"
                aria-label="Kembali ke manajemen produk"
            >
                <i class="fas fa-arrow-left" aria-hidden="true"></i>
            </a>

            <div class="min-w-0">
                <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.16em] text-ckb-on-surface-variant">Inventori Operasional</p>
                <h2
                    class="text-xl font-bold leading-tight text-ckb-primary sm:text-2xl"
                >
                    Edit Produk
                </h2>

                <p class="mt-1 text-xs text-ckb-on-surface-variant">Perbarui informasi produk.</p>
            </div>
        </div>

        <x-product.product-form mode="edit" :product-id="$productId" />
    </div>

    <x-slot:script>
        @vite ("resources/js/product/form.js")
    </x-slot:script>
</x-layout>
