@php
    use App\Support\FrontendImage;

    $companyName = $siteSettings?->company_name ?: config('app.name');
    $defaultTitle = trim($companyName.' | '.($siteSettings?->tagline ?: 'Construction & Engineering'));
    $title = trim($__env->yieldContent('title', $seo['title'] ?? $defaultTitle));
    $description = trim($__env->yieldContent('meta_description', $seo['description'] ?? ($siteSettings?->tagline ?: 'Construction and engineering services delivered with safety, quality, and professionalism.')));
    $canonical = $__env->yieldContent('canonical', $seo['canonical'] ?? url()->current());
    $ogTitle = trim($__env->yieldContent('og_title', $seo['og_title'] ?? $title));
    $ogDescription = trim($__env->yieldContent('og_description', $seo['og_description'] ?? $description));
    $robots = trim($__env->yieldContent('robots', $seo['robots'] ?? 'index, follow'));
    $fallbackImage = FrontendImage::url($siteSettings?->logo) ?: FrontendImage::url($siteSettings?->favicon);
    $ogImage = $__env->yieldContent('og_image', $seo['og_image'] ?? $fallbackImage);
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<link rel="canonical" href="{{ $canonical }}">
<meta name="robots" content="{{ $robots }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $companyName }}">
<meta property="og:title" content="{{ $ogTitle }}">
<meta property="og:description" content="{{ $ogDescription }}">
<meta property="og:url" content="{{ $canonical }}">
@if(filled($ogImage))
<meta property="og:image" content="{{ url($ogImage) }}">
@endif
<meta name="twitter:card" content="{{ filled($ogImage) ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
@if(filled($ogImage))
<meta name="twitter:image" content="{{ url($ogImage) }}">
@endif
