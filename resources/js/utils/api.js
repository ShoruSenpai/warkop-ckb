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

async function handleApiError(response) {
    if (response.status === 401) {
        await Swal.fire({
            icon: "warning",
            title: "Sesi Berakhir",
            text: "Sesi login kamu sudah berakhir. Silakan login kembali.",
            confirmButtonText: "Login",
        });

        if (loginUrl) {
            window.location.href = loginUrl;
        }

        throw new Error("UNAUTHORIZED");
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
        await handleApiError(response);
    }

    return {
        response,
        data,
    };
}
