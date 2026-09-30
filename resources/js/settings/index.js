import { apiFetch } from "../utils/api.js";

document.addEventListener("DOMContentLoaded", () => {
    const formProfile = document.getElementById("form-profile-settings");
    const usernameInput = document.getElementById("setting-username");
    const fullNameInput = document.getElementById("setting-full-name");
    const emailInput = document.getElementById("setting-email");
    const passwordInput = document.getElementById("setting-password");

    async function loadSettings() {
        try {
            const { data } = await apiFetch('/api/settings');
            const user = data.data.user;

            if (user) {
                usernameInput.value = user.username || '';
                fullNameInput.value = user.full_name || '';
                emailInput.value = user.email || '';
            }
        } catch (err) {
            console.error("Gagal memuat pengaturan:", err);
        }
    }

    if (formProfile) {
        formProfile.addEventListener("submit", async (e) => {
            e.preventDefault();

            const payload = {
                email: emailInput.value.trim(),
                full_name: fullNameInput.value.trim(),
            };

            if (passwordInput.value.trim()) {
                payload.password = passwordInput.value.trim();
            }

            try {
                const { data } = await apiFetch('/api/settings/profile', {
                    method: 'PUT',
                    body: payload,
                });

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message || 'Profil berhasil diperbarui!',
                    confirmButtonColor: '#6B4423',
                });

                passwordInput.value = '';
            } catch (err) {
                // error handled by apiFetch sweetalert
            }
        });
    }

    loadSettings();
});
