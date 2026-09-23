<tr class="item-row border-b border-gray-50">
    <td class="py-2 pr-2">
        <select class="item-select w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white" onchange="onItemChange(this)" required>
            <option value="" disabled selected>-- Pilih Barang --</option>
        </select>
    </td>
    <td class="py-2 pr-2">
        <input type="text" class="unit w-full border border-gray-200 rounded px-2 py-1.5 text-sm" placeholder="Karton/Pack" required>
    </td>
    <td class="py-2 pr-2">
        <input type="number" step="0.1" class="qty w-full border border-gray-200 rounded px-2 py-1.5 text-sm" value="1" oninput="calculateRow(this)" required>
    </td>
    <td class="py-2 pr-2">
        <div class="flex items-center gap-1">
            <input type="number" step="0.1" class="conv w-full border border-gray-200 rounded px-2 py-1.5 text-sm" value="1" placeholder="Konversi" required>
            <span class="base-unit-text text-[10px] text-gray-400 min-w-[28px]">-</span>
        </div>
    </td>
    <td class="py-2 pr-2">
        <input type="number" class="price w-full border border-gray-200 rounded px-2 py-1.5 text-sm" value="0" oninput="calculateRow(this)" required>
    </td>
    <td class="py-2 pr-2">
        <input type="text" class="subtotal w-full border border-transparent bg-gray-50 rounded px-2 py-1.5 text-sm font-semibold text-gray-700" value="Rp 0" readonly>
    </td>
    <td class="py-2 text-center">
        <button type="button" onclick="removeRow(this)" class="text-red-400 hover:text-red-600"><i class="fas fa-minus-square"></i></button>
    </td>
</tr>
