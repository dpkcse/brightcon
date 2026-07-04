@php use App\Support\FrontendImage; @endphp
<section class="home-services section-spacing">
    <div class="container-xl">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker">{{ $section?->title ?: 'What We Do' }}</span>
            <h2>{{ $section?->subtitle ?: 'Construction services built around your goals.' }}</h2>
            @if($section?->content)<p>{{ $section->content }}</p>@endif
        </div>
        @if($services->isNotEmpty())
            <div class="row g-4 mt-3 align-items-stretch">
                @foreach($services as $service)
                    <div class="col-md-6 col-xl-3 d-flex">
                        @include('frontend.partials.content.service-card', ['service' => $service])
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-4">No active services available.</div>
        @endif
    </div>
</section>
