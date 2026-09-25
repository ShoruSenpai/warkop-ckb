<tr class="item-row border-b border-gray-50">

    {{-- Barang --}}
    <td class="py-2 pr-2">

        <select
            class="item-select w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:ring-1 focus:ring-ckb-primary focus:outline-none transition"
            required
        >

            <option
                value=""
                disabled
                selected
            >
                -- Pilih Barang --
            </option>

        </select>

    </td>


    {{-- Satuan Beli --}}
    <td class="py-2 pr-2">

        <select
            class="packaging-select w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:ring-1 focus:ring-ckb-primary focus:outline-none transition"
            required
            disabled
        >

            <option
                value=""
                disabled
                selected
            >
                -- Pilih Satuan --
            </option>

        </select>

    </td>


    {{-- Quantity --}}
    <td class="py-2 pr-2">

        <input
            type="number"
            step="0.1"
            min="0.1"
            class="qty w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none transition"
            value="1"
            required
        >

    </td>


    {{-- Conversion --}}
    <td class="py-2 pr-2">

        <div
            class="conversion-display min-h-[34px] flex items-center bg-gray-50 border border-gray-200 rounded px-2 py-1.5"
        >

            <span
                class="conversion-text text-xs text-gray-400"
            >
                Satuan Beli
            </span>

        </div>

    </td>


    {{-- Price --}}
    <td class="py-2 pr-2">

        <input
            type="number"
            min="0"
            step="0.01"
            class="price w-full border border-gray-200 rounded px-2 py-1.5 text-sm focus:ring-1 focus:ring-ckb-primary focus:outline-none transition"
            value="0"
            required
        >

    </td>


    {{-- Subtotal --}}
    <td class="py-2 pr-2">

        <input
            type="text"
            class="subtotal w-full border border-transparent bg-gray-50 rounded px-2 py-1.5 text-sm font-semibold text-gray-700"
            value="Rp 0"
            readonly
        >

    </td>


    {{-- Action --}}
    <td class="py-2 text-center">

        <button
            type="button"
            class="text-red-400 hover:text-red-600"
            title="Hapus baris"
        >

            <i class="fas fa-minus-square"></i>

        </button>

    </td>

</tr>
