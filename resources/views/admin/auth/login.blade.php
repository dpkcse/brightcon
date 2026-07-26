<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $settings = app(\App\Contracts\SettingsRepositoryInterface::class);
        $companyName = $settings->string('company_name') ?: config('cms.defaults.company_name');
        $productName = $settings->string('product_name') ?: config('cms.product.name', 'Buildora CMS');
        $companyLogo = $settings->string('logo');
    @endphp
    <title>Admin Login | {{ $companyName }}</title>
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
</head>
<body>
<div class="login-panel d-flex align-items-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <div class="text-center mb-4">
                            @if($companyLogo)<img src="{{ \App\Support\FrontendImage::url($companyLogo) }}" alt="{{ $companyName }}" class="frontend-logo mb-3">@endif
                            <div class="brand-mark h3">{{ $companyName }}</div>
                            <p class="text-muted mb-1">Website Administration</p>
                            @if($settings->bool('show_powered_by', true))<p class="text-muted small mb-0">{{ $settings->string('powered_by_text') ?: 'Powered by '.$productName }}</p>@endif
                        </div>
                        <form method="POST" action="{{ route('admin.login.submit') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-check mb-4"><input class="form-check-input" id="remember" name="remember" type="checkbox"><label class="form-check-label" for="remember">Remember me</label></div>
                            <button class="btn btn-primary-brand w-100" type="submit">Login</button>
                        </form>
                    </div>
                </div>
                <p class="text-center text-muted small mt-3">{{ config('cms.product.description') }}</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
