export const $ = (id) => document.getElementById(id);

let modalTrigger = null;

export function toggleModal(id, show) {
    const modal = $(id);

    if (!modal) return;

    if (show) {
        modalTrigger = document.activeElement;
    }

    modal.classList.toggle("hidden", !show);

    modal.classList.toggle("flex", show);

    modal.setAttribute("aria-hidden", String(!show));

    if (show) {
        modal.querySelector("input:not([disabled]), button:not([disabled])")?.focus();
    } else if (modalTrigger instanceof HTMLElement && modalTrigger.isConnected) {
        modalTrigger.focus();
        modalTrigger = null;
    }
}

document.addEventListener("keydown", (event) => {
    const modal = document.querySelector(
        '[role="dialog"][aria-modal="true"][aria-hidden="false"]',
    );

    if (!modal) return;

    if (event.key === "Escape") {
        event.preventDefault();
        modal.querySelector("[data-modal-close]")?.click();
        return;
    }

    if (event.key !== "Tab") return;

    const focusable = [
        ...modal.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
        ),
    ].filter((element) => element.getClientRects().length);

    if (!focusable.length) {
        event.preventDefault();
        modal.focus();
        return;
    }

    const first = focusable[0];
    const last = focusable[focusable.length - 1];

    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
});

export function escapeHtml(value) {
    return String(value ?? "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}
