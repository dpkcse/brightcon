<form method="POST" action="{{ route('contact.store') }}" class="{{ $formClass ?? 'contact-form' }}" novalidate>
    @csrf
    <input type="text" name="website" class="d-none" tabindex="-1" autocomplete="off" aria-hidden="true">
    <div class="row g-3">
        <div class="col-md-6"><input class="form-control @error('full_name') is-invalid @enderror" name="full_name" value="{{ old('full_name') }}" placeholder="Full Name *" required>@error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="Email Address *" required>@error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><input class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" placeholder="Phone">@error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><input class="form-control @error('subject') is-invalid @enderror" name="subject" value="{{ old('subject') }}" placeholder="Subject">@error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><textarea class="form-control @error('message') is-invalid @enderror" name="message" rows="{{ $rows ?? 5 }}" placeholder="Message *" required>{{ old('message') }}</textarea>@error('message')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-12"><button class="btn btn-primary-brand" type="submit">Send Message</button></div>
    </div>
</form>
