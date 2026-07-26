@php
    use App\Support\FrontendImage;

    $companyName = $siteSettings?->company_name ?: config('app.name');
    $defaultTitle = $siteSettings?->default_seo_title ?: trim($companyName.' | '.($siteSettings?->tagline ?: config('cms.defaults.tagline')));
    $title = trim($__env->yieldContent('title', $seo['title'] ?? $defaultTitle));
    $description = trim($__env->yieldContent('meta_description', $seo['description'] ?? ($siteSettings?->default_meta_description ?: ($siteSettings?->tagline ?: 'Construction and engineering services delivered with safety, quality, and professionalism.'))));
    $canonicalDefault = $siteSettings?->canonical_base_url ? rtrim($siteSettings->canonical_base_url, '/').'/'.ltrim(request()->path(), '/') : url()->current();
    $canonical = $__env->yieldContent('canonical', $seo['canonical'] ?? $canonicalDefault);
    $ogTitle = trim($__env->yieldContent('og_title', $seo['og_title'] ?? $title));
    $ogDescription = trim($__env->yieldContent('og_description', $seo['og_description'] ?? $description));
    $robots = trim($__env->yieldContent('robots', $seo['robots'] ?? ($siteSettings?->robots_directive ?: 'index, follow')));
    $fallbackImage = FrontendImage::url($siteSettings?->open_graph_image_path) ?: FrontendImage::url($siteSettings?->logo) ?: FrontendImage::url($siteSettings?->favicon);
    $ogImage = $__env->yieldContent('og_image', $seo['og_image'] ?? $fallbackImage);
    $twitterImage = FrontendImage::url($siteSettings?->twitter_card_image_path) ?: $ogImage;
@endphp
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
@if(filled($siteSettings?->default_meta_keywords))<meta name="keywords" content="{{ $siteSettings->default_meta_keywords }}">@endif
@if(filled($siteSettings?->search_console_verification))<meta name="google-site-verification" content="{{ $siteSettings->search_console_verification }}">@endif
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
@if($siteSettings?->organization_schema_enabled)
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'Organization','name'=>$companyName,'url'=>$siteSettings?->canonical_base_url ?: url('/'),'email'=>$siteSettings?->email,'telephone'=>$siteSettings?->phone], JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
@endif
@if(preg_match('/^(?:G-[A-Z0-9]{6,20}|UA-\d{4,12}-\d+)$/', $siteSettings?->google_analytics_id ?? ''))
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $siteSettings->google_analytics_id }}"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json($siteSettings->google_analytics_id));</script>
@endif
@if(preg_match('/^GTM-[A-Z0-9]{4,12}$/', $siteSettings?->google_tag_manager_id ?? ''))
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f)})(window,document,'script','dataLayer',@json($siteSettings->google_tag_manager_id));</script>
@endif
<meta name="twitter:card" content="{{ filled($twitterImage) ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $ogTitle }}">
<meta name="twitter:description" content="{{ $ogDescription }}">
@if(filled($twitterImage))
<meta name="twitter:image" content="{{ url($twitterImage) }}">
@endif
