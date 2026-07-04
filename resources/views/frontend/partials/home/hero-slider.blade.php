@php use App\Support\FrontendImage; @endphp
<section class="home-hero" aria-label="Homepage hero slider">
    @if($sliders->isNotEmpty())
        <div id="homeHeroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($sliders as $slide)
                    @php $imageUrl = $slide->image ? FrontendImage::url($slide->image) : null; @endphp
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <div class="hero-slide" @if($imageUrl) style="background-image: linear-gradient(90deg, rgba(8,11,16,.88), rgba(8,11,16,.58)), url('{{ $imageUrl }}');" @endif>
                            <div class="container">
                                <div class="hero-content">
                                    @if($slide->sub_heading)<span class="hero-eyebrow">{{ $slide->sub_heading }}</span>@endif
                                    @if($loop->first)<h1>{{ $slide->heading ?: 'Building Better Spaces With Engineering Excellence' }}</h1>@else<h2>{{ $slide->heading ?: 'Building Better Spaces With Engineering Excellence' }}</h2>@endif
                                    @if($slide->description)<p>{{ $slide->description }}</p>@endif
                                    @if($slide->button_text && $slide->button_link)<a href="{{ url($slide->button_link) }}" class="btn btn-primary-brand btn-lg">{{ $slide->button_text }}</a>@endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($sliders->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="prev"><span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span></button>
                <button class="carousel-control-next" type="button" data-bs-target="#homeHeroCarousel" data-bs-slide="next"><span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span></button>
            @endif
        </div>
    @else
        <div class="hero-slide hero-fallback">
            <div class="container">
                <div class="hero-content">
                    <span class="hero-eyebrow">Construction & Engineering</span>
                    <h1>Building Better Spaces With Engineering Excellence</h1>
                    <p>Delivering safe, reliable, and professional construction solutions for modern businesses and communities.</p>
                    <a href="{{ url('/contact') }}" class="btn btn-primary-brand btn-lg">Start a Project</a>
                </div>
            </div>
        </div>
    @endif
</section>
