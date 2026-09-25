import {
    fetchInventoryItems,
    fetchSupplierSuggestions,
    initInventoryEvents,
} from "./inventory.js";

import { addRow, initRowEvents } from "./rows.js";

import { initPurchaseEvents } from "./purchase.js";

import { $ } from "./helper.js";

document.addEventListener("DOMContentLoaded", async () => {
    if ($("purchase_date")) {
        $("purchase_date").valueAsDate = new Date();
    }

    initInventoryEvents();
    initRowEvents();
    initPurchaseEvents();

    await Promise.all([fetchInventoryItems(), fetchSupplierSuggestions()]);

    addRow();
});
