<tr class="item-row border-b border-ckb-outline-variant/30 text-xs sm:text-sm">
    {{-- Barang --}}
    <td class="px-3 py-2.5">
        <select
            class="item-select min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
            required
        >
            <option value="" disabled selected>-- Pilih Barang --</option>
        </select>
    </td>

    {{-- Satuan Beli --}}
    <td class="px-3 py-2.5">
        <select
            class="packaging-select min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent disabled:cursor-not-allowed disabled:bg-ckb-surface-container-low sm:text-sm motion-reduce:transition-none"
            required
            disabled
        >
            <option value="" disabled selected>-- Pilih Satuan --</option>
        </select>
    </td>

    {{-- Quantity --}}
    <td class="px-3 py-2.5">
        <input
            type="number"
            step="1"
            min="1"
            max="999"
            class="qty min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
            value="1"
            required
        />
    </td>

    {{-- Conversion --}}
    <td class="px-3 py-2.5">
        <div
            class="conversion-display flex min-h-11 items-center rounded-lg border border-ckb-outline-variant bg-ckb-surface-container-low px-3 py-2"
        >
            <span
                class="conversion-text text-xs text-ckb-on-surface-variant sm:text-sm"
            >
                Satuan Beli
            </span>
        </div>
    </td>

    {{-- Price --}}
    <td class="px-3 py-2.5">
        <input
            type="number"
            min="0"
            max="999999999"
            step="1"
            class="price min-h-11 w-full rounded-lg border border-ckb-outline-variant bg-ckb-surface px-3 py-2 text-xs text-ckb-on-surface outline-none transition focus:border-ckb-accent focus:ring-1 focus:ring-ckb-accent sm:text-sm motion-reduce:transition-none"
            value="0"
            required
        />
    </td>

    {{-- Subtotal --}}
    <td class="px-3 py-2.5">
        <input
            type="text"
            class="subtotal min-h-11 w-full rounded-lg border border-transparent bg-ckb-surface-container-low px-3 py-2 text-xs font-semibold text-ckb-on-surface sm:text-sm"
            value="Rp 0"
            readonly
        />
    </td>

    {{-- Action --}}
    <td class="px-3 py-2.5 text-center">
        <button
            type="button"
            data-action="remove-row"
            aria-label="Hapus baris pembelian"
            class="flex size-11 cursor-pointer items-center justify-center rounded-lg text-xs text-ckb-error transition hover:bg-ckb-error-container focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-ckb-accent motion-reduce:transition-none"
            title="Hapus baris"
        >
            <i class="fas fa-minus-square"></i>
        </button>
    </td>
</tr>
