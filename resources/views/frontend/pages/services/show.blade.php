@extends('frontend.layouts.app')
@php
    use Illuminate\Support\Facades\Storage;
    $companyName = $siteSettings?->company_name ?: config('app.name');
    $imageUrl = $service->image && Storage::disk('public')->exists($service->image) ? Storage::url($service->image) : null;
@endphp
@section('title', ($service->seo_title ?: $service->title).' | '.$companyName)
@section('meta_description', $service->seo_description ?: ($service->short_description ?: 'Service details from '.$companyName))
@section('content')
@include('frontend.partials.page-header', ['title' => $service->title, 'description' => $service->short_description ?: 'Professional construction and engineering service.'])
<section class="container section-spacing">
    <div class="row g-5 align-items-start">
        <div class="col-lg-7">
            <div class="detail-media mb-4">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $service->title }}" loading="lazy">@else<div class="image-placeholder">Service</div>@endif</div>
            <div class="content-body">{!! nl2br(e($service->full_description ?: $service->short_description ?: 'Detailed service information will be updated from the CMS.')) !!}</div>
        </div>
        <aside class="col-lg-5">
            <div class="detail-summary rounded-4 p-4">
                <div class="detail-icon mb-3">@if($service->icon_class)<i class="{{ $service->icon_class }}"></i>@else⚙@endif</div>
                <h2 class="h4">Service Overview</h2>
                <p>{{ $service->short_description ?: 'A focused construction and engineering capability supported by experienced site teams.' }}</p>
            </div>
            <div class="mt-4">@include('frontend.partials.content.cta-contact')</div>
        </aside>
    </div>
</section>
@if($relatedServices->isNotEmpty())
<section class="bg-soft section-spacing"><div class="container"><div class="section-heading mb-4"><span class="section-kicker">Explore More</span><h2>Related Services</h2></div><div class="row g-4">@foreach($relatedServices as $related)<div class="col-md-6 col-xl-3">@include('frontend.partials.content.service-card', ['service' => $related])</div>@endforeach</div></div></section>
@endif
@endsection
