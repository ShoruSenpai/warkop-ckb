<!DOCTYPE html><html lang="id" style=""><head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Cak Kebo POS &amp; Workforce Hub</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
          },
          colors: {
            brand: {
              emerald: '#10b981',
              mint: '#34d399',
              sky: '#0284c7',
              cyan: '#06b6d4',
              violet: '#6366f1',
              amber: '#f59e0b',
              slate: '#0f172a',
            }
          },
          boxShadow: {
            'glow-emerald': '0 12px 30px -8px rgba(16, 185, 129, 0.35)',
            'glow-sky': '0 12px 30px -8px rgba(2, 132, 199, 0.3)',
            'card-float': '0 20px 50px -12px rgba(15, 23, 42, 0.08), 0 0 1px 1px rgba(226, 232, 240, 0.8)',
            'brand-glow': '0 20px 40px -15px rgba(99, 102, 241, 0.25)',
          },
          borderRadius: {
            '4xl': '2rem',
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .mesh-gradient {
      background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 35%, #eff6ff 70%, #faf5ff 100%);
    }
    .left-hero-gradient {
      background: radial-gradient(circle at 10% 20%, rgba(52, 211, 153, 0.25) 0%, transparent 40%),
                  radial-gradient(circle at 90% 80%, rgba(56, 189, 248, 0.3) 0%, transparent 45%),
                  radial-gradient(circle at 50% 50%, rgba(251, 191, 36, 0.15) 0%, transparent 50%),
                  linear-gradient(145deg, #ecfdf5 0%, #f0f9ff 50%, #f5f3ff 100%);
    }
    .glass-card {
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(16px);
      -webkit-backdrop-filter: blur(16px);
      border: 1px solid rgba(255, 255, 255, 0.9);
    }
    .badge-pill-pulse {
      animation: pulseGlow 3s infinite ease-in-out;
    }
    @keyframes pulseGlow {
      0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
      50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
    }
  </style>
</head>
<body class="min-h-screen w-full bg-slate-50 text-slate-800 antialiased flex items-center justify-center p-4 sm:p-6 lg:p-10 select-none overflow-x-hidden">

  <!-- Ambient Decorative Blurred Blobs in Canvas Background -->
  <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-200/40 rounded-full blur-3xl"></div>
    <div class="absolute top-1/2 -right-32 w-96 h-96 bg-sky-200/40 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 left-1/3 w-96 h-96 bg-amber-100/50 rounded-full blur-3xl"></div>
  </div>

  <!-- Main Split-Screen Container -->
  <div class="relative z-10 w-full max-w-6xl min-h-[640px] lg:h-[720px] bg-white rounded-3xl lg:rounded-4xl shadow-card-float border border-slate-100/80 overflow-hidden grid grid-cols-1 lg:grid-cols-12">

    <!-- LEFT SIDE: Branding, Colorful Illustration & Value Proposition (7 Cols on LG) -->
    <div class="lg:col-span-6 xl:col-span-7 left-hero-gradient p-8 sm:p-10 lg:p-14 flex flex-col justify-between relative overflow-hidden border-b lg:border-b-0 lg:border-r border-slate-100">

      <!-- Subtle Floating Geometric Accents -->
      <div class="absolute top-10 right-10 w-28 h-28 bg-gradient-to-tr from-sky-400/20 to-emerald-300/30 rounded-full blur-xl pointer-events-none"></div>
      <div class="absolute bottom-16 left-12 w-36 h-36 bg-gradient-to-br from-amber-300/25 to-pink-300/20 rounded-full blur-xl pointer-events-none"></div>

      <!-- Top Header / Brand Logo & Status -->
      <div class="relative z-10 flex items-center justify-between">
        <div class="flex items-center gap-3.5">
          <!-- Logo Cak Kebo Image Placeholder -->
          <div class="w-14 h-14 rounded-2xl bg-white shadow-md p-2 flex items-center justify-center border border-white/80 transition-transform hover:scale-105">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuAbwm1ru73_Vlv79GLiXvKL3oQKg91sUkWPaKOYLf1UfQF6-EubF2k5g2BUAao43auiqLBxsbz8hUtq9gueby1csltpE6lcDcR9Fgave2CjMojteLWYVPhsDFNctji3IJ5c4IGmrygGZVFWR1rAbWAcihO-45BgpIRJFcr9KQ0MTNa2EIQ3YJYxiYOQv6zvs81NEX5-aNTxZ7YbATVxuuaLHPbZwmFO25jp5pbyUSXLTuR9v4GjWD3fnrPa1D1Stuf5Sw" alt="Logo Cak Kebo" class="w-full h-full object-contain filter drop-shadow-sm">
          </div>
          <div>
            <div class="flex items-center gap-2">
              <span class="text-xl font-extrabold tracking-tight text-slate-900">Cak Kebo</span>
              <span class="px-2 py-0.5 text-[11px] font-bold tracking-wider text-emerald-800 bg-emerald-100/90 rounded-full border border-emerald-300/60 uppercase">Enterprise</span>
            </div>
            <p class="text-xs font-semibold text-slate-500">POS &amp; Workforce Management Hub</p>
          </div>
        </div>

        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/80 backdrop-blur-sm border border-emerald-200 text-xs font-semibold text-emerald-700 shadow-sm">
          <span class="w-2 h-2 rounded-full bg-emerald-500 badge-pill-pulse"></span>
          <span class="">Sistem Online • Server Aman</span>
        </div>
      </div>

      <!-- Middle Content: Welcome Title & Dimensional Feature Badges -->
      <div class="relative z-10 my-8 lg:my-0 space-y-6 max-w-xl">
        <div class="space-y-3">
          <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-gradient-to-r from-emerald-500/10 via-sky-500/10 to-amber-500/10 border border-slate-200/80 text-xs font-bold text-slate-700 shadow-sm">
            <span class="text-emerald-600 font-bold">✨ Versi Terkini 2026</span>
            <span class="text-slate-300">•</span>
            <span class="text-slate-600">Terintegrasi RBAC</span>
          </div>
          <h1 class="text-3xl sm:text-4xl lg:text-[40px] font-black tracking-tight text-slate-900 leading-[1.2]">Selamat&nbsp; Datang Kembali</h1>
          <p class="text-sm sm:text-base text-slate-600 font-medium leading-relaxed">
            Platform komprehensif kasir, logistik bahan baku, presensi shift, dan audit finansial khusus tim internal Warkop Cak Kebo.
          </p>
        </div>

        <!-- 3 Feature Highlight Dimensional Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">

          <!-- Card 1: Kasir Cepat -->


          <!-- Card 2: Restock Otomatis -->


          <!-- Card 3: Rekap Finansial -->


        </div>
      </div>

      <!-- Bottom Trust / Role Indicators -->
      <div class="relative z-10 pt-4 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 font-semibold border-t border-slate-200/60">
        <div class="flex items-center gap-2">
          <i class="fa-solid fa-shield-halved text-emerald-600 text-sm"></i>
          <span class="">Enkripsi Sesi 256-bit • Akses Terisolasi Per-Role</span>
        </div>
        <div class="flex items-center gap-1.5 text-slate-400">
          <span class="">Warkop Cak Kebo HQ</span>
          <span class="">© 2026</span>
        </div>
      </div>

    </div>

    <!-- RIGHT SIDE: Clean, Floating Form Login (5 Cols on LG) -->
    <div class="lg:col-span-6 xl:col-span-5 bg-white p-8 sm:p-10 lg:p-12 flex flex-col justify-center relative">

      <!-- Subtle internal glow behind form -->
      <div class="absolute -top-16 -right-16 w-64 h-64 bg-emerald-100/60 rounded-full blur-3xl pointer-events-none"></div>
      <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-sky-100/50 rounded-full blur-3xl pointer-events-none"></div>

      <div class="w-full max-w-md mx-auto relative z-10">

        <!-- Form Header -->
        <div class="mb-7 text-center sm:text-left">
          <div class="inline-flex lg:hidden items-center justify-center w-12 h-12 rounded-2xl bg-slate-50 border border-slate-100 p-2 shadow-sm mb-3">
            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuDC8MtBpWm56yqN2unhJVRliOZanYGFntP76YQforCrkg42lsNP4qt_s3delVSvFDg8kJFXGBicpFHRg0J00EfaoxWYrPly3FccoD6wjKvOp9kVLd6rxz-qaJXa0L6U2lslLPXB-1wV-_pNLXlg85eX3aIQQRznUsqtZuE-PUBOjHRvdMPPzZXY8TIy-fyyOaVOuxrECsoc5Hyh34LOIHRurp7fd6bSQfVrWJ3KzhP4LXhh4jus69FNmVxqG_lQQ5lYgQ" alt="Logo" class="w-full h-full object-contain">
          </div>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Masuk Terminal &amp;&nbsp;<div class="">Sistem Operasional</div></h2>

        </div>

        <!-- Role Selector Pills (Quick Demo / RBAC Hint) -->


        <!-- Login Form Element -->
        <form id="loginForm" onsubmit="handleLoginSubmit(event)" class="space-y-5">

          <!-- Field 1: Email / Username -->
          <div>
            <label for="usernameInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
              Email atau Username Staff
            </label>
            <div class="relative rounded-2xl shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i class="fa-regular fa-envelope text-slate-400 text-sm"></i>
              </div>
              <input type="text" id="usernameInput" name="username" required="" value="alisa.yasmin@cakkebo.id" placeholder="nama.staff@cakkebo.id" class="block w-full pl-11 pr-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-slate-50/70 border border-slate-200 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition-all duration-200">
            </div>
          </div>

          <!-- Field 2: Password with Toggle Eye -->
          <div>
            <div class="flex items-center justify-between mb-2">
              <label for="passwordInput" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                Kata Sandi
              </label>
              <a href="javascript:void(0)" onclick="alert('Silakan hubungi Superadmin / Owner untuk reset kredensial akun staff Anda.')" class="text-xs font-bold text-sky-600 hover:text-sky-700 hover:underline transition-colors">
                Lupa Password?
              </a>
            </div>
            <div class="relative rounded-2xl shadow-sm">
              <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                <i class="fa-solid fa-lock text-slate-400 text-sm"></i>
              </div>
              <input type="password" id="passwordInput" name="password" required="" value="••••••••••••" placeholder="Masukkan kata sandi akun" class="block w-full pl-11 pr-12 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 bg-slate-50/70 border border-slate-200 rounded-2xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition-all duration-200">
              <button type="button" id="togglePasswordBtn" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors focus:outline-none" title="Tampilkan / Sembunyikan Password">
                <i id="eyeIcon" class="fa-regular fa-eye text-sm"></i>
              </button>
            </div>
          </div>

          <!-- Remember Me Checkbox -->
          <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2.5 cursor-pointer">
              <input type="checkbox" id="rememberMe" checked="" class="w-4 h-4 rounded text-emerald-600 border-slate-300 focus:ring-emerald-500 focus:ring-offset-0 cursor-pointer transition-all">
              <span class="text-xs font-semibold text-slate-600 select-none">Ingat sesi perangkat ini</span>
            </label>
            <span class="text-[11px] font-bold text-slate-400">Terminal #01 (Surabaya)</span>
          </div>

          <!-- Main CTA Action Button (Vibrant Emerald to Sky Gradient with Soft Glow) -->
          <div class="pt-2">
            <button type="submit" id="submitBtn" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-emerald-500 via-teal-500 to-sky-600 hover:from-emerald-600 hover:via-teal-600 hover:to-sky-700 text-white font-extrabold text-sm sm:text-base tracking-wide shadow-glow-emerald hover:shadow-glow-sky transform active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-3 cursor-pointer group">
              <span class="">Masuk ke Sistem Cak Kebo</span>
              <i class="fa-solid fa-arrow-right-long text-sm transition-transform group-hover:translate-x-1"></i>
            </button>
          </div>

        </form>

        <!-- Notification Banner / Feedback State -->
        <div id="loginFeedback" class="hidden mt-4 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2.5 transition-all">
          <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
          <div>
            <p class="font-bold" id="feedbackTitle">Autentikasi Berhasil!</p>
            <p class="text-[11px] text-emerald-700" id="feedbackMsg">Mengarahkan ke Dashboard POS &amp; Shift...</p>
          </div>
        </div>

        <!-- Bottom Assistance & Info -->
        <div class="mt-8 pt-5 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-xs font-medium text-slate-500">
          <div class="flex items-center gap-1.5 text-slate-600">
            <i class="fa-solid fa-headset text-slate-400"></i>
            <span class="">Butuh bantuan akun?</span>
            <a href="javascript:void(0)" onclick="alert('Hubungi WhatsApp Supervisor / Admin Bima: +62 812-3456-7890')" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline">Kontak Admin</a>
          </div>
          <div class="inline-flex items-center gap-1 text-[11px] text-slate-400 font-semibold">
            <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
            <span class="">Build 2.8.4</span>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Interactive JavaScript Logic for Form, Role Switching, & Password Toggle -->
  <script>
    function togglePasswordVisibility() {
      const passwordField = document.getElementById('passwordInput');
      const eyeIcon = document.getElementById('eyeIcon');
      if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }

    function selectRole(role, btnElement) {
      // Update UI button tabs
      document.querySelectorAll('.role-btn').forEach(btn => {
        btn.className = 'role-btn flex-1 py-2 px-2.5 rounded-xl text-slate-600 hover:text-slate-900 transition-all flex items-center justify-center gap-1.5';
      });

      btnElement.className = 'role-btn flex-1 py-2 px-2.5 rounded-xl bg-white text-emerald-700 shadow-sm border border-slate-200/80 transition-all flex items-center justify-center gap-1.5 font-bold';

      const usernameInput = document.getElementById('usernameInput');
      if (role === 'Kasir') {
        usernameInput.value = 'alisa.yasmin@cakkebo.id';
      } else if (role === 'Manager') {
        usernameInput.value = 'admin.bima@cakkebo.id';
      } else if (role === 'Owner') {
        usernameInput.value = 'yuski.owner@cakkebo.id';
      }
    }

    function handleLoginSubmit(event) {
      event.preventDefault();
      const submitBtn = document.getElementById('submitBtn');
      const feedback = document.getElementById('loginFeedback');
      const feedbackTitle = document.getElementById('feedbackTitle');
      const feedbackMsg = document.getElementById('feedbackMsg');
      const username = document.getElementById('usernameInput').value;

      submitBtn.disabled = true;
      submitBtn.classList.add('opacity-80', 'cursor-wait');
      submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-sm"></i><span>Memverifikasi Kredensial...</span>';

      setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-80', 'cursor-wait');
        submitBtn.innerHTML = '<span>Masuk ke Sistem Cak Kebo</span><i class="fa-solid fa-arrow-right-long text-sm"></i>';

        feedback.classList.remove('hidden');
        feedbackTitle.innerText = 'Autentikasi Berhasil!';
        feedbackMsg.innerText = `Sesi aktif untuk ${username}. Memuat ruang kerja...`;
      }, 700);
    }
  </script>








</body></html>
