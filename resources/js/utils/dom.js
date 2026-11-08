export const $ = (id) => document.getElementById(id);

export function toggleModal(id, show) {
    const modal = $(id);

    modal.classList.toggle("hidden", !show);

    modal.classList.toggle("flex", show);
}

export function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}
