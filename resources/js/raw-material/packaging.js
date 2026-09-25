import { state } from "./state.js";

import {
    $,
    escapeHtml,
    findById,
    showApiError,
    toggleModal,
} from "./helper.js";

import { apiFetch } from "../utils/api.js";
import { fetchRawMaterials } from "./table.js";

export async function openEditMaterialModal(id) {
    const item = findById(state.rawMaterialData, id);

    if (!item) {
        await showApiError({
            message: "Data bahan baku tidak ditemukan.",
        });

        return;
    }

    state.editingMaterial = item;
    state.packagingData = [];
    state.editingPackagingId = null;

    $("packaging-modal-title").innerText = `Edit Bahan · ${item.name}`;

    $("packaging-modal-description").innerText =
        "Ubah informasi bahan dan kelola kemasan pembelian.";

    $("edit-rm-name").value = item.name;

    $("packaging-base-unit").innerText = item.unit_measurement;

    toggleModal("packaging-modal", true);

    resetPackagingForm();

    await fetchPackagings(item.id);
}

export function closePackagingModal() {
    toggleModal("packaging-modal", false);

    state.editingMaterial = null;
    state.packagingData = [];
    state.editingPackagingId = null;

    resetPackagingForm();
}

async function fetchPackagings(materialId) {
    const tbody = $("packaging-list");

    tbody.innerHTML = `
        <tr>
            <td
                colspan="4"
                class="py-6 text-center text-gray-400"
            >
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Memuat kemasan...
            </td>
        </tr>
    `;

    try {
        const { response, data } = await apiFetch(
            `/api/raw-materials/${materialId}/packagings`,
        );

        if (!response.ok) {
            await showApiError(data, "Gagal Memuat Kemasan");

            return;
        }

        state.packagingData = Array.isArray(data.data) ? data.data : [];

        renderPackagingList();
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Daftar kemasan tidak dapat dimuat.",
        });
    }
}

function renderPackagingList() {
    const tbody = $("packaging-list");

    if (!state.packagingData.length) {
        tbody.innerHTML = `
            <tr>
                <td
                    colspan="4"
                    class="py-8 text-center text-gray-400"
                >
                    <i class="fas fa-box-open text-2xl mb-2"></i>

                    <p class="text-sm">
                        Belum ada kemasan.
                    </p>

                    <p class="text-xs mt-1">
                        Tambahkan kemasan agar bahan ini bisa digunakan pada Pembelian Supplier.
                    </p>
                </td>
            </tr>
        `;

        return;
    }

    tbody.innerHTML = state.packagingData
        .map((packaging) => {
            const conversion = Number(packaging.conversion_factor);

            const status = packaging.is_active
                ? `
                            <span class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs font-semibold">
                                Aktif
                            </span>
                        `
                : `
                            <span class="text-gray-600 bg-gray-100 px-2 py-1 rounded text-xs font-semibold">
                                Nonaktif
                            </span>
                        `;

            return `
                    <tr class="border-b border-gray-50">

                        <td class="px-4 py-3">
                            <span class="font-medium text-sm text-gray-800">
                                ${escapeHtml(packaging.purchase_unit)}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            <span class="text-sm font-semibold text-ckb-secondary">
                                ${conversion.toLocaleString("id-ID")}
                                ${escapeHtml(
                                    state.editingMaterial?.unit_measurement ||
                                        "",
                                )}
                            </span>

                            <span class="text-xs text-gray-400 ml-1">
                                per 1
                                ${escapeHtml(packaging.purchase_unit)}
                            </span>
                        </td>

                        <td class="px-4 py-3">
                            ${status}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">

                                <button
                                    type="button"
                                    data-action="edit-packaging"
                                    data-id="${Number(packaging.id)}"
                                    class="px-2.5 py-1.5 rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100 text-xs"
                                    title="Edit"
                                >
                                    <i class="fas fa-edit"></i>
                                </button>

                                <button
                                    type="button"
                                    data-action="delete-packaging"
                                    data-id="${Number(packaging.id)}"
                                    class="px-2.5 py-1.5 rounded-lg bg-red-50 text-red-700 hover:bg-red-100 text-xs"
                                    title="Hapus"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>

                            </div>
                        </td>

                    </tr>
                `;
        })
        .join("");
}

function resetPackagingForm() {
    $("packaging-form")?.reset();

    state.editingPackagingId = null;

    $("packaging-id").value = "";

    const button = $("btn-save-packaging");

    button.innerText = "+ Tambah Kemasan";

    $("btn-cancel-packaging-edit").classList.add("hidden");
}

function editPackaging(id) {
    const packaging = findById(state.packagingData, id);

    if (!packaging) {
        return;
    }

    state.editingPackagingId = Number(packaging.id);

    $("packaging-id").value = packaging.id;

    $("packaging-unit").value = packaging.purchase_unit;

    $("packaging-conversion").value = packaging.conversion_factor;

    $("btn-save-packaging").innerText = "Simpan Perubahan";

    $("btn-cancel-packaging-edit").classList.remove("hidden");

    $("packaging-unit").focus();
}

function cancelPackagingEdit() {
    resetPackagingForm();
}

async function submitMaterialEdit(event) {
    event.preventDefault();

    if (!state.editingMaterial) {
        return;
    }

    const button = $("btn-save-material-edit");

    const name = $("edit-rm-name").value.trim();

    if (!name) {
        await showApiError(
            {
                message: "Nama bahan baku wajib diisi.",
            },
            "Data Belum Lengkap",
        );

        $("edit-rm-name").focus();
        return;
    }

    button.disabled = true;

    button.innerHTML =
        '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

    try {
        const { response, data } = await apiFetch(
            `/api/raw-materials/${state.editingMaterial.id}`,
            {
                method: "PUT",
                body: {
                    name,
                    unit_measurement: state.editingMaterial.unit_measurement,
                },
            },
        );

        if (!response.ok) {
            await showApiError(data, "Gagal Mengubah Bahan");

            return;
        }

        await Swal.fire({
            icon: "success",
            title: "Bahan Baku Diperbarui",
            text: "Nama bahan baku berhasil diperbarui.",
            confirmButtonText: "OK",
            confirmButtonColor: "#6B4423",
        });

        await fetchRawMaterials();

        state.editingMaterial.name = name;

        $("packaging-modal-title").innerText = `Edit Bahan · ${name}`;
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Bahan baku tidak dapat diperbarui.",
        });
    } finally {
        button.disabled = false;

        button.innerHTML = "Simpan Perubahan";
    }
}

async function submitPackaging(event) {
    event.preventDefault();

    if (!state.editingMaterial) {
        await showApiError({
            message: "Bahan baku belum dipilih.",
        });

        return;
    }

    const button = $("btn-save-packaging");

    const purchaseUnit = $("packaging-unit").value.trim();

    const conversionFactor = Number($("packaging-conversion").value);

    if (!purchaseUnit) {
        await showApiError(
            {
                message: "Satuan beli wajib diisi.",
            },
            "Data Belum Lengkap",
        );

        $("packaging-unit").focus();
        return;
    }

    if (!Number.isFinite(conversionFactor) || conversionFactor <= 0) {
        await showApiError(
            {
                message: "Nilai konversi harus lebih besar dari 0.",
            },
            "Konversi Tidak Valid",
        );

        $("packaging-conversion").focus();

        return;
    }

    const id = state.editingPackagingId;

    const baseUrl = `/api/raw-materials/${state.editingMaterial.id}/packagings`;

    const url = id ? `${baseUrl}/${id}` : baseUrl;

    const method = id ? "PUT" : "POST";

    button.disabled = true;

    button.innerHTML =
        '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';

    try {
        const { response, data } = await apiFetch(url, {
            method,
            body: {
                purchase_unit: purchaseUnit,

                conversion_factor: conversionFactor,

                is_active: true,
            },
        });

        if (!response.ok) {
            await showApiError(
                data,
                id ? "Gagal Mengubah Kemasan" : "Gagal Menambahkan Kemasan",
            );

            return;
        }

        resetPackagingForm();

        await fetchPackagings(state.editingMaterial.id);

        await Swal.fire({
            icon: "success",
            title: id ? "Kemasan Diperbarui" : "Kemasan Ditambahkan",

            text: id
                ? "Konfigurasi kemasan berhasil diperbarui."
                : "Kemasan berhasil ditambahkan.",

            confirmButtonText: "OK",

            confirmButtonColor: "#6B4423",
        });
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Kemasan tidak dapat disimpan. Silakan coba lagi.",
        });
    } finally {
        button.disabled = false;

        button.innerText = id ? "Simpan Perubahan" : "+ Tambah Kemasan";
    }
}

async function deletePackaging(id) {
    const packaging = findById(state.packagingData, id);

    if (!packaging || !state.editingMaterial) {
        return;
    }

    const result = await Swal.fire({
        icon: "warning",
        title: "Hapus Kemasan?",

        html: `
                Kemasan
                <strong>
                    ${escapeHtml(packaging.purchase_unit)}
                </strong>
                akan dihapus.

                <br><br>

                <span class="text-sm text-gray-500">
                    Jika kemasan sudah pernah digunakan
                    dalam transaksi, sistem dapat menolaknya.
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
        const { response, data } = await apiFetch(
            `/api/raw-materials/${state.editingMaterial.id}/packagings/${id}`,
            {
                method: "DELETE",
            },
        );

        if (!response.ok) {
            await showApiError(data, "Kemasan Tidak Dapat Dihapus");

            return;
        }

        await fetchPackagings(state.editingMaterial.id);

        await Swal.fire({
            icon: "success",
            title: "Kemasan Dihapus",
            text: "Kemasan berhasil dihapus.",
            confirmButtonText: "OK",
            confirmButtonColor: "#6B4423",
        });
    } catch (error) {
        if (error.message === "UNAUTHORIZED") {
            return;
        }

        console.error(error);

        await showApiError({
            message: "Kemasan tidak dapat dihapus.",
        });
    }
}

export function initPackagingEvents() {
    $("packaging-modal")?.addEventListener("click", (event) => {
        const closeButton = event.target.closest("[data-modal-close]");

        if (closeButton) {
            closePackagingModal();
            return;
        }

        const button = event.target.closest("[data-action]");

        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);

        switch (button.dataset.action) {
            case "edit-packaging":
                editPackaging(id);
                break;

            case "delete-packaging":
                deletePackaging(id);
                break;
        }
    });

    $("material-edit-form")?.addEventListener("submit", submitMaterialEdit);

    $("packaging-form")?.addEventListener("submit", submitPackaging);

    $("btn-cancel-packaging-edit")?.addEventListener(
        "click",
        cancelPackagingEdit,
    );
}
