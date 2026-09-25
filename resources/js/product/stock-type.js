import { $ } from "./helper.js";

export function handleStockTypeChange() {
    const selected = document.querySelector(
        'input[name="stock_type"]:checked',
    )?.value;

    $("recipe-section")?.classList.toggle("hidden", selected !== "recipe");

    $("packaging-section")?.classList.toggle("hidden", selected !== "static");

    $("untracked-info")?.classList.toggle("hidden", selected !== "untracked");
}

export function initStockTypeEvents() {
    document.querySelectorAll('input[name="stock_type"]').forEach((input) => {
        input.addEventListener("change", handleStockTypeChange);
    });
}
