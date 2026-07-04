@php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($service->image);
@endphp
<article class="content-card service-card h-100">
    <div class="content-card-media content-card-media-service">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $service->title }}" loading="lazy" decoding="async">
        @else
            <div class="image-placeholder image-placeholder-service"><span>Service</span></div>
        @endif
        <span class="service-icon">@if($service->icon_class)<i class="{{ $service->icon_class }}"></i>@else⚙@endif</span>
    </div>
    <div class="content-card-body service-card-body">
        <h3 class="content-card-title"><a href="{{ route('services.show', $service) }}" class="stretched-link text-decoration-none text-reset">{{ $service->title }}</a></h3>
        <p class="content-card-description">{{ $service->short_description ?: 'Professional construction and engineering service delivered by experienced specialists.' }}</p>
        <span class="content-card-action">Explore Service →</span>
    </div>
</article>
