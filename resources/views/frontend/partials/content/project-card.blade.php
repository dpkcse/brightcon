@php
    use App\Support\FrontendImage;
    $imageUrl = FrontendImage::url($project->featured_image);
@endphp
<article class="work-card h-100">
    <div class="card-media">
        @if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $project->title }}" loading="lazy" decoding="async">@else<div class="image-placeholder">Project</div>@endif
        <span class="progress-badge">{{ $project->progress_percentage ?? 0 }}%</span>
    </div>
    <div class="card-body">
        <span class="card-meta">{{ $project->category?->name ?: 'Project' }}</span>
        <h3 class="h5"><a href="{{ route('projects.show', $project) }}" class="stretched-link text-decoration-none text-reset">{{ $project->title }}</a></h3>
        <p>{{ $project->short_description ?: 'Construction project delivered with careful planning, quality control, and safe execution.' }}</p>
        <span class="card-link">Read More →</span>
    </div>
</article>
