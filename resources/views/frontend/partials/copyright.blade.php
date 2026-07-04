@php
    $copyright = $siteSettings?->copyright_text ?: '© '.date('Y').' '.($siteSettings?->company_name ?: config('app.name')).'. All Rights Reserved.';
    $developerLink = $siteSettings?->developer_link;
    $validDeveloperLink = $developerLink && filter_var($developerLink, FILTER_VALIDATE_URL);
@endphp
<div class="copyright-bar py-3">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center text-md-start">
        <span>{{ str_replace('{year}', date('Y'), $copyright) }}</span>
        @if($siteSettings?->developer_name)
            <span>Site Developed By @if($validDeveloperLink)<a href="{{ $developerLink }}" target="_blank" rel="noopener">{{ $siteSettings->developer_name }}</a>@else{{ $siteSettings->developer_name }}@endif.</span>
        @endif
    </div>
</div>
