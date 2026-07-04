<form method="POST" action="{{ route('contact.store') }}" class="{{ $formClass ?? 'contact-form' }}" novalidate>
    @csrf
    <label class="visually-hidden" for="{{ $formId ?? 'contact' }}-website">Website</label>
    <input id="{{ $formId ?? 'contact' }}-website" type="text" name="website" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="visually-hidden" for="{{ $formId ?? 'contact' }}-full-name">Full Name</label>
            <input id="{{ $formId ?? 'contact' }}-full-name" class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" placeholder="Full Name *" autocomplete="name" required aria-describedby="@error('full_name') {{ ($formId ?? 'contact').'-full-name-error' }} @enderror">
            @error('full_name')<div id="{{ ($formId ?? 'contact').'-full-name-error' }}" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="visually-hidden" for="{{ $formId ?? 'contact' }}-email">Email Address</label>
            <input id="{{ $formId ?? 'contact' }}-email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email Address *" autocomplete="email" required aria-describedby="@error('email') {{ ($formId ?? 'contact').'-email-error' }} @enderror">
            @error('email')<div id="{{ ($formId ?? 'contact').'-email-error' }}" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="visually-hidden" for="{{ $formId ?? 'contact' }}-phone">Phone</label>
            <input id="{{ $formId ?? 'contact' }}-phone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Phone" autocomplete="tel" aria-describedby="@error('phone') {{ ($formId ?? 'contact').'-phone-error' }} @enderror">
            @error('phone')<div id="{{ ($formId ?? 'contact').'-phone-error' }}" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6">
            <label class="visually-hidden" for="{{ $formId ?? 'contact' }}-subject">Subject</label>
            <input id="{{ $formId ?? 'contact' }}-subject" class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" placeholder="Subject" aria-describedby="@error('subject') {{ ($formId ?? 'contact').'-subject-error' }} @enderror">
            @error('subject')<div id="{{ ($formId ?? 'contact').'-subject-error' }}" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12">
            <label class="visually-hidden" for="{{ $formId ?? 'contact' }}-message">Message</label>
            <textarea id="{{ $formId ?? 'contact' }}-message" class="form-control @error('message') is-invalid @enderror" name="message" rows="{{ $rows ?? 5 }}" placeholder="Message *" required aria-describedby="@error('message') {{ ($formId ?? 'contact').'-message-error' }} @enderror">{{ old('message') }}</textarea>
            @error('message')<div id="{{ ($formId ?? 'contact').'-message-error' }}" class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12"><button class="btn btn-primary-brand" type="submit">Send Message</button></div>
    </div>
</form>
