<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | BrightCon</title>
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
                            <div class="brand-mark h3">BrightCon Admin</div>
                            <p class="text-muted mb-0">Sign in to the back office.</p>
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
                <p class="text-center text-muted small mt-3">Default local credentials are seeded for development only.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
