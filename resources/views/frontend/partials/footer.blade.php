@php use App\Support\FrontendImage; @endphp
<footer class="frontend-footer py-5">
    <div class="container">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="mb-3">
                    @if($siteSettings?->logo)<img src="{{ FrontendImage::url($siteSettings->logo) }}" alt="{{ $siteSettings?->company_name }}" class="footer-logo">@else<h5>{{ $siteSettings?->company_name ?: config('app.name') }}</h5>@endif
                </div>
                @if($siteSettings?->address)<p>{{ $siteSettings->address }}</p>@endif
                @if($siteSettings?->phone)<p class="mb-1"><a href="tel:{{ $siteSettings->phone }}">{{ $siteSettings->phone }}</a></p>@endif
                @if($siteSettings?->email)<p><a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a></p>@endif
                @if($socialLinks->whereNotNull('url')->isNotEmpty())
                    <div class="footer-social d-flex flex-wrap gap-3 mt-3">
                        @foreach($socialLinks as $social)
                            @continue(blank($social->url))
                            <a href="{{ $social->url }}" target="_blank" rel="noopener" aria-label="{{ $social->platform }}">@if($social->icon_class)<i class="{{ $social->icon_class }}"></i>@else{{ $social->platform }}@endif</a>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-lg-3">
                <h5>Quick Links</h5>
                <ul class="footer-links list-unstyled mt-3">
                    @forelse($footerLinks as $link)
                        <li><a href="{{ str_starts_with($link->url, 'http') ? $link->url : url($link->url) }}" target="{{ in_array($link->target, ['_self', '_blank'], true) ? $link->target : '_self' }}" @if($link->target === '_blank') rel="noopener" @endif>{{ $link->label }}</a></li>
                    @empty
                        <li><a href="{{ route('about') }}">About</a></li><li><a href="{{ route('services.index') }}">Services</a></li><li><a href="{{ route('contact.index') }}">Contact</a></li>
                    @endforelse
                </ul>
            </div>
            <div class="col-lg-5">
                <h5>Send Us a Message</h5>
                @include('frontend.partials.contact-form', ['formClass' => 'footer-contact-form mt-3', 'rows' => 4, 'formId' => 'footer-contact'])
            </div>
        </div>
    </div>
</footer>
