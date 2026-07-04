@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('app.name'))
@section('title', 'Projects | '.$companyName)
@section('meta_description', 'Browse active construction and engineering projects by '.$companyName.'.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Projects', 'description' => 'A portfolio of infrastructure, building, power, and development works.'])
<section class="container-xl section-spacing">
    <div class="filter-pills mb-5"><a class="btn {{ $activeCategory ? 'btn-outline-secondary' : 'btn-primary-brand' }}" href="{{ route('projects.index') }}">All Projects</a>@foreach($categories as $category)<a class="btn {{ $activeCategory?->is($category) ? 'btn-primary-brand' : 'btn-outline-secondary' }}" href="{{ route('projects.index', ['category' => $category->slug]) }}">{{ $category->name }}</a>@endforeach</div>
    @if($projects->count())<div class="row g-4">@foreach($projects as $project)<div class="col-md-6 col-xl-4 d-flex">@include('frontend.partials.content.project-card', ['project' => $project])</div>@endforeach</div><div class="mt-5">{{ $projects->links() }}</div>@else @include('frontend.partials.content.empty-state', ['title' => 'No projects found', 'message' => 'Active projects added from the CMS will appear here.']) @endif
</section>
@endsection
