@extends('frontend.layouts.app')

@php
    $companyName = $siteSettings?->company_name ?: config('app.name');
    $homeTitle = 'Home | '.$companyName;
    $homeDescription = $siteSettings?->tagline ?: 'Construction and engineering services delivered with safety, quality, and professionalism.';
@endphp

@section('title', $homeTitle)
@section('meta_description', $homeDescription)

@section('content')
    @include('frontend.partials.home.hero-slider', ['sliders' => $sliders])
    @include('frontend.partials.home.feature-strip', ['featureItems' => $featureItems])
    @include('frontend.partials.home.about-section', ['section' => $aboutSection])
    @include('frontend.partials.home.project-highlights', ['section' => $projectHighlightsSection, 'projects' => $featuredProjects])
    @include('frontend.partials.home.gallery-cta', ['section' => $galleryCtaSection])
    @include('frontend.partials.home.services-section', ['section' => $servicesSection, 'services' => $services])
@endsection
