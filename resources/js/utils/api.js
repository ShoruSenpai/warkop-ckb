const loginUrl = document.querySelector('meta[name="login-url"]')?.content;

function getCookie(name) {
    const cookies = document.cookie.split("; ");

    const cookiesItem = cookies.find((item) => item.startsWith(`${name}=`));

    return cookiesItem ? cookiesItem.substring(name.length + 1) : null;
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

function getXsrfToken() {
    const cookie = getCookie("XSRF-TOKEN");

    return cookie ? decodeURIComponent(cookie) : null;
}

async function handleApiError(response, data) {
    if (response.status === 401) {
        await Swal.fire({
            icon: "warning",
            title: "Sesi Berakhir",
            text: "Sesi login kamu sudah berakhir. Silakan login kembali.",
            confirmButtonText: "Login",
            confirmButtonColor: "#6B4423",
        });

        if (loginUrl) {
            window.location.href = loginUrl;
        }

        throw new Error("UNAUTHORIZED");
    }

    if (response.status === 403) {
        await Swal.fire({
            icon: "error",
            title: "Akses Ditolak",
            text:
                data?.message ||
                "Kamu tidak memiliki izin untuk melakukan tindakan ini.",
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#6B4423",
        });

        throw new Error("FORBIDDEN");
    }

    if (response.status === 404) {
        await Swal.fire({
            icon: "error",
            title: "Data Tidak Ditemukan",
            text: data?.message || "Data yang kamu cari tidak ditemukan.",
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#6B4423",
        });

        throw new Error("NOT_FOUND");
    }

    if (response.status === 503) {
        await Swal.fire({
            icon: "warning",
            title: "Koneksi Bermasalah",
            text:
                data?.message ||
                "Layanan sedang tidak tersedia. Silakan coba lagi.",
            confirmButtonText: "Coba Lagi",
            confirmButtonColor: "#6B4423",
        });

        throw new Error("SERVICE_UNAVAILABLE");
    }

    if (response.status >= 500) {
        await Swal.fire({
            icon: "error",
            title: "Terjadi Kesalahan",
            text:
                data?.message ||
                "Terjadi kesalahan pada server. Silakan coba lagi.",
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#6B4423",
        });

        throw new Error("SERVER_ERROR");
    }
}

export async function apiFetch(url, options = {}) {
    const method = (options.method || "GET").toUpperCase();

    const headers = {
        Accept: "application/json",
        ...(options.headers || {}),
    };

    if (method !== "GET") {
        const csrfToken = getCsrfToken();
        const xsrfToken = getXsrfToken();

        if (csrfToken) {
            headers["X-CSRF-TOKEN"] = csrfToken;
        }

        if (xsrfToken) {
            headers["X-XSRF-TOKEN"] = xsrfToken;
        }
    }

    let body = options.body;

    if (body instanceof FormData) {
        // Leave the body untouched.
    } else if (body && typeof body !== "string") {
        headers["Content-Type"] = "application/json";

        body = JSON.stringify(body);
    }

    let response;

    try {
        response = await fetch(url, {
            ...options,
            headers,
            body,
            credentials: "same-origin",
        });
    } catch (error) {
        await Swal.fire({
            icon: "warning",
            title: "Koneksi Bermasalah",
            text: "Tidak dapat terhubung ke server. Periksa koneksi internet dan coba lagi.",
            confirmButtonText: "Mengerti",
            confirmButtonColor: "#6B4423",
        });

        throw new Error("NETWORK_ERROR");
    }

    let data;

    try {
        data = await response.json();
    } catch {
        data = {
            message: "Server mengirim respons yang tidak dapat diproses.",
        };
    }

    if (!response.ok) {
        await handleApiError(response, data);
    }

    return {
        response,
        data,
    };
}
