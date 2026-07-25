@php use App\Support\FrontendImage; @endphp
@if($organizations->isNotEmpty())
<section class="home-organizations section-spacing" aria-labelledby="organizations-title">
    <div class="container-xl">
        <div class="section-heading organization-heading text-center mx-auto">
            <span class="section-kicker">Selected Project Experience</span>
            <h2 id="organizations-title">Organizations represented in our experience</h2>
        </div>
        <div class="organization-grid" style="--organization-columns: {{ min($organizations->count(), 5) }}">
            @foreach($organizations as $organization)
                @php($logoUrl = FrontendImage::url($organization->logo))
                <div class="organization-logo-card">
                    @if($organization->website_url)<a href="{{ $organization->website_url }}" target="_blank" rel="noopener noreferrer" aria-label="Visit {{ $organization->name }} website">@endif
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $organization->name }} logo" loading="lazy" decoding="async" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                        <span class="organization-name-fallback" hidden>{{ $organization->name }}</span>
                    @else
                        <span>{{ $organization->name }}</span>
                    @endif
                    @if($organization->website_url)</a>@endif
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
