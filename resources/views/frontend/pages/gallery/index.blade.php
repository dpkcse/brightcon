@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('app.name'))
@section('title', 'Gallery | '.$companyName)
@section('meta_description', 'Browse construction site, project, and equipment gallery images.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Gallery', 'description' => 'Project snapshots, site progress, equipment, and completed work highlights.'])
<section class="container section-spacing">
    <div class="filter-pills mb-5"><a class="btn {{ $activeCategory ? 'btn-outline-secondary' : 'btn-primary-brand' }}" href="{{ route('gallery.index') }}">All Images</a>@foreach($categories as $category)<a class="btn {{ $activeCategory === $category ? 'btn-primary-brand' : 'btn-outline-secondary' }}" href="{{ route('gallery.index', ['category' => $category]) }}">{{ $category }}</a>@endforeach</div>
    @if($images->count())<div class="gallery-grid">@foreach($images as $image)@php($imageUrl = $image->image && Illuminate\Support\Facades\Storage::disk('public')->exists($image->image) ? Illuminate\Support\Facades\Storage::url($image->image) : null)<figure class="gallery-card">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $image->title ?: $image->category ?: 'Gallery image' }}" loading="lazy">@else<div class="image-placeholder">Gallery</div>@endif<figcaption><strong>{{ $image->title ?: 'Project Image' }}</strong><span>{{ $image->category ?: 'Gallery' }}</span></figcaption></figure>@endforeach</div><div class="mt-5">{{ $images->links() }}</div>@else @include('frontend.partials.content.empty-state', ['title' => 'No gallery images found', 'message' => 'Active gallery images added from the CMS will appear here.']) @endif
</section>
@endsection
