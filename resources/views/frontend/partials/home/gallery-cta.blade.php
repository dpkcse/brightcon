@php
    use App\Support\FrontendImage;
    $backgroundImage = $section?->background_image ? FrontendImage::url($section->background_image) : null;
    $backgroundColor = $section?->background_color ?: 'var(--primary-color)';
@endphp
<section class="gallery-cta" style="--cta-bg: {{ $backgroundColor }}; @if($backgroundImage) background-image: linear-gradient(rgba(8,11,16,.70), rgba(8,11,16,.70)), url('{{ $backgroundImage }}'); @endif">
    <div class="container-xl text-center">
        <span class="section-kicker light">{{ $section?->title ?: 'Our Gallery' }}</span>
        <h2>{{ $section?->subtitle ?: 'In our work we have pride, quality is what we provide.' }}</h2>
        @if($section?->content)<p>{{ $section->content }}</p>@endif
        @if($section?->button_text && $section?->button_link)<a href="{{ url($section->button_link) }}" class="btn btn-light btn-lg">{{ $section->button_text }}</a>@endif
    </div>
</section>
