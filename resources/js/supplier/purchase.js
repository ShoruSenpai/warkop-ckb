import { $, showApiError } from "./helper.js";
import { apiFetch } from "../utils/api.js";

export async function submitPurchase(event) {
    event.preventDefault();

    const rows = [...document.querySelectorAll(".item-row")];

    const items = [];

    for (let index = 0; index < rows.length; index++) {
        const row = rows[index];

        const line = index + 1;

        const itemSelect = row.querySelector(".item-select");

        const packagingSelect = row.querySelector(".packaging-select");

        const selectedOption = itemSelect.options[itemSelect.selectedIndex];

        const quantity = parseFloat(row.querySelector(".qty").value);

        const unitPrice = parseFloat(row.querySelector(".price").value);

        if (!itemSelect.value) {
            await showApiError(
                {
                    message: `Silakan pilih barang pada baris ke-${line}.`,
                },
                "Data Belum Lengkap",
            );

            return;
        }

        if (!packagingSelect.value) {
            await showApiError(
                {
                    message: `Silakan pilih satuan beli pada baris ke-${line}.`,
                },
                "Satuan Beli Belum Dipilih",
            );

            return;
        }

        if (!Number.isFinite(quantity) || quantity <= 0) {
            await showApiError(
                {
                    message: `Jumlah pada baris ke-${line} harus lebih besar dari 0.`,
                },
                "Jumlah Tidak Valid",
            );

            return;
        }

        if (!Number.isFinite(unitPrice) || unitPrice < 0) {
            await showApiError(
                {
                    message: `Harga satuan pada baris ke-${line} tidak valid.`,
                },
                "Harga Tidak Valid",
            );

            return;
        }

        const packagingId = Number(packagingSelect.value);

        if (!Number.isInteger(packagingId) || packagingId <= 0) {
            await showApiError(
                {
                    message: `Kemasan pada baris ke-${line} tidak valid.`,
                },
                "Kemasan Tidak Valid",
            );

            return;
        }

        const isRawMaterial = Boolean(selectedOption.dataset.rawId);

        const isProduct = Boolean(selectedOption.dataset.prodId);

        items.push({
            raw_material_id: isRawMaterial
                ? Number(selectedOption.dataset.rawId)
                : null,

            raw_material_packaging_id: isRawMaterial ? packagingId : null,

            product_id: isProduct
                ? Number(selectedOption.dataset.prodId)
                : null,

            product_packaging_id: isProduct ? packagingId : null,

            quantity,
            unit_price: unitPrice,
        });
    }

    const payload = {
        invoice_number: $("invoice_number").value.trim(),

        supplier_name: $("supplier_name").value.trim(),

        purchase_date: $("purchase_date").value,

        status: "received",

        items,
    };

    const result = await Swal.fire({
        icon: "question",
        title: "Simpan Pembelian?",
        text: "Pembelian akan dicatat dan stok akan langsung diperbarui.",

        showCancelButton: true,

        confirmButtonText: "Ya, Simpan",

        cancelButtonText: "Periksa Lagi",

        confirmButtonColor: "#6B4423",

        cancelButtonColor: "#6b7280",
    });

    if (!result.isConfirmed) {
        return;
    }

    const button = $("btn-submit");

    button.disabled = true;

    button.innerHTML =
        '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';

    try {
        const { response, data } = await apiFetch("/api/supplier-purchases", {
            method: "POST",
            body: payload,
        });

        if (!response.ok) {
            await showApiError(
                data,
                response.status === 422
                    ? "Data Pembelian Belum Valid"
                    : "Pembelian Gagal",
            );

            return;
        }

        await Swal.fire({
            icon: "success",
            title: "Pembelian Berhasil",
            text: "Pembelian telah dicatat dan stok berhasil diperbarui.",
            confirmButtonText: "Selesai",
            confirmButtonColor: "#6B4423",
        });

        window.location.reload();
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Pembelian tidak dapat diproses. Silakan coba lagi.",
        });
    } finally {
        button.disabled = false;

        button.innerHTML =
            '<i class="fas fa-check-circle mr-2"></i> Simpan & Update Stok';
    }
}

export function initPurchaseEvents() {
    $("form-purchase")?.addEventListener("submit", submitPurchase);
}
