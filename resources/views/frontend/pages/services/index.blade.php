@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('app.name'))
@section('title', 'Services | '.$companyName)
@section('meta_description', $section?->content ?: 'Explore our construction and engineering services.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Our Services', 'description' => $section?->subtitle ?: 'Integrated civil, structural, infrastructure, and power-sector construction capabilities.'])
<section class="container-xl section-spacing">
    <div class="section-heading text-center mx-auto mb-5">
        <span class="section-kicker">What We Do</span>
        <h2>{{ $section?->title ?: 'Construction & Engineering Services' }}</h2>
        <p>{{ $section?->content ?: 'We provide reliable services from planning and procurement through site execution and handover.' }}</p>
    </div>
    @if($services->count())
        <div class="row g-4">@foreach($services as $service)<div class="col-md-6 col-xl-4 d-flex">@include('frontend.partials.content.service-card', ['service' => $service])</div>@endforeach</div>
        <div class="mt-5">{{ $services->links() }}</div>
    @else
        @include('frontend.partials.content.empty-state', ['title' => 'No services published yet', 'message' => 'Active services added from the CMS will appear here.'])
    @endif
</section>
@endsection
