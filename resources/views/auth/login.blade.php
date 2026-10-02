<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Login' }} - Warkop Cak Kebo Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-dvh overflow-hidden flex flex-col bg-ckb-background text-ckb-on-surface font-sans">

{{-- ================= HEADER ================= --}}
<header class="h-14 shrink-0 bg-white border-b border-ckb-surface-container px-4 sm:px-6 lg:px-8 flex items-center justify-between">

    <div class="flex items-center gap-2.5">
        <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-lg bg-ckb-primary flex items-center justify-center">
            <i class="fa-solid fa-cash-register text-sm sm:text-base text-ckb-secondary-container"></i>
        </div>

        <div>
            <h1 class="text-sm sm:text-base font-bold tracking-tight">
                Warkop Cak Kebo
            </h1>

            <p class="hidden sm:block text-[9px] text-ckb-on-surface-variant leading-none">
                POS & Inventori
            </p>
        </div>
    </div>

    <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-full bg-ckb-surface-container-low text-[9px] sm:text-[10px] font-semibold text-ckb-on-surface-variant">
        <i class="fa-solid fa-circle text-[5px] text-ckb-secondary-container"></i>

        <span class="hidden sm:inline">
            Online - Cloud Sync Active
        </span>

        <span class="sm:hidden">
            Online
        </span>
    </div>

</header>


{{-- ================= MAIN ================= --}}
<main class="relative min-h-0 flex-1 flex items-center justify-center px-3 sm:px-5 py-4 overflow-hidden">

    {{-- Background Glow --}}
    <div class="pointer-events-none absolute inset-0
        bg-[radial-gradient(circle_at_50%_0%,rgba(16,185,129,0.07),transparent_40%)]">
    </div>


    {{-- ================= LOGIN CARD ================= --}}
    <div class="relative w-full max-w-[500px] max-h-full bg-white rounded-2xl sm:rounded-3xl border border-ckb-surface-container shadow-[0_12px_35px_rgba(25,28,30,0.07)] overflow-hidden">

        {{-- Accent --}}
        <div class="h-1 bg-linear-to-r from-ckb-secondary via-ckb-secondary-container to-[#dbe8ff]"></div>


        <div class="px-5 py-5 sm:px-7 sm:py-6 lg:px-8 lg:py-7">

            {{-- Status --}}
            <div class="flex items-center justify-between mb-4 sm:mb-5">

                <div class="flex items-center gap-1.5 rounded-full bg-ckb-surface-container-low px-2.5 py-1.5 text-[9px] sm:text-[10px] font-semibold text-ckb-on-surface-variant">
                    <i class="fa-solid fa-circle text-[5px] text-ckb-secondary-container"></i>
                    Status: Online
                </div>

                <span class="px-2 py-1 rounded-md bg-ckb-surface-container text-[9px] font-bold text-ckb-on-surface-variant">
                    POS • v2.4
                </span>

            </div>


            {{-- Icon --}}
            <div class="flex justify-center mb-3">

                <div class="w-12 h-12 rounded-xl bg-ckb-surface-container-low flex items-center justify-center">

                    <div class="w-9 h-9 rounded-lg bg-ckb-primary flex items-center justify-center">
                        <i class="fa-solid fa-cash-register text-lg text-ckb-secondary-container"></i>
                    </div>

                </div>

            </div>


            {{-- Title --}}
            <div class="text-center mb-5">

                <h2 class="text-[23px] sm:text-[25px] lg:text-[27px] leading-tight font-extrabold tracking-tight">
                    Masuk ke Sistem POS
                </h2>

                <p class="mt-1.5 text-xs sm:text-sm text-ckb-on-surface-variant">
                    Sistem Manajemen POS & Inventori Terpadu
                </p>

            </div>


            {{-- Error --}}
            @if($errors->any())
                <div class="mb-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-ckb-error-container/60 border border-ckb-error/20 text-ckb-on-error-container text-[10px]">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif


            {{-- Success --}}
            @if(session('success'))
                <div class="mb-3 flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-[10px]">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif


            {{-- ================= FORM ================= --}}
            <form action="{{ route('login.process') }}" method="POST" class="space-y-3.5">
                @csrf


                {{-- Email --}}
                <div>

                    <div class="flex items-center justify-between mb-1.5">

                        <label
                            for="email"
                            class="text-xs sm:text-sm font-bold"
                        >
                            Email / ID Karyawan
                        </label>

                        <span class="hidden sm:block text-[9px] text-ckb-on-surface-variant">
                            NIP / Akun Resmi
                        </span>

                    </div>

                    <div class="relative">

                        <i class="fa-solid fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-ckb-on-surface-variant"></i>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="admin@ckb.id"
                            autocomplete="username"
                            required
                            class="w-full h-11 sm:h-12 pl-10 pr-3 text-xs sm:text-sm bg-white border border-ckb-outline-variant/60 rounded-lg sm:rounded-xl text-ckb-on-surface placeholder-ckb-outline focus:outline-none focus:border-ckb-secondary focus:ring-2 focus:ring-ckb-secondary/10 transition"
                        >

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
                            class="text-[10px] sm:text-xs font-semibold text-ckb-secondary hover:text-ckb-primary transition"
                        >
                            Lupa kata sandi?
                        </a>

                    </div>

                    <div class="relative">

                        <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-ckb-on-surface-variant"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••••••"
                            autocomplete="current-password"
                            required
                            class="w-full h-11 sm:h-12 pl-10 pr-11 text-xs sm:text-sm bg-white border border-ckb-outline-variant/60 rounded-lg sm:rounded-xl text-ckb-on-surface placeholder-ckb-outline focus:outline-none focus:border-ckb-secondary focus:ring-2 focus:ring-ckb-secondary/10 transition"
                        >

                        <button
                            type="button"
                            onclick="togglePasswordVisibility()"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-ckb-on-surface-variant hover:text-ckb-on-surface transition"
                            aria-label="Tampilkan Password"
                        >
                            <i id="eye-open" class="fa-solid fa-eye text-sm"></i>
                            <i id="eye-closed" class="fa-solid fa-eye-slash text-sm hidden"></i>
                        </button>

                    </div>

                </div>


                {{-- Remember --}}
                <div class="flex items-center justify-between pt-0.5">

                    <label class="flex items-center gap-2 cursor-pointer select-none">

                        <input
                            type="checkbox"
                            name="remember"
                            class="w-4 h-4 rounded border-ckb-outline-variant text-ckb-tertiary focus:ring-ckb-tertiary"
                        >

                        <span class="text-[10px] sm:text-xs font-medium text-ckb-on-surface-variant">
                            Ingat sesi di perangkat terminal ini
                        </span>

                    </label>

                    <span class="hidden sm:inline-flex px-1.5 py-1 rounded bg-ckb-surface-container text-[8px] font-bold text-ckb-on-surface-variant">
                        Shift 1
                    </span>

                </div>


                {{-- Button --}}
                <div class="pt-1">

                    <button
                        type="submit"
                        class="w-full h-11 sm:h-12 flex items-center justify-center gap-2 bg-ckb-secondary-container hover:bg-ckb-secondary text-white text-xs sm:text-sm font-bold rounded-lg sm:rounded-xl shadow-[0_5px_14px_rgba(0,108,73,0.16)] transition active:scale-[0.99]"
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
                    class="inline-flex items-center gap-1.5 text-[10px] sm:text-xs font-medium text-ckb-on-surface-variant hover:text-ckb-on-surface transition"
                >
                    <i class="fa-solid fa-headset"></i>
                    Butuh Bantuan Dukungan Teknis?
                </a>

            </div>


            {{-- Security --}}
            <div class="mt-2.5 flex items-center justify-center gap-1.5 text-[8px] sm:text-[10px] font-semibold text-ckb-on-surface-variant">

                <i class="fa-solid fa-shield-halved text-ckb-secondary"></i>

                Enkripsi End-to-End 256-Bit • Sesi Terproteksi

            </div>

        </div>

    </div>

</main>


{{-- ================= FOOTER ================= --}}
<footer class="h-10 shrink-0 bg-white border-t border-ckb-surface-container px-4 sm:px-6 lg:px-8 flex items-center justify-between">

    <div class="flex items-center gap-1.5 text-[8px] sm:text-[9px] font-medium text-ckb-on-surface-variant">

        <i class="fa-solid fa-lock text-ckb-secondary"></i>

        <span>
            SSL/TLS 256-bit Terverifikasi
        </span>

    </div>

    <div class="text-[8px] sm:text-[9px] font-semibold text-ckb-on-surface-variant text-right">
        Warkop Cak Kebo POS • Terminal Admin
    </div>

</footer>


<script>
    function togglePasswordVisibility() {
        const password = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        const showPassword = password.type === 'password';

        password.type = showPassword ? 'text' : 'password';

        eyeOpen.classList.toggle('hidden', showPassword);
        eyeClosed.classList.toggle('hidden', !showPassword);
    }
</script>

</body>
</html>
