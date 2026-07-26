@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('cms.defaults.company_name') ?: config('cms.product.name'))
@section('title', 'Competency | '.$companyName)
@section('meta_description', 'Explore construction and engineering competencies backed by active services and project experience.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Competency', 'description' => $servicesSection?->content ?: 'Core capabilities for complex construction, infrastructure, and engineering delivery.'])
<section class="container-xl section-spacing"><div class="row g-4">@forelse($competencies as $item)<div class="col-md-6 col-xl-4"><article class="competency-card h-100">@if($item->image_path)<img class="img-fluid rounded mb-3" src="{{ \App\Support\FrontendImage::url($item->image_path) }}" alt="{{ $item->image_alt ?: $item->title }}">@endif<h2 class="h5">{{ $item->title }}</h2><p>{{ $item->short_description }}</p></article></div>@empty @foreach([['Civil Construction','Earthwork, foundations, concrete works, and building delivery.'],['Structural Engineering','Structural execution support for durable commercial and infrastructure assets.'],['Project Management','Planning, coordination, reporting, and milestone-driven delivery.']] as [$title,$copy])<div class="col-md-6 col-xl-4"><div class="competency-card h-100"><h2 class="h5">{{ $title }}</h2><p>{{ $copy }}</p></div></div>@endforeach @endforelse</div></section>
@endsection
