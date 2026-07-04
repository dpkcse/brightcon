<div class="cta-contact rounded-4 p-4 p-lg-5 text-center text-lg-start d-lg-flex align-items-center justify-content-between gap-4">
    <div>
        <span class="section-kicker light">Start a Project</span>
        <h2 class="h3 mb-2">{{ $title ?? 'Need reliable construction support?' }}</h2>
        <p class="mb-0">{{ $message ?? 'Talk to our engineering team about your upcoming project requirements.' }}</p>
    </div>
    <a href="{{ route('contact.index') }}" class="btn btn-light fw-bold mt-4 mt-lg-0">Contact Us</a>
</div>
