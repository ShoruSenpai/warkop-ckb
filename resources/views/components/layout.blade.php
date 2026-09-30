<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        {{
            $title ??
                "Dashboard"
        }} - Warkop Cak Kebo
    </title>

    @vite (["resources/css/app.css", "resources/js/app.js"])

    {{-- hardcore code --}}
    <script>
        const LIMIT_CONFIG = {
            maxDigits: 9,
        };

        document.addEventListener("input", (event) => {
            const input = event.target;

            if (input.type !== "number") {
                return;
            }

            if (input.value.length > LIMIT_CONFIG.maxDigits) {
                input.value = input.value.slice(0, LIMIT_CONFIG.maxDigits);
            }
        });
    </script>
</head>
<body class="bg-ckb-background flex h-screen overflow-hidden font-sans">
    <x-sidebar />

    <div
        class="flex-1 flex flex-col h-screen overflow-hidden bg-ckb-background"
    >
        <x-navbar :title="$title ?? 'Dashboard'" />

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-ckb-bg p-6">
            {{ $slot }}
        </main>
    </div>

    {{ $script ?? "" }}
</body>
</html>
