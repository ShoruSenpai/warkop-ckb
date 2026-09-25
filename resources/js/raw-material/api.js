const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
const loginUrl = document.querySelector('meta[name="login-url"]')?.content;
// const loginUrl = document.body.dataset.loginUrl;

export async function apiFetch(url, options = {}) {
    const method = (options.method || "GET").toUpperCase();

    const headers = {
        Accept: "application/json",
        ...(options.headers || {}),
    };

    const request = { ...options, headers };

    if (method !== "GET") {
        headers["X-CSRF-TOKEN"] = csrfToken;
    }

    if (options.body && typeof options.body !== "string") {
        headers["Content-Type"] = "application/json";
        options.body = JSON.stringify(options.body);
    }

    const response = await fetch(url, {
        ...options,
        headers,
        credentials: "same-origin",
    });

    let data;

    try {
        data = await response.json();
    } catch {
        data = {
            message: "Server mengirim respons yang tidak dapat diproses.",
        };
    }

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

    return { response, data };
}
