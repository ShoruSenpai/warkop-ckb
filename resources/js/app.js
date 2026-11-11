// import './bootstrap';
import Swal from "sweetalert2";
import { ckbColor, ckbColorWithAlpha } from "./utils/theme.js";

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";

library.add(fas);

dom.watch();
const themedSwal = Swal.mixin({
    background: ckbColor("surface"),
    color: ckbColor("on-surface"),
    backdrop: ckbColorWithAlpha("inverse-surface", 0.4),
    confirmButtonColor: ckbColor("primary"),
    cancelButtonColor: ckbColor("surface-container"),
    customClass: { cancelButton: "ckb-swal-cancel" },
});
const fireSwal = themedSwal.fire.bind(themedSwal);
const swalIconColors = {
    error: ckbColor("error"),
    warning: ckbColor("warning"),
    success: ckbColor("success"),
    info: ckbColor("accent"),
    question: ckbColor("accent"),
};

themedSwal.fire = (options = {}) =>
    fireSwal({
        ...options,
        customClass: {
            cancelButton: "ckb-swal-cancel",
            ...options.customClass,
        },
        iconColor: swalIconColors[options.icon] ?? options.iconColor,
    });

window.Swal = themedSwal;

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
