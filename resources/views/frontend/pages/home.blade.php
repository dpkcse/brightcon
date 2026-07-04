@extends('frontend.layouts.app')
@section('title', 'Home | BrightCon')
@section('content')
    @include('frontend.partials.page-header', ['title' => 'Home Page Placeholder', 'description' => 'This simple placeholder confirms the CMS-powered frontend shell is active. Final homepage sections will be built in a later phase.'])
    <section class="container section-spacing">
        <div class="section-placeholder rounded-3 p-4 p-lg-5">
            <h2 class="h4">CMS Section Placeholder</h2>
            <p class="mb-0 text-muted">Reserved for future editable content, media, calls to action, and construction company information.</p>
        </div>
    </section>
@endsection
