@php
    $partnerMessages = $partnerMessages ?? collect();
@endphp

@if($partnerMessages->isNotEmpty())
<section id="partner-messages" class="partner-messages-section section-spacing" aria-labelledby="partner-messages-title">
    <div class="container-xl">
        <header class="section-heading text-center mx-auto mb-5">
            <span class="section-kicker">Leadership Voices</span>
            <h2 id="partner-messages-title">Messages from Our Partners</h2>
            <p>Leadership insights, shared values, and the vision behind our journey.</p>
        </header>

        <div class="partner-message-stage" data-partner-carousel tabindex="0" aria-label="Partner messages carousel">
            @foreach($partnerMessages as $message)
                @php($initials = collect(preg_split('/\s+/', trim($message->name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode(''))
                <article id="partner-message-{{ $message->id }}" class="partner-message-card {{ $loop->first ? 'is-active' : '' }}" data-partner-slide data-slide-index="{{ $loop->index }}" aria-hidden="{{ $loop->first ? 'false' : 'true' }}" @if(! $loop->first) hidden @endif>
                    <div class="partner-message-media">
                        <div class="partner-message-accent" aria-hidden="true"></div>
                        @if($message->image_path)
                            <img src="{{ \App\Support\FrontendImage::url($message->image_path) }}" alt="Portrait of {{ $message->name }}" {{ $loop->first ? 'fetchpriority=high' : 'loading=lazy' }} decoding="async">
                        @else
                            <div class="partner-message-placeholder" role="img" aria-label="Portrait placeholder for {{ $message->name }}">{{ $initials ?: '?' }}</div>
                        @endif
                    </div>
                    <div class="partner-message-content">
                        <span class="partner-message-quote" aria-hidden="true">“</span>
                        @if($message->highlighted_text)<p class="partner-message-highlight">{{ $message->highlighted_text }}</p>@endif
                        <p class="partner-message-copy">{{ $message->full_message }}</p>
                        <footer class="partner-message-person">
                            <div>
                                @if($message->organization_logo_path)<img src="{{ \App\Support\FrontendImage::url($message->organization_logo_path) }}" alt="{{ $message->organization ? $message->organization.' logo' : 'Organization logo' }}" class="partner-message-logo" loading="lazy" decoding="async">@endif
                                <h3>{{ $message->name }}</h3>
                                <p>{{ $message->designation }}@if($message->organization) <span aria-hidden="true">·</span> {{ $message->organization }}@endif</p>
                            </div>
                            @if($message->linkedin_url)<a class="partner-message-linkedin" href="{{ $message->linkedin_url }}" target="_blank" rel="noopener noreferrer" aria-label="View {{ $message->name }} on LinkedIn"><i class="fa-brands fa-linkedin-in" aria-hidden="true"></i></a>@endif
                        </footer>
                    </div>
                </article>
            @endforeach

            @if($partnerMessages->count() > 1)
                <div class="partner-message-navigation" aria-label="Partner message navigation">
                    <button type="button" class="partner-message-control" data-partner-prev aria-label="Previous partner message"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></button>
                    <span class="partner-message-indicator" data-partner-indicator aria-live="polite">01 / {{ str_pad((string) $partnerMessages->count(), 2, '0', STR_PAD_LEFT) }}</span>
                    <button type="button" class="partner-message-control" data-partner-next aria-label="Next partner message"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
                </div>
                <div class="partner-message-selectors" role="list" aria-label="Select a partner message">
                    @foreach($partnerMessages as $message)
                        <button type="button" class="partner-message-selector {{ $loop->first ? 'is-active' : '' }}" data-partner-tab data-slide-index="{{ $loop->index }}" aria-controls="partner-message-{{ $message->id }}" @if($loop->first) aria-current="true" @endif>
                            @if($message->image_path)<img src="{{ \App\Support\FrontendImage::url($message->image_path) }}" alt="" loading="lazy" decoding="async">@else<span class="partner-message-selector-placeholder" aria-hidden="true">{{ collect(preg_split('/\s+/', trim($message->name)))->filter()->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') ?: '?' }}</span>@endif
                            <span><strong>{{ $message->name }}</strong><small>{{ $message->designation ?: $message->organization }}</small></span>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</section>
@endif
