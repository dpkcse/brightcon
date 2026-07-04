@extends('frontend.layouts.app')

@section('title', 'Page Not Found | '.($siteSettings?->company_name ?: config('app.name')))
@section('meta_description', 'The page you requested could not be found.')
@section('robots', 'noindex, follow')

@section('content')
<section class="container section-spacing text-center">
    <div class="mx-auto" style="max-width: 680px;">
        <span class="section-kicker">404 Error</span>
        <h1 class="display-5 fw-bold mb-3">Page Not Found</h1>
        <p class="lead text-muted mb-4">The page may have moved, been removed, or the address may be incorrect.</p>
        <a href="{{ route('home') }}" class="btn btn-primary-brand btn-lg">Back to Home</a>
    </div>
</section>
@endsection
