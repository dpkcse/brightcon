@php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($service->image);
@endphp
<article class="service-card h-100">
    <div class="card-media">
        @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $service->title }}" loading="lazy" decoding="async">@else<div class="image-placeholder">Service</div>@endif
        <span class="service-icon">@if($service->icon_class)<i class="{{ $service->icon_class }}"></i>@else⚙@endif</span>
    </div>
    <div class="card-body">
        <h3 class="h5"><a href="{{ route('services.show', $service) }}" class="stretched-link text-decoration-none text-reset">{{ $service->title }}</a></h3>
        <p>{{ $service->short_description ?: 'Professional construction and engineering service delivered by experienced specialists.' }}</p>
        <span class="card-link">Read More →</span>
    </div>
</article>
