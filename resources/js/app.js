// import './bootstrap';
import Swal from "sweetalert2";

import { library, dom } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";

library.add(fas);

dom.watch();
window.Swal = Swal;

document.addEventListener("input", (event) => {
    const input = event.target;

    if (input.type === "text" && input.maxLength > 0) {
        if (input.value.length > input.maxLength) {
            input.value = input.value.slice(0, input.maxLength);

            Swal.fire({
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

        Swal.fire({
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
