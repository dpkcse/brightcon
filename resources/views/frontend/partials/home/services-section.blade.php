@php use App\Support\FrontendImage; @endphp
<section class="home-services section-spacing">
    <div class="container-xl">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker">{{ $section?->title ?: 'What We Do' }}</span>
            <h2>{{ $section?->subtitle ?: 'Construction services built around your goals.' }}</h2>
            @if($section?->content)<p>{{ $section->content }}</p>@endif
        </div>
        @if($services->isNotEmpty())
            <div class="row g-4 mt-2">
                @foreach($services as $service)
                    <div class="col-md-6 col-xl-3">
                        <article class="service-card h-100">
                            <div class="card-media">
                                @if($service->image)<img src="{{ FrontendImage::url($service->image) }}" alt="{{ $service->title }}" loading="lazy" decoding="async">@else<div class="image-placeholder"><span>Service</span></div>@endif
                                <div class="service-icon">@if($service->icon_class)<i class="{{ $service->icon_class }}"></i>@else<span>▰</span>@endif</div>
                            </div>
                            <div class="card-body"><h3 class="h5">{{ $service->title }}</h3>@if($service->short_description)<p>{{ $service->short_description }}</p>@endif <a href="{{ url('/services/'.$service->slug) }}" class="stretched-link card-link">Explore Service</a></div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-4">No active services available.</div>
        @endif
    </div>
</section>
