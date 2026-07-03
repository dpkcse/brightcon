<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Dashboard') | BrightCon</title>
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body>
<div class="admin-shell d-flex">
    @php($items = ['Dashboard','General Settings','Theme Settings','Homepage CMS','Sliders','Features','Projects','Services','Gallery','Contact Messages','Menus','Social Links','Footer Links'])
    <aside class="admin-sidebar d-none d-lg-flex flex-column">
        <div class="brand p-4 fw-bold fs-4">BrightCon Admin</div>
        <nav class="nav flex-column gap-1 p-3">
            @foreach($items as $item)
                <a class="nav-link {{ $item === 'Dashboard' ? 'active' : '' }}" href="{{ $item === 'Dashboard' ? route('admin.dashboard') : '#' }}">{{ $item }}</a>
            @endforeach
        </nav>
    </aside>
    <div class="admin-content flex-grow-1">
        <div class="admin-topbar px-4 py-3 d-flex justify-content-between align-items-center">
            <div><strong>@yield('page-heading', 'Dashboard')</strong><div class="small text-muted">CMS foundation for future modules</div></div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="btn btn-primary-brand btn-sm" type="submit">Logout</button>
            </form>
        </div>
        <main class="p-4">@yield('content')</main>
    </div>
</div>
</body>
</html>
