import { $ } from "./helper.js";

export function calculateRow(element) {
    const row = element.closest(".item-row");

    if (!row) return;

    const quantity = parseFloat(row.querySelector(".qty").value) || 0;

    const price = parseFloat(row.querySelector(".price").value) || 0;

    const subtotal = quantity * price;

    row.querySelector(".subtotal").value =
        `Rp ${subtotal.toLocaleString("id-ID")}`;

    row.dataset.subtotalValue = subtotal;

    updateSummary();
}

export function updateSummary() {
    const rows = [...document.querySelectorAll(".item-row")];

    const total = rows.reduce(
        (sum, row) => sum + parseFloat(row.dataset.subtotalValue || 0),
        0,
    );

    $("grand-total").innerText = `Rp ${total.toLocaleString("id-ID")}`;

    $("item-count").innerText =
        `${rows.length} item · stok akan diperbarui otomatis`;
}
