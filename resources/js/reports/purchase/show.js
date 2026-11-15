import { apiFetch } from "@/utils/api.js";

const root = document.querySelector("[data-purchase-detail]");

if (root) {
    const purchaseId = root.dataset.purchaseId;

    loadPurchase();

    async function loadPurchase() {
        try {
            const { response, data } = await apiFetch(
                `/api/reports/purchases/${encodeURIComponent(purchaseId)}`,
            );

            if (!response.ok || !data?.success) {
                throw new Error(
                    data?.message || "Detail invoice tidak dapat dimuat.",
                );
            }

            renderPurchase(data.data);
        } catch (error) {
            if (error.message === "UNAUTHORIZED") return;
            setState(error.message || "Detail invoice tidak dapat dimuat.");
        }
    }

    function renderPurchase(purchase) {
        root.querySelectorAll("[data-purchase-field]").forEach((field) => {
            const key = field.dataset.purchaseField;
            field.textContent =
                key === "purchase_date"
                    ? formatDate(purchase[key])
                    : key === "status"
                      ? formatStatus(purchase[key])
                      : purchase[key] || "-";
        });

        root.querySelector("[data-purchase-total]").textContent = formatRupiah(
            purchase.total_cost,
        );

        const itemsNode = root.querySelector("[data-invoice-items]");
        const itemTemplate = document.getElementById(
            "invoice-item-row-template",
        );
        const items = Array.isArray(purchase.items) ? purchase.items : [];
        itemsNode.replaceChildren();

        if (!items.length) {
            const row = document.createElement("tr");
            const cell = document.createElement("td");
            cell.colSpan = 4;
            cell.className =
                "px-3 py-6 text-center text-xs text-ckb-on-surface-variant";
            cell.textContent = "Invoice ini belum memiliki rincian item.";
            row.append(cell);
            itemsNode.append(row);
        } else {
            items.forEach((item) => {
                const row =
                    itemTemplate.content.firstElementChild.cloneNode(true);
                const name =
                    item.raw_material?.name ||
                    item.product?.name ||
                    "Barang tidak tersedia";
                const unit =
                    item.purchase_unit ||
                    item.raw_material_packaging?.purchase_unit ||
                    item.product_packaging?.purchase_unit ||
                    "unit";
                setItemField(row, "item-name", name);
                setItemField(
                    row,
                    "item-quantity",
                    `${formatNumber(item.quantity)} ${unit}`,
                );
                setItemField(
                    row,
                    "item-unit-price",
                    formatRupiah(item.unit_price),
                );
                setItemField(row, "item-subtotal", formatRupiah(item.subtotal));
                itemsNode.append(row);
            });
        }

        root.querySelector("[data-detail-content]").hidden = false;
        setState(
            `${formatNumber(items.length)} item pada invoice ${purchase.invoice_number || ""}.`,
        );
    }

    function setItemField(row, name, value) {
        const field = row.querySelector(`[data-${name}]`);
        if (field) field.textContent = value;
    }

    function setState(message) {
        const state = root.querySelector("[data-detail-state]");
        if (state) state.textContent = message;
    }

    function formatRupiah(value) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(Number(value) || 0);
    }

    function formatNumber(value) {
        return new Intl.NumberFormat("id-ID").format(Number(value) || 0);
    }

    function formatDate(value) {
        if (!value) return "-";
        const date = new Date(`${value}T00:00:00`);
        return Number.isNaN(date.getTime())
            ? "-"
            : date.toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "long",
                  year: "numeric",
              });
    }

    function formatStatus(status) {
        const labels = { received: "Diterima", pending: "Menunggu" };
        return labels[status] || status || "Status tidak diketahui";
    }
}
