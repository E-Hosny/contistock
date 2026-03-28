@php
    $locale = request()->cookie('contistock_locale', config('app.locale'));
    $locale = in_array($locale, ['ar', 'en']) ? $locale : config('app.locale');
    $dir = $locale === 'ar' ? 'rtl' : 'ltr';
    $lang = $locale === 'ar' ? 'ar' : 'en';
@endphp
<!DOCTYPE html>
<html lang="{{ $lang }}" dir="{{ $dir }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'ContiStock') }}</title>

        <!-- Fonts: Inter (EN), Cairo (AR) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
