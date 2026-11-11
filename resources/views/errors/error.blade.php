<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v={{ filemtime(public_path('favicon.ico')) }}" />

    <title>{{ $code }} - {{ $title }}</title>

    @vite (["resources/css/app.css", "resources/js/app.js"])
</head>

<body class="m-0 min-h-screen bg-ckb-background text-ckb-on-surface">
    <main
        class="fixed inset-0 z-[9999] flex min-h-screen w-screen items-center justify-center"
    >
        <div class="w-full max-w-lg px-6 text-center">
            <div class="text-7xl font-bold text-ckb-primary">{{ $code }}</div>

            <h1 class="mt-4 text-2xl font-semibold text-ckb-on-surface">
                {{ $title }}
            </h1>

            <p class="mt-3 text-ckb-on-surface-variant">{{ $message }}</p>

            <div class="mt-8 flex justify-center gap-3">
                <button
                    type="button"
                    onclick="history.back()"
                    class="rounded-lg border border-ckb-outline-variant px-5 py-2.5 text-ckb-on-surface transition hover:bg-ckb-surface-container-low"
                >
                    Kembali
                </button>

                <button
                    type="button"
                    onclick="window.location.reload()"
                    class="rounded-lg bg-ckb-primary px-5 py-2.5 text-ckb-on-primary transition hover:bg-ckb-accent hover:text-ckb-on-accent"
                >
                    Coba Lagi
                </button>
            </div>
        </div>
    </main>
</body>
</html>
