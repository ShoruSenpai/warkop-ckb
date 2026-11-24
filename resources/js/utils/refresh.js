export async function runRefreshAction(button, request) {
    if (!button || button.disabled) return false;

    const setIconSpinning = (isSpinning) => {
        button.querySelectorAll("i, svg").forEach((icon) => {
            icon.classList.toggle("fa-spin", isSpinning);
        });
    };

    button.disabled = true;
    button.setAttribute("aria-busy", "true");
    setIconSpinning(true);

    try {
        return await request();
    } finally {
        button.disabled = false;
        button.removeAttribute("aria-busy");
        setIconSpinning(false);
    }
}
