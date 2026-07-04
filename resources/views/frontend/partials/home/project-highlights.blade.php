@php use Illuminate\Support\Facades\Storage; @endphp
<section class="home-projects section-spacing bg-soft">
    <div class="container">
        <div class="section-heading text-center mx-auto">
            <span class="section-kicker">{{ $section?->title ?: 'Project Highlights' }}</span>
            <h2>{{ $section?->subtitle ?: 'Featured work delivered with precision.' }}</h2>
            @if($section?->content)<p>{{ $section->content }}</p>@endif
        </div>
        @if($projects->isNotEmpty())
            <div class="row g-4 mt-2">
                @foreach($projects as $project)
                    <div class="col-md-6 col-lg-4">
                        <article class="work-card h-100">
                            <div class="card-media">
                                @if($project->featured_image)<img src="{{ Storage::url($project->featured_image) }}" alt="{{ $project->title }}" loading="lazy">@else<div class="image-placeholder"><span>Project</span></div>@endif
                                <span class="progress-badge">{{ (int) $project->progress_percentage }}%</span>
                            </div>
                            <div class="card-body"><h3 class="h5">{{ $project->title }}</h3>@if($project->category)<span class="card-meta">{{ $project->category->name }}</span>@endif @if($project->short_description)<p>{{ $project->short_description }}</p>@endif <a href="{{ url('/projects/'.$project->slug) }}" class="stretched-link card-link">Read More</a></div>
                        </article>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state mt-4">No featured projects available.</div>
        @endif
    </div>
</section>
