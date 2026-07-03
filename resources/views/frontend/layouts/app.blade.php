<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'BrightCon Construction & Engineering')</title>
    @vite(['resources/css/app.css', 'resources/css/frontend.css', 'resources/js/app.js'])
</head>
<body>
    <div class="frontend-topbar py-2">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2">
            <span>Top mini bar placeholder: certifications, working hours, or announcement.</span>
            <span>Email: info@example.com | Phone: +1 000 000 0000</span>
        </div>
    </div>
    <header class="frontend-header bg-white py-3">
        <div class="container d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
            <a class="h3 text-decoration-none brand-mark mb-0" href="{{ route('home') }}">BrightCon</a>
            <div class="text-muted">Header/logo/contact placeholder for future CMS-managed company details.</div>
        </div>
    </header>
    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav gap-lg-2">
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">Services</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="nav-item"><a class="nav-link" href="#competency">Competency</a></li>
                    <li class="nav-item"><a class="nav-link" href="#equipment-list">Equipment List</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}" href="{{ route('gallery.index') }}">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <main>@yield('content')</main>
    <footer class="frontend-footer py-5"><div class="container"><div class="row g-4"><div class="col-md-6"><h5>Footer Placeholder</h5><p>Future CMS-controlled footer content, contact details, quick links, and social links.</p></div><div class="col-md-6"><h5>Company Links Placeholder</h5><p>Footer links will be managed in a later CMS phase.</p></div></div></div></footer>
    <div class="copyright-bar py-3"><div class="container">Copyright bar placeholder © {{ date('Y') }} BrightCon.</div></div>
</body>
</html>
