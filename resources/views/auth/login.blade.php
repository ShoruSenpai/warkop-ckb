<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        rel="icon"
        type="image/x-icon"
        href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}"
    />

    <title>
        {{
            $title ??
                "Login"
        }} - Warkop Cak Kebo Admin
    </title>

    @vite (["resources/css/app.css", "resources/js/app.js"])
</head>

<body
    class="h-dvh overflow-hidden flex flex-col bg-ckb-background text-ckb-on-surface font-sans"
>
    {{-- ================= HEADER ================= --}}
    <header
        class="h-14 shrink-0 bg-ckb-surface border-b border-ckb-outline-variant/50 px-4 sm:px-6 lg:px-8 flex items-center justify-between"
    >
        <div class="flex items-center gap-2.5">
            <div
                class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-ckb-primary flex items-center justify-center"
            >
                <i
                    class="fa-solid fa-cash-register text-sm sm:text-base text-ckb-on-primary"
                ></i>
            </div>

            <div>
                <h1 class="text-sm sm:text-base font-bold tracking-tight">
                    Warkop Cak Kebo
                </h1>

                <p class="hidden text-[13px] leading-none text-ckb-on-surface-variant sm:block">POS & Inventori</p>
            </div>
        </div>

        <div
            class="flex items-center gap-1.5 rounded-full bg-ckb-surface-container-low px-2.5 py-1.5 text-[13px] font-semibold text-ckb-on-surface-variant"
        >
            <i
                class="fa-solid fa-circle text-[5px] text-ckb-success"
            ></i>

            <span class="hidden sm:inline">Portal Admin</span>

            <span class="sm:hidden">Admin</span>
        </div>
    </header>

    {{-- ================= MAIN ================= --}}
    <main
        class="relative min-h-0 flex-1 flex items-center justify-center px-3 sm:px-5 py-4 overflow-hidden"
    >
        {{-- ================= LOGIN CARD ================= --}}
        <div
            class="relative max-h-full w-full max-w-[500px] overflow-y-auto rounded-2xl border border-ckb-outline-variant/50 bg-ckb-surface shadow-floating sm:rounded-3xl"
        >
            {{-- Accent --}}
            <div
                class="h-1 bg-linear-to-r from-ckb-primary via-ckb-accent to-ckb-tertiary"
            ></div>

            <div class="px-5 py-5 sm:px-7 sm:py-6 lg:px-8 lg:py-7">
                {{-- Status --}}
                <div class="mb-4 flex items-center justify-between sm:mb-5">
                    <div
                        class="flex items-center gap-1.5 rounded-full bg-ckb-surface-container-low px-2.5 py-1.5 text-[13px] font-semibold text-ckb-on-surface-variant"
                    >
                        <i
                            class="fa-solid fa-circle text-[5px] text-ckb-success"
                        ></i>
                        Portal Admin
                    </div>

                    <span
                        class="rounded-md bg-ckb-surface-container px-2 py-1 text-[13px] font-bold text-ckb-on-surface-variant"
                    >
                        Warkop Cak Kebo
                    </span>
                </div>

                {{-- Icon --}}
                <div class="flex justify-center mb-3">
                    <div
                        class="w-12 h-12 rounded-xl bg-ckb-surface-container-low flex items-center justify-center"
                    >
                        <div
                            class="w-9 h-9 rounded-lg bg-ckb-primary flex items-center justify-center"
                        >
                            <i
                                class="fa-solid fa-cash-register text-lg text-ckb-on-primary"
                            ></i>
                        </div>
                    </div>
                </div>

                {{-- Title --}}
                <div class="text-center mb-5">
                    <h2
                        class="text-[23px] sm:text-[25px] lg:text-[27px] leading-tight font-extrabold tracking-tight"
                    >
                        Masuk ke Sistem POS
                    </h2>

                    <p class="mt-1.5 text-xs sm:text-sm text-ckb-on-surface-variant">Sistem Manajemen POS & Inventori Terpadu</p>
                </div>

                {{-- Error --}}
                @if ($errors->any())
                    <div
                        class="mb-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-ckb-error-container/60 border border-ckb-error/20 text-ckb-on-error-container text-[13px]"
                    >
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Success --}}
                @if (session("success"))
                    <div
                        class="mb-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-ckb-success-container border border-ckb-success/20 text-ckb-on-success-container text-[13px]"
                    >
                        <i class="fa-solid fa-circle-check"></i>
                        <span>{{
                            session(
                                "success",
                            )
                        }}</span>
                    </div>
                @endif

                {{-- ================= FORM ================= --}}
                <form
                    action="{{ route('login.process') }}"
                    method="POST"
                    class="space-y-3.5"
                >
                    @csrf

                    {{-- Email --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label
                                for="email"
                                class="text-xs sm:text-sm font-bold"
                            >
                                Email
                            </label>

                            <span
                                class="hidden text-[13px] text-ckb-on-surface-variant sm:block"
                            >
                                Akun Resmi
                            </span>
                        </div>

                        <div class="relative">
                            <i
                                class="fa-solid fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-ckb-on-surface-variant"
                            ></i>

                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="admin@ckb.id"
                                autocomplete="username"
                                required
                                class="w-full h-11 sm:h-12 pl-10 pr-3 text-xs sm:text-sm bg-ckb-surface border border-ckb-outline-variant/60 rounded-lg sm:rounded-xl text-ckb-on-surface placeholder-ckb-on-surface-variant focus:outline-none focus:border-ckb-accent focus:ring-2 focus:ring-ckb-accent/10 transition"
                            />
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label
                                for="password"
                                class="text-xs sm:text-sm font-bold"
                            >
                                Kata Sandi
                            </label>

                            <a
                                href="#"
                                class="text-[13px] font-semibold text-ckb-accent hover:text-ckb-primary transition"
                            >
                                Lupa kata sandi?
                            </a>
                        </div>

                        <div class="relative">
                            <i
                                class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-ckb-on-surface-variant"
                            ></i>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="••••••••••••"
                                autocomplete="current-password"
                                required
                                class="w-full h-11 sm:h-12 pl-10 pr-11 text-xs sm:text-sm bg-ckb-surface border border-ckb-outline-variant/60 rounded-lg sm:rounded-xl text-ckb-on-surface placeholder-ckb-on-surface-variant focus:outline-none focus:border-ckb-accent focus:ring-2 focus:ring-ckb-accent/10 transition"
                            />

                            <button
                                type="button"
                                onclick="togglePasswordVisibility()"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ckb-on-surface-variant hover:text-ckb-on-surface transition"
                                aria-label="Tampilkan kata sandi"
                                aria-controls="password"
                                aria-pressed="false"
                            >
                                <i
                                    id="password-eye"
                                    class="fa-solid fa-eye-slash text-sm"
                                    aria-hidden="true"
                                ></i>
                            </button>
                        </div>
                    </div>

                    {{-- Button --}}
                    <div class="pt-1">
                        <button
                            type="submit"
                            class="w-full h-11 sm:h-12 flex items-center justify-center gap-2 bg-ckb-primary hover:bg-ckb-accent text-ckb-on-primary hover:text-ckb-on-accent text-xs sm:text-sm font-bold rounded-lg sm:rounded-xl shadow-surface transition active:scale-[0.99]"
                        >
                            Masuk ke Terminal
                            <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>
                </form>

                {{-- Support --}}
                <div class="mt-4 text-center">
                    <a
                        href="#"
                        class="inline-flex items-center gap-1.5 text-[13px] font-medium text-ckb-on-surface-variant hover:text-ckb-on-surface transition"
                    >
                        <i class="fa-solid fa-headset"></i>
                        Butuh Bantuan Dukungan Teknis?
                    </a>
                </div>

                {{-- Security --}}
                <div
                    class="mt-2.5 flex items-center justify-center gap-1.5 text-[13px] font-semibold text-ckb-on-surface-variant"
                >
                    <i class="fa-solid fa-shield-halved text-ckb-accent"></i>

                    Gunakan akun resmi untuk masuk.
                </div>
            </div>
        </div>
    </main>

    {{-- ================= FOOTER ================= --}}
    <footer
        class="h-10 shrink-0 bg-ckb-surface border-t border-ckb-outline-variant/50 px-4 sm:px-6 lg:px-8 flex items-center justify-between"
    >
        <div
            class="flex items-center gap-1.5 text-[13px] font-medium text-ckb-on-surface-variant"
        >
            <span>Akses khusus akun terdaftar</span>
        </div>

        <div
            class="text-[13px] font-semibold text-ckb-on-surface-variant text-right"
        >
            Warkop Cak Kebo POS • Terminal Admin
        </div>
    </footer>

    <script>
        function togglePasswordVisibility() {
            const password = document.getElementById("password");
            const eye = document.getElementById("password-eye");

            const showPassword = password.type === "password";
            const toggle = document.querySelector('[aria-controls="password"]');

            password.type = showPassword ? "text" : "password";
            toggle.setAttribute("aria-pressed", String(showPassword));
            toggle.setAttribute(
                "aria-label",
                showPassword ? "Sembunyikan kata sandi" : "Tampilkan kata sandi",
            );

            eye.classList.toggle("fa-eye", showPassword);
            eye.classList.toggle("fa-eye-slash", !showPassword);
        }
    </script>
</body>
</html>
