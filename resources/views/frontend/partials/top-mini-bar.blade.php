<div class="frontend-topbar py-2">
    <div class="container-xl d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div class="topbar-tagline text-center text-md-start">{{ $siteSettings?->tagline ?: 'Engineering excellence built on trust.' }}</div>
        @if($socialLinks->whereNotNull('url')->isNotEmpty())
            <div class="topbar-social d-flex flex-wrap justify-content-center gap-3">
                @foreach($socialLinks as $social)
                    @continue(blank($social->url))
                    <a href="{{ $social->url }}" target="_blank" rel="noopener" aria-label="{{ $social->platform }}">
                        @if($social->icon_class)<i class="{{ $social->icon_class }}"></i>@else{{ $social->platform }}@endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
