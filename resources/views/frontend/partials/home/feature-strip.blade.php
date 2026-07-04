@php use App\Support\FrontendImage; @endphp
@if($featureItems->isNotEmpty())
<section class="feature-strip-wrap">
    <div class="container">
        <div class="feature-strip row g-0">
            @foreach($featureItems->take(3) as $feature)
                <article class="col-md-4 feature-card">
                    <div class="feature-icon">
                        @if($feature->icon_class)<i class="{{ $feature->icon_class }}"></i>
                        @elseif($feature->image)<img src="{{ FrontendImage::url($feature->image) }}" alt="{{ $feature->title ?: 'Feature icon' }}" loading="lazy" decoding="async">
                        @else<span>✓</span>@endif
                    </div>
                    <div><h2 class="h5">{{ $feature->title ?: 'Reliable Delivery' }}</h2>@if($feature->short_text)<p>{{ $feature->short_text }}</p>@endif</div>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endif
