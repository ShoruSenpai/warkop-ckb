// import './bootstrap';
import { ckbColor } from "./utils/theme.js";
import "./utils/alert.js";

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import {
    faArrowLeft,
    faArrowRight,
    faBan,
    faBars,
    faBox,
    faBoxOpen,
    faCashRegister,
    faCheckCircle,
    faChevronDown,
    faChevronRight,
    faCircle,
    faCircleCheck,
    faCircleExclamation,
    faClock,
    faChartPie,
    faCog,
    faCubes,
    faEdit,
    faExclamationCircle,
    faExclamationTriangle,
    faFileInvoiceDollar,
    faEye,
    faEyeSlash,
    faFlask,
    faHeadset,
    faIdBadge,
    faInfoCircle,
    faLock,
    faMinusSquare,
    faPen,
    faPlus,
    faRightFromBracket,
    faRotateRight,
    faSave,
    faSearch,
    faShieldHalved,
    faSpinner,
    faStar,
    faSyncAlt,
    faTags,
    faTimes,
    faTrash,
    faTruckLoading,
    faUtensils,
    faUsersCog,
    faWallet,
    faChartLine,
    faBell,
    faRoadBarrier,
} from "@fortawesome/free-solid-svg-icons";

library.add(
    faArrowLeft,
    faArrowRight,
    faBan,
    faBars,
    faBox,
    faBoxOpen,
    faCashRegister,
    faCheckCircle,
    faChevronDown,
    faChevronRight,
    faCircle,
    faCircleCheck,
    faCircleExclamation,
    faClock,
    faChartPie,
    faCog,
    faCubes,
    faEdit,
    faExclamationCircle,
    faExclamationTriangle,
    faFileInvoiceDollar,
    faEye,
    faEyeSlash,
    faFlask,
    faHeadset,
    faIdBadge,
    faInfoCircle,
    faLock,
    faMinusSquare,
    faPen,
    faPlus,
    faRightFromBracket,
    faRotateRight,
    faSave,
    faSearch,
    faShieldHalved,
    faSpinner,
    faStar,
    faSyncAlt,
    faTags,
    faTimes,
    faTrash,
    faTruckLoading,
    faUtensils,
    faUsersCog,
    faWallet,
    faChartLine,
    faBell,
    faRoadBarrier,
);

function initializeFontAwesome() {
    dom.watch();

    void dom.i2svg({ node: document }).catch((error) => {
        console.error("Font Awesome icons could not be rendered.", error);
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initializeFontAwesome, {
        once: true,
    });
} else {
    initializeFontAwesome();
}

const sidebar = document.getElementById("admin-sidebar");
const sidebarToggle = document.getElementById("sidebar-toggle");
const sidebarBackdrop = document.getElementById("sidebar-backdrop");
const desktopSidebarQuery = window.matchMedia("(min-width: 64rem)");

function setSidebarOpen(isOpen, restoreFocus = false) {
    if (!sidebar || !sidebarToggle) return;

    const wasOpen = sidebarToggle.getAttribute("aria-expanded") === "true";
    const isMobile = !desktopSidebarQuery.matches;
    const shouldOpen = isOpen && isMobile;

    sidebar.classList.toggle("-translate-x-full", !shouldOpen);
    if (isMobile) {
        sidebar.setAttribute("aria-hidden", String(!shouldOpen));
    } else {
        sidebar.removeAttribute("aria-hidden");
    }
    sidebar.inert = !shouldOpen && isMobile;
    sidebarBackdrop?.classList.toggle("hidden", !shouldOpen);
    sidebarBackdrop?.setAttribute("aria-hidden", String(!shouldOpen));
    sidebarToggle.setAttribute("aria-expanded", String(shouldOpen));
    sidebarToggle.setAttribute(
        "aria-label",
        shouldOpen ? "Tutup menu navigasi" : "Buka menu navigasi",
    );

    if (!shouldOpen && wasOpen && restoreFocus) {
        sidebarToggle.focus();
    }
}

setSidebarOpen(false);

sidebarToggle?.addEventListener("click", () => {
    setSidebarOpen(sidebarToggle.getAttribute("aria-expanded") !== "true");
});

sidebarBackdrop?.addEventListener("click", () => setSidebarOpen(false, true));

document.addEventListener("pointerdown", (event) => {
    const isOpen = sidebarToggle?.getAttribute("aria-expanded") === "true";
    const target = event.target;

    if (
        isOpen &&
        target instanceof Node &&
        !sidebar?.contains(target) &&
        !sidebarToggle?.contains(target)
    ) {
        setSidebarOpen(false);
    }
});

sidebar?.addEventListener("click", (event) => {
    if (event.target.closest("a") && !desktopSidebarQuery.matches) {
        setSidebarOpen(false);
    }
});

document.addEventListener("keydown", (event) => {
    if (
        event.key === "Escape" &&
        sidebarToggle?.getAttribute("aria-expanded") === "true"
    ) {
        setSidebarOpen(false, true);
    }
});

desktopSidebarQuery.addEventListener("change", () => setSidebarOpen(false));

document.querySelectorAll("[data-ckb-avatar]").forEach((avatar) => {
    const imageUrl = new URL("https://ui-avatars.com/api/");
    const background = ckbColor("secondary-container").replace("#", "");
    const foreground = ckbColor("on-secondary-container").replace("#", "");

    imageUrl.searchParams.set("name", avatar.dataset.ckbAvatar);
    imageUrl.searchParams.set("background", background);
    imageUrl.searchParams.set("color", foreground);
    imageUrl.searchParams.set("bold", "true");

    avatar.src = imageUrl.toString();
});

document.addEventListener("input", (event) => {
    const input = event.target;

    if (input.type === "text" && input.maxLength > 0) {
        if (input.value.length > input.maxLength) {
            input.value = input.value.slice(0, input.maxLength);

            window.Swal.fire({
                toast: true,
                position: "top-end",
                icon: "warning",
                title: `Maksimal ${input.maxLength} karakter`,
                showConfirmButton: false,
                timer: 1800,
                timerProgressBar: true,
            });
        }

        return;
    }

    if (input.type !== "number") {
        return;
    }

    if (!input.max) {
        return;
    }

    const digits = input.value.replace(/\D/g, "");

    if (digits === "") {
        input.value = "";
        return;
    }

    const maxDigits = input.max.replace(/\D/g, "").length;

    if (digits.length > maxDigits) {
        input.value = digits.slice(0, maxDigits);

        window.Swal.fire({
            toast: true,
            position: "top-end",
            icon: "warning",
            title: `Maksimal ${maxDigits} angka`,
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true,
        });
    } else {
        input.value = digits;
    }
});
