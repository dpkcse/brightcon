@php use App\Support\FrontendImage; @endphp
<section class="home-projects section-spacing bg-soft">
    <div class="container-xl">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker">{{ $section?->title ?: 'Project Highlights' }}</span>
            <h2>{{ $section?->subtitle ?: 'Featured work delivered with precision.' }}</h2>
            @if($section?->content)<p>{{ $section->content }}</p>@endif
        </div>
        @if($projects->isNotEmpty())
            <div class="row g-4 mt-3 align-items-stretch">
                @foreach($projects as $project)
                    <div class="col-md-6 col-lg-4 d-flex">
                        @include('frontend.partials.content.project-card', ['project' => $project])
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-4">No featured projects available.</div>
        @endif
    </div>
</section>
