@php
    $companyName = $siteSettings?->company_name ?: config('cms.defaults.company_name') ?: config('cms.product.name');
    $productName = $siteSettings?->product_name ?: config('cms.product.name', 'Buildora CMS');
    $copyright = $siteSettings?->copyright_text ?: '© '.date('Y').' '.$companyName.'. All Rights Reserved.';
    $developerLink = $siteSettings?->developer_link;
    $validDeveloperLink = $developerLink && filter_var($developerLink, FILTER_VALIDATE_URL);
@endphp
<div class="copyright-bar py-3">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center text-md-start">
        <span>{{ str_replace('{year}', date('Y'), $copyright) }}</span>
        @if($siteSettings?->developer_name)
            <span>Site Developed By @if($validDeveloperLink)<a href="{{ $developerLink }}" target="_blank" rel="noopener">{{ $siteSettings->developer_name }}</a>@else{{ $siteSettings->developer_name }}@endif.</span>
        @endif
        @if($siteSettings?->show_powered_by ?? config('cms.defaults.show_powered_by'))
            <span>{{ $siteSettings?->powered_by_text ?: 'Powered by '.$productName }}</span>
        @endif
    </div>
</div>
