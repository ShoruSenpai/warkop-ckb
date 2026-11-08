<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="login-url" content="{{ route('login') }}" />
    <title>
        {{
            $title ??
                "Dashboard"
        }} - Warkop Cak Kebo
    </title>

    @vite (["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="bg-ckb-background flex h-dvh overflow-hidden font-sans">
    <x-sidebar />

    <div
        class="flex min-w-0 flex-1 flex-col h-dvh overflow-hidden bg-ckb-background"
    >
        <x-navbar :title="$title ?? 'Dashboard'" />

        <main class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto bg-ckb-background p-4 sm:p-5">
            {{ $slot }}
        </main>
    </div>

    {{ $script ?? "" }}
</body>
</html>
