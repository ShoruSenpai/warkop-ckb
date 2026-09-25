import { state } from "./state.js";
import { apiFetch } from "./api.js";
import { $, escapeHtml, showApiError } from "./helper.js";

import {
    populateSelectOptions,
    onItemChange,
    onPackagingChange,
} from "./rows.js";

import { calculateRow } from "./calculation.js";

export async function fetchInventoryItems() {
    try {
        const { response, data } = await apiFetch("/api/inventory-items");

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Daftar Barang");

            return;
        }

        state.inventoryItems = Array.isArray(data.data) ? data.data : [];
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Daftar barang tidak dapat dimuat.",
        });
    }
}

export async function fetchSupplierSuggestions() {
    try {
        const { response, data } = await apiFetch("/api/supplier-suggestions");

        const dataList = $("supplier-suggestions");

        if (!dataList) return;

        dataList.innerHTML = "";

        if (!response.ok) {
            console.error(data);
            return;
        }

        const suppliers = Array.isArray(data.data) ? data.data : [];

        dataList.innerHTML = suppliers
            .map(
                (supplier) =>
                    `<option value="${escapeHtml(supplier)}"></option>`,
            )
            .join("");
    } catch (error) {
        console.error(error);
    }
}

export async function refreshPurchaseData() {
    const button = $("btn-refresh-purchase");

    const rows = [...document.querySelectorAll(".item-row")];

    const rowStates = rows.map((row) => ({
        itemKey: row.querySelector(".item-select")?.value || "",

        packagingId: row.querySelector(".packaging-select")?.value || "",

        quantity: row.querySelector(".qty")?.value || "1",

        price: row.querySelector(".price")?.value || "0",
    }));

    if (button) {
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        button.disabled = true;
    }

    try {
        await Promise.all([fetchInventoryItems(), fetchSupplierSuggestions()]);

        rows.forEach((row, index) => {
            const saved = rowStates[index];

            const itemSelect = row.querySelector(".item-select");

            populateSelectOptions(itemSelect);

            if (saved.itemKey) {
                itemSelect.value = saved.itemKey;

                onItemChange(itemSelect);
            }

            const packagingSelect = row.querySelector(".packaging-select");

            if (
                saved.packagingId &&
                [...packagingSelect.options].some(
                    (option) => option.value === saved.packagingId,
                )
            ) {
                packagingSelect.value = saved.packagingId;

                onPackagingChange(packagingSelect);
            }

            row.querySelector(".qty").value = saved.quantity;

            row.querySelector(".price").value = saved.price;

            calculateRow(row.querySelector(".price"));
        });

        await Swal.fire({
            icon: "success",
            title: "Data Diperbarui",
            text: "Daftar barang dan supplier berhasil diperbarui.",
            timer: 1500,
            showConfirmButton: false,
        });
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Data pembelian tidak dapat diperbarui.",
        });
    } finally {
        if (button) {
            button.innerHTML = '<i class="fas fa-sync-alt"></i>';

            button.disabled = false;
        }
    }
}

export function initInventoryEvents() {
    $("btn-refresh-purchase")?.addEventListener("click", refreshPurchaseData);
}
