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
    document.addEventListener("change", (event) => {
        if (
            event.target instanceof HTMLInputElement &&
            event.target.matches('input[name="stock_type"]')
        ) {
            handleStockTypeChange();
        }
    });
}
