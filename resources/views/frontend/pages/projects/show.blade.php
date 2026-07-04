@extends('frontend.layouts.app')
@php
    use App\Support\FrontendImage;
    $companyName = $siteSettings?->company_name ?: config('app.name');
    $imageUrl = FrontendImage::url($project->featured_image);
@endphp
@section('title', ($project->seo_title ?: $project->title).' | '.$companyName)
@section('meta_description', $project->seo_description ?: ($project->short_description ?: 'Project details from '.$companyName))
@if($imageUrl)@section('og_image', $imageUrl)@endif
@section('og_title', ($project->seo_title ?: $project->title).' | '.$companyName)
@section('og_description', $project->seo_description ?: ($project->short_description ?: 'Project details from '.$companyName))
@section('content')
@include('frontend.partials.page-header', ['title' => $project->title, 'description' => $project->short_description ?: 'Project details and delivery progress.'])
<section class="container-xl section-spacing"><div class="row g-5"><div class="col-lg-8"><div class="detail-media mb-4">@if($imageUrl)<img src="{{ $imageUrl }}" alt="{{ $project->title }}" loading="lazy" decoding="async">@else<div class="image-placeholder">Project</div>@endif</div><div class="content-body">{!! nl2br(e($project->full_description ?: $project->short_description ?: 'Detailed project information will be updated from the CMS.')) !!}</div></div><aside class="col-lg-4"><div class="detail-summary rounded-4 p-4"><span class="card-meta">{{ $project->category?->name ?: 'Project' }}</span><h2 class="h4">Project Snapshot</h2><div class="progress my-3" role="progressbar" aria-label="Project progress" aria-valuenow="{{ $project->progress_percentage ?? 0 }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-primary-brand" style="width: {{ $project->progress_percentage ?? 0 }}%">{{ $project->progress_percentage ?? 0 }}%</div></div><p>{{ $project->short_description ?: 'A CMS-managed project record with category, progress, and detailed description.' }}</p></div><div class="mt-4">@include('frontend.partials.content.cta-contact', ['title' => 'Have a similar project?', 'message' => 'Our team can review scope, schedule, and execution requirements.'])</div></aside></div></section>
@if($relatedProjects->isNotEmpty())<section class="bg-soft section-spacing"><div class="container-xl"><div class="section-heading mb-4"><span class="section-kicker">More Work</span><h2>Related Projects</h2></div><div class="row g-4">@foreach($relatedProjects as $related)<div class="col-md-6 col-xl-4 d-flex">@include('frontend.partials.content.project-card', ['project' => $related])</div>@endforeach</div></div></section>@endif
@endsection
