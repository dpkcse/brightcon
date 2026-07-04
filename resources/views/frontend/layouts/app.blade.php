<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', ($siteSettings?->company_name ?: config('app.name')).' | Construction & Engineering')</title>
    @vite(['resources/css/app.css', 'resources/css/frontend.css', 'resources/js/app.js'])
    @include('frontend.partials.theme-css')
</head>
<body>
    @include('frontend.partials.top-mini-bar')
    @include('frontend.partials.header')
    @include('frontend.partials.navigation')

    <main>@yield('content')</main>

    @include('frontend.partials.footer')
    @include('frontend.partials.copyright')
</body>
</html>
