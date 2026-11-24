import SweetAlert from "sweetalert2";
import { ckbColor, ckbColorWithAlpha } from "./theme.js";

const themedSwal = SweetAlert.mixin({
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

export default themedSwal;
