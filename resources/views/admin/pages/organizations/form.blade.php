@extends('admin.layouts.app')
@section('title', $item->exists ? 'Edit Organization' : 'Create Organization')
@section('page-heading', $item->exists ? 'Edit Organization' : 'Create Organization')
@section('content')
<form method="POST" enctype="multipart/form-data" action="{{ $item->exists ? route('admin.organizations.update', $item) : route('admin.organizations.store') }}" class="card border-0 shadow-sm">
    <div class="card-body row g-3">@csrf @if($item->exists) @method('PUT') @endif
        <div class="col-md-6"><label class="form-label">Name</label><input class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $item->name) }}" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Website URL (optional)</label><input type="url" class="form-control @error('website_url') is-invalid @enderror" name="website_url" value="{{ old('website_url', $item->website_url) }}">@error('website_url')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-6"><label class="form-label">Logo</label><input type="file" class="form-control @error('logo') is-invalid @enderror" name="logo" accept="image/*">@error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror @if($item->logo)<img src="{{ Storage::url($item->logo) }}" class="img-thumbnail mt-2" style="max-height:100px" alt="Current {{ $item->name }} logo">@endif</div>
        <div class="col-md-2"><label class="form-label">Display order</label><input type="number" min="0" class="form-control" name="display_order" value="{{ old('display_order', $item->display_order ?? 0) }}"></div>
        <div class="col-md-2"><label class="form-label">Active</label><select class="form-select" name="is_active"><option value="1" @selected(old('is_active', $item->is_active ?? true))>Yes</option><option value="0" @selected(! old('is_active', $item->is_active ?? true))>No</option></select></div>
        <div class="col-md-2"><label class="form-label">Homepage</label><select class="form-select" name="is_featured"><option value="1" @selected(old('is_featured', $item->is_featured ?? false))>Featured</option><option value="0" @selected(! old('is_featured', $item->is_featured ?? false))>Hidden</option></select></div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="{{ route('admin.organizations.index') }}">Back</a><button class="btn btn-primary-brand">Save</button></div>
</form>
@endsection
