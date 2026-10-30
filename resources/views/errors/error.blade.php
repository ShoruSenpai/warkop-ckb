<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $code }} - {{ $title }}</title>

    @vite (["resources/css/app.css", "resources/js/app.js"])
</head>

<body class="m-0 min-h-screen bg-surface">
    <main
        class="fixed inset-0 z-[9999] flex min-h-screen w-screen items-center justify-center"
    >
        <div class="w-full max-w-lg px-6 text-center">
            <div class="text-7xl font-bold text-slate-800">{{ $code }}</div>

            <h1 class="mt-4 text-2xl font-semibold text-slate-900">
                {{ $title }}
            </h1>

            <p class="mt-3 text-slate-500">{{ $message }}</p>

            <div class="mt-8 flex justify-center gap-3">
                <button
                    type="button"
                    onclick="history.back()"
                    class="rounded-lg border border-slate-300 px-5 py-2.5 text-slate-700 transition hover:bg-slate-50"
                >
                    Kembali
                </button>

                <button
                    type="button"
                    onclick="window.location.reload()"
                    class="rounded-lg bg-slate-900 px-5 py-2.5 text-white transition hover:bg-slate-800"
                >
                    Coba Lagi
                </button>
            </div>
        </div>
    </main>
</body>
</html>
