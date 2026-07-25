@extends('frontend.layouts.app')
@php use App\Support\FrontendImage; @endphp
@php($companyName = $siteSettings?->company_name ?: config('app.name'))
@section('title', 'Gallery | '.$companyName)
@section('meta_description', 'Browse construction site, project, and equipment gallery images.')

@section('content')
<header class="gallery-hero">
    <div class="container-xl">
        <span class="section-kicker">Construction &amp; Engineering</span>
        <h1>Project Gallery</h1>
        <p>Explore selected construction works, completed projects, engineering solutions, site progress, and specialized installations.</p>
    </div>
</header>

<section id="gallery-collection" class="gallery-page section-spacing" aria-labelledby="gallery-heading">
    <div class="container-xl">
        <div class="gallery-intro">
            <span class="section-label">Selected work</span>
            <h2 id="gallery-heading">Built with precision. Documented in detail.</h2>
            <p>Browse recent field work and completed installations by category.</p>
        </div>

        <nav class="gallery-filters" aria-label="Filter gallery by category">
            <a class="gallery-filter {{ $activeCategory ? '' : 'is-active' }}" href="{{ route('gallery.index') }}#gallery-collection" @if(! $activeCategory) aria-current="page" @endif>All Images</a>
            @foreach($categories as $category)
                <a class="gallery-filter {{ $activeCategory === $category ? 'is-active' : '' }}" href="{{ route('gallery.index', ['category' => $category]) }}#gallery-collection" @if($activeCategory === $category) aria-current="page" @endif>{{ $category }}</a>
            @endforeach
        </nav>

        @if($images->isNotEmpty())
            <div class="gallery-grid" data-gallery-grid>
                @foreach($images as $image)
                    @php($imageUrl = FrontendImage::url($image->image))
                    @php($imageTitle = $image->title ?: 'Project Image')
                    @php($imageCategory = $image->category ?: 'Gallery')
                    @php($imageAlt = $image->title ?: ($image->category ? $image->category.' project image' : 'Construction project gallery image'))
                    <figure class="gallery-card">
                        @if($imageUrl)
                            <button
                                class="gallery-card-preview"
                                type="button"
                                data-gallery-item
                                data-full-image="{{ $imageUrl }}"
                                data-title="{{ $imageTitle }}"
                                data-category="{{ $imageCategory }}"
                                data-alt="{{ $imageAlt }}"
                                aria-label="View {{ $imageTitle }} in image viewer"
                            >
                                <img src="{{ $imageUrl }}" alt="{{ $imageAlt }}" loading="lazy" decoding="async" width="800" height="600" onerror="this.hidden=true;this.nextElementSibling.hidden=false;this.closest('button').disabled=true">
                                <span class="gallery-image-fallback" hidden>Image unavailable</span>
                                <span class="gallery-preview-overlay" aria-hidden="true"><i class="fa-solid fa-magnifying-glass-plus"></i><span>View Image</span></span>
                            </button>
                        @else
                            <div class="gallery-card-preview gallery-image-fallback">Image unavailable</div>
                        @endif
                        <figcaption>
                            <span>{{ $imageCategory }}</span>
                            <strong>{{ $imageTitle }}</strong>
                        </figcaption>
                    </figure>
                @endforeach
            </div>

            @if($images->hasPages())
                <div class="gallery-pagination" data-gallery-pagination>
                    {{ $images->onEachSide(1)->links('frontend.partials.pagination.bootstrap') }}
                </div>
            @endif
        @else
            <div class="gallery-empty-state">
                <span class="empty-state-icon" aria-hidden="true"><i class="fa-regular fa-images"></i></span>
                <h2>No gallery images found</h2>
                <p>There are currently no published images available in this category.</p>
                <a class="btn btn-primary-brand" href="{{ route('gallery.index') }}#gallery-collection">View All Images</a>
            </div>
        @endif
    </div>
</section>

<div class="gallery-lightbox" data-gallery-lightbox hidden aria-hidden="true">
    <div class="gallery-lightbox-backdrop" data-gallery-close></div>
    <div class="gallery-lightbox-dialog" role="dialog" aria-modal="true" aria-labelledby="gallery-lightbox-title">
        <button class="gallery-lightbox-close" type="button" data-gallery-close aria-label="Close image viewer"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        <button class="gallery-lightbox-nav gallery-lightbox-prev" type="button" data-gallery-prev aria-label="View previous image"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
        <div class="gallery-lightbox-stage">
            <img data-gallery-lightbox-image src="" alt="">
        </div>
        <button class="gallery-lightbox-nav gallery-lightbox-next" type="button" data-gallery-next aria-label="View next image"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
        <div class="gallery-lightbox-meta" aria-live="polite">
            <div><span data-gallery-lightbox-category></span><h2 id="gallery-lightbox-title" data-gallery-lightbox-title></h2></div>
            <span class="gallery-lightbox-count" data-gallery-lightbox-count></span>
        </div>
    </div>
</div>
@endsection
