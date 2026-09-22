<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login' }} - Warkop Cak Kebo Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans bg-[#FBF7F2]">
<div class="bg-white w-full max-w-[440px] rounded-3xl p-8 sm:p-10 shadow-[0_10px_40px_rgba(0,0,0,0.04)] border border-[#F2EAE1]/80">

    <div class="text-center">
        <h1 class="text-2xl font-bold text-[#2A1713] tracking-tight">Warkop Cak Kebo</h1>
        <p class="text-xs text-[#8C766C] mt-1 font-medium">Sistem Manajemen POS & Inventori</p>
    </div>

    <hr class="my-7 border-t border-[#F0E5DA]">

    <div class="mb-6">
        <h2 class="text-lg font-bold text-[#2A1713]">Selamat datang kembali</h2>
        <p class="text-xs text-[#8C766C] mt-1">Masuk ke panel admin untuk melanjutkan.</p>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-2.5 rounded-xl mb-5 text-xs">
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-2.5 rounded-xl mb-5 text-xs">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('login.process') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-xs font-semibold text-[#2A1713] mb-1.5">
                Alamat Email
            </label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email') }}"
                placeholder="admin@ckb.id"
                required
                class="w-full px-4 py-3 bg-[#FCF9F6] border border-[#E8DDD1] rounded-xl text-sm text-[#2A1713] placeholder-[#B5A499] focus:outline-none focus:border-[#7A5B49] focus:ring-1 focus:ring-[#7A5B49] transition duration-150"
            >
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-[#2A1713] mb-1.5">
                Password
            </label>
            <div class="relative">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="••••••••"
                    required
                    class="w-full px-4 py-3 pr-11 bg-[#FCF9F6] border border-[#E8DDD1] rounded-xl text-sm text-[#2A1713] placeholder-[#B5A499] focus:outline-none focus:border-[#7A5B49] focus:ring-1 focus:ring-[#7A5B49] transition duration-150"
                >
                <button
                    type="button"
                    onclick="togglePasswordVisibility()"
                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-[#9E8B80] hover:text-[#2A1713] transition-colors"
                    aria-label="Tampilkan Password"
                >
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="pt-2">
            <button
                type="submit"
                class="w-full bg-[#241411] hover:bg-[#38211C] text-white text-sm font-semibold py-3.5 px-4 rounded-xl shadow-sm transition duration-150 active:scale-[0.99]"
            >
                Masuk ke Dashboard
            </button>
        </div>
    </form>

    <p class="text-center text-[11px] font-mono text-[#8C766C] mt-7">
        Demo: admin@ckb.id · ckb2026
    </p>
</div>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
        } else {
            passwordInput.type = 'password';
        }
    }
</script>
</body>
</html>
