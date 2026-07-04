@extends('frontend.layouts.app')
@section('title', 'About | BrightCon')
@section('content')
    @include('frontend.partials.page-header', ['title' => 'About Page Placeholder', 'description' => 'Company profile content will be connected in a later phase; this page now uses the dynamic frontend layout.'])
    <section class="container section-spacing">
        <div class="section-placeholder rounded-3 p-4 p-lg-5">
            <h2 class="h4">CMS Section Placeholder</h2>
            <p class="mb-0 text-muted">Reserved for future editable content, media, calls to action, and construction company information.</p>
        </div>
    </section>
@endsection
