<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title inertia>{{ config('app.name', 'Laravel Starter Template') }}</title>
    <script>
        // https://ui.nuxt.com/docs/getting-started/integrations/ssr
        const theme = localStorage.getItem('vueuse-color-scheme') || 'auto'
        if (theme === 'dark' || (theme === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

    <link
        rel="icon"
        href="/favicon.ico"
        sizes="any"
    >
    <link
        rel="icon"
        href="/favicon.svg"
        type="image/svg+xml"
    >
    <link
        rel="apple-touch-icon"
        href="/apple-touch-icon.png"
    >

    <link
        rel="preconnect"
        href="https://fonts.bunny.net"
        crossorigin
    >
    <link
        rel="preload"
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap"
        as="style"
    >
    <link
        href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600&display=swap"
        rel="stylesheet"
    />

    @routes
    @inertiaHead
    @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
</head>

<body>
    <div class="isolate">
        @inertia
    </div>
</body>

</html>
