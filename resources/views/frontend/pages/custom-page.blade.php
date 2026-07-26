@extends('frontend.layouts.app')
@section('title', ($page->seo_title ?: $page->title).' | '.($siteSettings?->company_name ?: config('cms.product.name')))
@section('meta_description', $page->seo_description ?: $page->excerpt)
@section('meta_keywords', $page->seo_keywords)
@section('canonical', route('pages.show', $page))
@section('content')
@include('frontend.partials.page-header',['title'=>$page->title,'description'=>$page->excerpt])
<article class="container-xl section-spacing"><div class="row justify-content-center"><div class="col-lg-9">@if($page->featured_image_path)<img class="img-fluid rounded-4 mb-4" src="{{ \App\Support\FrontendImage::url($page->featured_image_path) }}" alt="{{ $page->featured_image_alt ?: $page->title }}">@endif<div class="cms-rich-content">{!! $page->content !!}</div></div></div></article>
@endsection
