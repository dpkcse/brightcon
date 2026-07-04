@php use App\Support\FrontendImage; @endphp
@if($featureItems->isNotEmpty())
<section class="home-feature-strip feature-strip-wrap" aria-label="Company feature highlights">
    <div class="container-xl">
        <div class="home-feature-grid">
            @foreach($featureItems as $feature)
                <article class="home-feature-card">
                    <div class="home-feature-icon" aria-hidden="true">
                        @if($feature->icon_class)
                            <i class="{{ $feature->icon_class }}"></i>
                        @elseif($feature->image)
                            <img src="{{ FrontendImage::url($feature->image) }}" alt="" loading="lazy" decoding="async">
                        @else
                            <span>✓</span>
                        @endif
                    </div>
                    <div class="home-feature-content">
                        <h2 class="home-feature-title">{{ $feature->title ?: 'Reliable Delivery' }}</h2>
                        @if($feature->short_text)
                            <p class="home-feature-text">{{ $feature->short_text }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
