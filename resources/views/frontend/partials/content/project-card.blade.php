@php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($project->featured_image);
@endphp
<article class="content-card project-card h-100">
    <div class="content-card-media content-card-media-project">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $project->title }}" loading="lazy" decoding="async">
        @else
            <div class="image-placeholder image-placeholder-project"><span>Project</span></div>
        @endif
        <span class="progress-badge">{{ $project->progress_percentage ?? 0 }}%</span>
    </div>
    <div class="content-card-body">
        <span class="content-card-meta">{{ $project->category?->name ?: 'Project' }}</span>
        <h3 class="content-card-title"><a href="{{ route('projects.show', $project) }}" class="stretched-link text-decoration-none text-reset">{{ $project->title }}</a></h3>
        <p class="content-card-description">{{ $project->short_description ?: 'Construction project delivered with careful planning, quality control, and safe execution.' }}</p>
        <span class="content-card-action">Read More →</span>
    </div>
</article>
