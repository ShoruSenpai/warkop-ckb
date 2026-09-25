import { state } from "./state.js";
import { $, escapeHtml, showApiError } from "./helper.js";

import { calculateRow, updateSummary } from "./calculation.js";

export function populateSelectOptions(select) {
    if (!select) return;

    const options = state.inventoryItems
        .map((item) => {
            const hasPackaging =
                Array.isArray(item.packagings) && item.packagings.length;

            return `
                    <option
                        value="${escapeHtml(item.item_key)}"
                        data-raw-id="${escapeHtml(item.raw_material_id || "")}"
                        data-prod-id="${escapeHtml(item.product_id || "")}"
                        data-unit="${escapeHtml(item.base_unit)}"
                    >
                        ${escapeHtml(item.name)}
                        ${hasPackaging ? "" : " — belum ada kemasan"}
                    </option>
                `;
        })
        .join("");

    select.innerHTML = `
        <option value="" disabled selected>
            -- Pilih Barang --
        </option>
        ${options}
    `;
}

export function addRow() {
    const template = $("row-template");

    const clone = template.content.cloneNode(true);

    populateSelectOptions(clone.querySelector(".item-select"));

    $("item-list").appendChild(clone);

    updateSummary();
}

export async function removeRow(button) {
    const rows = document.querySelectorAll(".item-row");

    if (rows.length <= 1) {
        await showApiError(
            {
                message: "Minimal harus ada satu item pembelian.",
            },
            "Tidak Dapat Menghapus",
        );

        return;
    }

    button.closest(".item-row")?.remove();

    updateSummary();
}

export function onItemChange(select) {
    const row = select.closest(".item-row");

    if (!row) return;

    const item = state.inventoryItems.find(
        (item) => item.item_key === select.value,
    );

    const packaging = row.querySelector(".packaging-select");

    const conversion = row.querySelector(".conversion-text");

    packaging.innerHTML = `
        <option value="" disabled selected>
            -- Pilih Satuan --
        </option>
    `;

    packaging.disabled = true;

    conversion.innerText = "Pilih satuan beli";

    row.dataset.itemType = item?.type || "";

    if (!item) return;

    const packagings = Array.isArray(item.packagings) ? item.packagings : [];

    if (!packagings.length) {
        conversion.innerHTML = `
            <span class="text-red-500">
                <i class="fas fa-exclamation-circle mr-1"></i>
                Belum ada kemasan
            </span>
        `;

        return;
    }

    packaging.innerHTML += packagings
        .map(
            (item) => `
                    <option
                        value="${escapeHtml(item.id)}"
                        data-conversion="${escapeHtml(item.conversion_factor)}"
                        data-unit="${escapeHtml(item.purchase_unit)}"
                    >
                        ${escapeHtml(item.purchase_unit)}
                    </option>
                `,
        )
        .join("");

    packaging.disabled = false;
}

export function onPackagingChange(select) {
    const row = select.closest(".item-row");

    const packaging = select.options[select.selectedIndex];

    const itemSelect = row.querySelector(".item-select");

    const selectedItem = itemSelect.options[itemSelect.selectedIndex];

    const text = row.querySelector(".conversion-text");

    if (!packaging || !selectedItem) {
        return;
    }

    const conversion = Number(packaging.dataset.conversion);

    const purchaseUnit = packaging.dataset.unit;

    const baseUnit = selectedItem.dataset.unit;

    if (!Number.isFinite(conversion) || conversion <= 0) {
        text.innerText = "Konversi tidak tersedia";

        return;
    }

    text.innerHTML = `
        <span class="font-semibold text-ckb-secondary">
            1 ${escapeHtml(purchaseUnit)}
            =
            ${conversion.toLocaleString("id-ID")}
            ${escapeHtml(baseUnit)}
        </span>
    `;

    calculateRow(select);
}

export function initRowEvents() {
    $("btn-add-row")?.addEventListener("click", addRow);

    const itemList = $("item-list");

    if (!itemList) return;

    itemList.addEventListener("change", (event) => {
        if (event.target.matches(".item-select")) {
            onItemChange(event.target);

            return;
        }

        if (event.target.matches(".packaging-select")) {
            onPackagingChange(event.target);
        }
    });

    itemList.addEventListener("input", (event) => {
        if (event.target.matches(".qty, .price")) {
            calculateRow(event.target);
        }
    });

    itemList.addEventListener("click", async (event) => {
        const button = event.target.closest('[data-action="remove-row"]');

        if (!button) return;

        await removeRow(button);
    });
}
