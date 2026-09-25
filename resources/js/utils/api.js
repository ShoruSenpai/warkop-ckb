const loginUrl = document.querySelector('meta[name="login-url"]')?.content;

/**
 * Read a browser cookie by name.
 */
function getCookie(name) {
    const cookies = document.cookie.split("; ");

    const cookie = cookies.find((item) => item.startsWith(`${name}=`));

    return cookie ? cookie.substring(name.length + 1) : null;
}

/**
 * Get the raw CSRF token from the meta tag.
 *
 * This value is used for X-CSRF-TOKEN.
 */
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

/**
 * Get the XSRF cookie value.
 *
 * Laravel expects the URL-decoded cookie value
 * in the X-XSRF-TOKEN header and will decrypt it
 * internally.
 */
function getXsrfToken() {
    const cookie = getCookie("XSRF-TOKEN");

    return cookie ? decodeURIComponent(cookie) : null;
}

export async function apiFetch(url, options = {}) {
    const method = (options.method || "GET").toUpperCase();

    const headers = {
        Accept: "application/json",
        ...(options.headers || {}),
    };

    /*
     * Add CSRF protection for state-changing requests.
     */
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

    /*
     * FormData is used for requests that contain files.
     *
     * Do not manually set Content-Type here.
     * The browser must generate the multipart boundary.
     */
    if (body instanceof FormData) {
        // Leave the body untouched.
    } else if (body && typeof body !== "string") {
        headers["Content-Type"] = "application/json";
        body = JSON.stringify(body);
    }

    const response = await fetch(url, {
        ...options,
        headers,
        body,
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

    return {
        response,
        data,
    };
}
