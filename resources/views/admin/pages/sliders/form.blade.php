@extends('admin.layouts.app')
@section('title', $item->exists ? 'Edit Hero Slider' : 'Create Hero Slider')
@section('page-heading', $item->exists ? 'Edit Hero Slider' : 'Create Hero Slider')
@section('content')
<form method="POST" action="{{ $item->exists ? route('admin.sliders.update', $item) : route('admin.sliders.store') }}" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3">@csrf @if($item->exists) @method('PUT') @endif
@foreach(['heading'=>'Heading','sub_heading'=>'Sub Heading','button_text'=>'Button Text','button_link'=>'Button Link','sort_order'=>'Sort Order'] as $field=>$label)<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field, $item->$field) }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endforeach
<div class="col-12"><label class="form-label">Description</label><textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="4">{{ old('description',$item->description) }}</textarea>@error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label">Image</label><input type="file" class="form-control @error('image') is-invalid @enderror" name="image">@error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror @if($item->image)<img src="{{ Storage::url($item->image) }}" class="img-thumbnail mt-2" style="max-height:120px" alt="Current image">@endif</div>
<div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" @selected(old('status',$item->status ?? true))>Active</option><option value="0" @selected(! old('status',$item->status ?? true))>Inactive</option></select></div>
</div><div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="{{ route('admin.sliders.index') }}">Back</a><button class="btn btn-primary-brand">Save</button></div></form>
@endsection
