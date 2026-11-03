import { $, escapeHtml } from "./dom.js";

export function validationMessage(data) {
    if (!data?.errors) {
        return data?.message || "Data tidak dapat diproses.";
    }

    return Object.values(data.errors).flat().map(escapeHtml).join("<br>");
}

export async function showApiError(data, title = "Terjadi Kesalahan") {
    await Swal.fire({
        icon: "error",
        title,
        html: validationMessage(data),
        confirmButtonText: "Mengerti",
        confirmButtonColor: "#6B4423",
    });
}
