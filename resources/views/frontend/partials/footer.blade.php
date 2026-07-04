@php use Illuminate\Support\Facades\Storage; @endphp
<footer class="frontend-footer py-5">
    <div class="container">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="row g-4 g-lg-5">
            <div class="col-lg-4">
                <div class="mb-3">
                    @if($siteSettings?->logo)<img src="{{ Storage::url($siteSettings->logo) }}" alt="{{ $siteSettings?->company_name }}" class="footer-logo">@else<h5>{{ $siteSettings?->company_name ?: config('app.name') }}</h5>@endif
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
                <form method="POST" action="{{ route('contact.store') }}" class="footer-contact-form mt-3" novalidate>
                    @csrf
                    <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="row g-3">
                        <div class="col-md-6"><input class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" placeholder="Full Name *" required>@error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email Address *" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><input class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Phone">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-md-6"><input class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" placeholder="Subject">@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="4" placeholder="Message *" required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                        <div class="col-12"><button class="btn btn-primary-brand" type="submit">Send Message</button></div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</footer>
