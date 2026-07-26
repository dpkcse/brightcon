@extends('frontend.layouts.app')
@php
    $companyName = $siteSettings?->company_name ?: config('app.name');
@endphp
@section('title', 'Contact | '.$companyName)
@section('meta_description', 'Contact '.$companyName.' for construction and engineering inquiries.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Contact Us', 'description' => 'Send project inquiries, requests for quotation, or general messages to our team.'])
@php
    $googleMapEmbedUrl = $siteSettings?->trustedGoogleMapEmbedUrl();
    $showContactMap = (bool) $siteSettings?->show_contact_map;
    $mapLocationName = $siteSettings?->map_location_name ?: $siteSettings?->company_name;
    $mapAddress = $siteSettings?->map_address ?: $siteSettings?->address;
    $directionsUrl = $siteSettings?->googleMapsDirectionsUrl();
@endphp
<section class="container-xl section-spacing">
    <div class="row g-4 g-lg-5 align-items-stretch">
        <div class="col-lg-5">
            <div class="contact-panel rounded-4 p-4 h-100">
                <h2 class="h4 mb-4">Company Information</h2>
                @if($siteSettings?->address)<p><strong>Address:</strong><br>{{ $siteSettings->address }}</p>@endif
                @if($siteSettings?->phone)<p><strong>Phone:</strong><br><a href="tel:{{ $siteSettings->phone }}">{{ $siteSettings->phone }}</a></p>@endif
                @if($siteSettings?->email)<p><strong>Email:</strong><br><a href="mailto:{{ $siteSettings->email }}">{{ $siteSettings->email }}</a></p>@endif
                @if($socialLinks->isNotEmpty())
                    <div class="d-flex gap-3 flex-wrap mt-4">
                        @foreach($socialLinks as $social)
                            @continue(blank($social->url))
                            <a href="{{ $social->url }}" target="_blank" rel="noopener" class="social-pill" aria-label="{{ $social->platform }}">{{ $social->platform }}</a>
                        @endforeach
                    </div>
                @endif

                <div class="contact-map-section mt-4">
                    @if($showContactMap && $googleMapEmbedUrl)
                        <div class="contact-map">
                            <iframe src="{{ $googleMapEmbedUrl }}" title="{{ $mapLocationName ?: 'Company location on Google Maps' }}" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    @else
                        <div class="contact-map-fallback">
                            <strong>{{ $mapLocationName ?: 'Our Location' }}</strong>
                            @if($mapAddress)<span>{{ $mapAddress }}</span>@endif
                        </div>
                    @endif
                    @if($directionsUrl)
                        <a class="btn btn-sm btn-outline-primary-brand mt-3" href="{{ $directionsUrl }}" target="_blank" rel="noopener noreferrer">Get Directions</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="contact-panel rounded-4 p-4 p-lg-5 h-100">
                @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
                <h2 class="h4 mb-4">Send a Message</h2>
                @include('frontend.partials.contact-form')
            </div>
        </div>
    </div>
</section>
@endsection
