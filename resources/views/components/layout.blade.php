<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - Warkop Cak Kebo</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-ckb-bg flex h-screen overflow-hidden font-sans">

<x-sidebar />

<div class="flex-1 flex flex-col h-screen overflow-hidden">

    <x-navbar :title="$title ?? 'Dashboard'" />

    <main class="flex-1 overflow-x-hidden overflow-y-auto bg-ckb-bg p-6">
        {{ $slot }}
    </main>
</div>

</body>
</html>
