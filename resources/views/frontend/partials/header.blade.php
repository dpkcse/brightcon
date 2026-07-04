@php use App\Support\FrontendImage; @endphp
<header class="frontend-header bg-white py-3">
    <div class="container d-flex flex-column flex-lg-row align-items-center justify-content-between gap-3">
        <a class="brand-wrap text-decoration-none" href="{{ route('home') }}">
            @if($siteSettings?->logo)
                <img src="{{ FrontendImage::url($siteSettings->logo) }}" alt="{{ $siteSettings?->company_name ?: config('app.name') }}" class="frontend-logo">
            @else
                <span class="brand-mark">{{ $siteSettings?->company_name ?: config('app.name') }}</span>
            @endif
        </a>
        <div class="header-contact d-flex flex-column flex-md-row align-items-center gap-3 gap-xl-4">
            @if($siteSettings?->email)
                <a class="header-contact-item" href="mailto:{{ $siteSettings->email }}"><span>Email</span><strong>{{ $siteSettings->email }}</strong></a>
            @endif
            @if($siteSettings?->phone)
                <a class="header-contact-item" href="tel:{{ $siteSettings->phone }}"><span>Call Us</span><strong>{{ $siteSettings->phone }}</strong></a>
            @endif
            @if($siteSettings?->profile_pdf)
                <a class="btn btn-primary-brand" href="{{ FrontendImage::url($siteSettings->profile_pdf) }}" target="_blank" rel="noopener">Download Profile</a>
            @endif
        </div>
    </div>
</header>
