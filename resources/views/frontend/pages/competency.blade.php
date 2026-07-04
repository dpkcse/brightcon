@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('app.name'))
@section('title', 'Competency | '.$companyName)
@section('meta_description', 'Explore construction and engineering competencies backed by active services and project experience.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Competency', 'description' => $servicesSection?->content ?: 'Core capabilities for complex construction, infrastructure, and engineering delivery.'])
<section class="container-xl section-spacing"><div class="row g-4">@foreach([['Civil Construction','Earthwork, foundations, concrete works, and building delivery.'],['Structural Engineering','Structural execution support for durable commercial and infrastructure assets.'],['Power & Energy Works','Power distribution and energy infrastructure construction support.'],['Road & Highway Works','Road formation, paving support, drainage, and transportation infrastructure.'],['Project Management','Planning, coordination, reporting, and milestone-driven delivery.'],['Quality & Safety Control','Inspection, safety awareness, and quality-focused construction practices.']] as [$title,$copy])<div class="col-md-6 col-xl-4"><div class="competency-card h-100"><h2 class="h5">{{ $title }}</h2><p>{{ $copy }}</p></div></div>@endforeach</div></section>
@endsection
