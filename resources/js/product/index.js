import {
    fetchProducts,
    fetchCategories,
    initProductListEvents,
} from "./list.js";

import { initCategoryEvents } from "./category.js";

document.addEventListener("DOMContentLoaded", async () => {
    initProductListEvents();
    initCategoryEvents();

    await Promise.all([fetchProducts(), fetchCategories()]);
});
