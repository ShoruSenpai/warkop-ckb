export async function downloadReportPdf(button, url, reportLabel) {
    const idleIcon = button.querySelector("[data-export-idle]");
    const loadingIcon = button.querySelector("[data-export-loading]");
    const label = button.querySelector("[data-export-label]");

    const originalLabel = label?.textContent ?? "Unduh PDF";

    const setLoading = (loading) => {
        idleIcon?.classList.toggle("hidden", loading);
        loadingIcon?.classList.toggle("hidden", !loading);

        button.disabled = loading;

        if (loading) {
            button.setAttribute("aria-busy", "true");
        } else {
            button.removeAttribute("aria-busy");
        }

        if (label) {
            label.textContent = loading ? "Menyiapkan PDF..." : originalLabel;
        }
    };

    setLoading(true);

    try {
        const response = await fetch(url, {
            headers: {
                Accept: "application/pdf",
            },
            credentials: "same-origin",
        });

        if (response.redirected && response.url.includes("/login")) {
            window.location.href = response.url;
            return;
        }

        const contentType = response.headers.get("content-type") || "";

        if (!response.ok || !contentType.includes("application/pdf")) {
            throw new Error(
                "PDF tidak dapat dibuat. Periksa filter lalu coba lagi.",
            );
        }

        const blob = await response.blob();
        const objectUrl = URL.createObjectURL(blob);

        try {
            const anchor = document.createElement("a");
            const disposition =
                response.headers.get("content-disposition") || "";

            const filename = disposition.match(/filename="?([^";]+)"?/i)?.[1];

            anchor.href = objectUrl;
            anchor.download = filename || `laporan-${reportLabel}.pdf`;

            document.body.append(anchor);
            anchor.click();
            anchor.remove();
        } finally {
            window.setTimeout(() => {
                URL.revokeObjectURL(objectUrl);
            }, 1000);
        }

        await Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "PDF laporan berhasil diunduh",
            showConfirmButton: false,
            timer: 2500,
        });
    } catch (error) {
        console.error("PDF export failed:", error);

        await Swal.fire({
            icon: "error",
            title: `Laporan ${reportLabel} tidak dapat diunduh`,
            text: error.message || "Terjadi kesalahan saat membuat PDF.",
            confirmButtonText: "Mengerti",
        });
    } finally {
        setLoading(false);
    }
}
