import { state } from "./state.js";

import { $, escapeHtml, showApiError, toggleModal } from "./helper.js";

import { apiFetch } from "../utils/api.js";
import { fetchRawMaterials } from "./table.js";

export function openAddMaterialModal() {
    state.editingMaterial = null;
    state.packagingData = [];

    $("material-form").reset();
    $("material-id").value = "";

    $("material-modal-title").innerText = "Daftarkan Bahan Baku Baru";

    const button = $("btn-save-material");

    button.innerText = "Simpan Bahan Baku";

    button.dataset.defaultText = "Simpan Bahan Baku";

    $("material-packaging-list").innerHTML = "";

    refreshMaterialPackagingEmptyState();

    toggleModal("material-modal", true);
}

export function closeMaterialModal() {
    toggleModal("material-modal", false);
}

export function addMaterialPackagingRow(data = null) {
    const template = $("material-packaging-template");

    const clone = template.content.cloneNode(true);

    const row = clone.querySelector(".material-packaging-row");

    row.dataset.packagingId = data?.id ?? "";

    row.querySelector(".material-packaging-unit").value =
        data?.purchase_unit ?? "";

    row.querySelector(".material-packaging-conversion").value =
        data?.conversion_factor ?? "";

    $("material-packaging-list").appendChild(clone);

    refreshMaterialPackagingEmptyState();

    const conversionInput = row.querySelector(".material-packaging-conversion");

    if (conversionInput.value) {
        updateMaterialPackagingPreview(conversionInput);
    }

    return row;
}

export function removeMaterialPackagingRow(button) {
    button.closest(".material-packaging-row")?.remove();

    refreshMaterialPackagingEmptyState();
}

export function refreshMaterialPackagingEmptyState() {
    const list = $("material-packaging-list");

    const empty = $("material-packaging-empty");

    const hasRows = list.querySelector(".material-packaging-row");

    empty.classList.toggle("hidden", Boolean(hasRows));
}

export function updateMaterialPackagingPreview(input) {
    const row = input.closest(".material-packaging-row");

    if (!row) return;

    const unit = row.querySelector(".material-packaging-unit").value.trim();

    const conversion = Number(input.value);

    const baseUnit = $("rm_unit").value;

    const preview = row.querySelector(".material-packaging-preview");

    if (!unit || !Number.isFinite(conversion) || conversion <= 0) {
        preview.innerText = "Masukkan jumlah konversi.";

        return;
    }

    preview.innerHTML = `
        <span class="font-semibold text-ckb-secondary">
            1 ${escapeHtml(unit)}
            =
            ${conversion.toLocaleString("id-ID")}
            ${escapeHtml(baseUnit)}
        </span>
    `;
}

function collectPackagings() {
    return [...document.querySelectorAll(".material-packaging-row")].map(
        (row) => ({
            purchase_unit: row
                .querySelector(".material-packaging-unit")
                .value.trim(),

            conversion_factor: Number(
                row.querySelector(".material-packaging-conversion").value,
            ),

            is_active: true,
        }),
    );
}

async function validatePackagings(packagings) {
    if (!packagings.length) {
        await Swal.fire({
            icon: "warning",
            title: "Kemasan Belum Diatur",
            text: "Tambahkan minimal satu kemasan pembelian agar bahan baku dapat digunakan pada Pembelian Supplier.",
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#6B4423",
        });

        return false;
    }

    for (let index = 0; index < packagings.length; index++) {
        const packaging = packagings[index];

        const line = index + 1;

        if (!packaging.purchase_unit) {
            await Swal.fire({
                icon: "warning",
                title: "Satuan Beli Belum Diisi",
                text: `Satuan beli pada kemasan ke-${line} wajib diisi.`,
                confirmButtonText: "Mengerti",
                confirmButtonColor: "#6B4423",
            });

            return false;
        }

        if (
            !Number.isFinite(packaging.conversion_factor) ||
            packaging.conversion_factor <= 0
        ) {
            await Swal.fire({
                icon: "warning",
                title: "Konversi Tidak Valid",
                text: `Nilai konversi pada kemasan ke-${line} harus lebih besar dari 0.`,
                confirmButtonText: "Mengerti",
                confirmButtonColor: "#6B4423",
            });

            return false;
        }
    }

    const units = packagings.map((packaging) =>
        packaging.purchase_unit.toLowerCase(),
    );

    if (new Set(units).size !== units.length) {
        await Swal.fire({
            icon: "warning",
            title: "Satuan Beli Duplikat",
            text: "Satuan beli yang sama tidak boleh didaftarkan dua kali.",
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#6B4423",
        });

        return false;
    }

    return true;
}

export async function submitMaterial(event) {
    event.preventDefault();

    const button = $("btn-save-material");

    const payload = {
        name: $("rm_name").value.trim(),

        unit_measurement: $("rm_unit").value,
    };

    const packagings = collectPackagings();

    if (!(await validatePackagings(packagings))) {
        return;
    }

    button.disabled = true;

    button.innerHTML =
        '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

    try {
        // Create material
        const { response, data } = await apiFetch("/api/raw-materials", {
            method: "POST",
            body: payload,
        });

        if (!response.ok) {
            await showApiError(data, "Gagal Menambahkan Bahan");

            return;
        }

        const materialId = data.data?.id;

        if (!materialId) {
            await showApiError({
                message:
                    "Bahan berhasil dibuat tetapi ID bahan tidak ditemukan.",
            });

            return;
        }

        // Create packaging
        for (const packaging of packagings) {
            const result = await apiFetch(
                `/api/raw-materials/${materialId}/packagings`,
                {
                    method: "POST",
                    body: packaging,
                },
            );

            if (!result.response.ok) {
                await showApiError(
                    result.data,
                    "Bahan Berhasil, Kemasan Gagal",
                );

                return;
            }
        }

        closeMaterialModal();

        await Swal.fire({
            icon: "success",
            title: "Bahan Baku Ditambahkan",
            text: "Bahan baku dan kemasan pembelian berhasil disimpan.",
            confirmButtonText: "Selesai",
            confirmButtonColor: "#6B4423",
        });

        await fetchRawMaterials();
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Bahan baku tidak dapat disimpan. Silakan coba lagi.",
        });
    } finally {
        button.disabled = false;

        button.innerText = "Simpan Bahan Baku";
    }
}

export async function deleteMaterial(id) {
    const item = state.rawMaterialData.find(
        (item) => Number(item.id) === Number(id),
    );

    if (!item) return;

    const result = await Swal.fire({
        icon: "warning",
        title: "Hapus Bahan Baku?",
        html: `
                Bahan
                <strong>
                    ${escapeHtml(item.name)}
                </strong>
                akan dihapus.

                <br><br>

                <span class="text-red-600 text-sm">
                    Pastikan bahan ini memang tidak lagi digunakan.
                </span>
            `,
        showCancelButton: true,
        confirmButtonText: "Ya, Hapus",
        cancelButtonText: "Batal",
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
    });

    if (!result.isConfirmed) {
        return;
    }

    try {
        const { response, data } = await apiFetch(`/api/raw-materials/${id}`, {
            method: "DELETE",
        });

        if (!response.ok) {
            await showApiError(data, "Bahan Tidak Dapat Dihapus");

            return;
        }

        await Swal.fire({
            icon: "success",
            title: "Bahan Baku Dihapus",
            text: "Data bahan baku berhasil dihapus.",
            confirmButtonText: "OK",
            confirmButtonColor: "#6B4423",
        });

        await fetchRawMaterials();
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Bahan baku tidak dapat dihapus.",
        });
    }
}

export function initMaterialEvents() {
    $("btn-add-material")?.addEventListener("click", openAddMaterialModal);

    $("material-form")?.addEventListener("submit", submitMaterial);

    $("material-modal")?.addEventListener("click", (event) => {
        const closeButton = event.target.closest("[data-modal-close]");

        if (closeButton) {
            closeMaterialModal();
            return;
        }

        const actionButton = event.target.closest("[data-action]");

        if (actionButton?.dataset.action === "remove-packaging") {
            removeMaterialPackagingRow(actionButton);
        }
    });

    $("btn-add-packaging")?.addEventListener("click", () =>
        addMaterialPackagingRow(),
    );

    $("btn-add-empty-packaging")?.addEventListener("click", () =>
        addMaterialPackagingRow(),
    );

    $("material-packaging-list")?.addEventListener("input", (event) => {
        if (event.target.matches(".material-packaging-conversion")) {
            updateMaterialPackagingPreview(event.target);
        }
    });
}
