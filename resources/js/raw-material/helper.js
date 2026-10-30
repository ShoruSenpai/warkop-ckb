export const $ = (id) => document.getElementById(id);

export function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

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

export function toggleModal(id, show) {
    const modal = $(id);

    modal.classList.toggle("hidden", !show);

    modal.classList.toggle("flex", show);
}

export function findById(items, id) {
    return items.find((item) => Number(item.id) === Number(id));
}
