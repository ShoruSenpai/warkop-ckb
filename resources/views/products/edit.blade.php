<x-layout title="Edit Produk">

    <div class="mx-auto max-w-5xl space-y-6">

        <div class="flex items-center gap-3">

            <a
                href="{{ route('products.index') }}"
                class="
                    flex h-9 w-9
                    items-center justify-center
                    rounded-lg
                    text-ckb-primary
                    hover:bg-ckb-primary-container
                "
                title="Kembali"
            >
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>
                <h2 class="text-2xl font-bold text-ckb-primary">
                    Edit Produk
                </h2>

                <p class="mt-1 text-sm text-ckb-on-surface-variant">
                    Perbarui informasi produk.
                </p>
            </div>

        </div>

        <x-product.product-form
            mode="edit"
            :product-id="$productId"
        />

    </div>

    <x-slot:script>
        @vite('resources/js/product/form.js')
    </x-slot:script>

</x-layout>
