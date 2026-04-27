<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'GIS Cuaca Jatim')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="m-0 p-0 font-sans bg-slate-50 h-screen flex flex-col">
    @include('partials.navbar')
    
    @yield('content')

    @stack('scripts')
</body>
</html>