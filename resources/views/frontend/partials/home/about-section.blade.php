@php use Illuminate\Support\Facades\Storage; @endphp
<section class="home-about section-spacing">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="section-kicker">{{ $section?->title ?: 'Who We Are' }}</span>
                <h2>{{ $section?->subtitle ?: 'Experienced construction and engineering professionals.' }}</h2>
                <div class="section-copy">{!! nl2br(e($section?->content ?: 'We combine practical field expertise, disciplined project management, and a quality-first mindset to deliver dependable construction outcomes.')) !!}</div>
                @if($section?->button_text && $section?->button_link)<a href="{{ url($section->button_link) }}" class="btn btn-primary-brand mt-4">{{ $section->button_text }}</a>@endif
            </div>
            <div class="col-lg-6">
                @if($section?->image)<img class="about-image" src="{{ Storage::url($section->image) }}" alt="{{ $section->subtitle ?: $section->title ?: 'About our company' }}" loading="lazy">
                @else<div class="image-placeholder about-placeholder"><span>Construction Excellence</span></div>@endif
            </div>
        </div>
    </div>
</section>
