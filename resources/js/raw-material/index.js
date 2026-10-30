import {
    fetchRawMaterials,
    filterTable,
    initTableEvents,
    renderTable,
} from "./table.js";

import {
    openAddMaterialModal,
    closeMaterialModal,
    addMaterialPackagingRow,
    removeMaterialPackagingRow,
    refreshMaterialPackagingEmptyState,
    updateMaterialPackagingPreview,
    submitMaterial,
    deleteMaterial,
    initMaterialEvents,
} from "./material.js";

import {
    openEditMaterialModal,
    closePackagingModal,
    initPackagingEvents,
} from "./packaging.js";

document.addEventListener("DOMContentLoaded", () => {
    fetchRawMaterials();

    initTableEvents({
        onEdit: openEditMaterialModal,
        onDelete: deleteMaterial,
    });

    initMaterialEvents();
    initPackagingEvents();
});
