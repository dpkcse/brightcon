@extends('admin.layouts.app')
@section('title','Edit Homepage Section')
@section('page-heading','Edit '.$label)
@section('content')
<form method="POST" action="{{ route('admin.homepage-sections.update', $item) }}" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3">@csrf @method('PUT')
<div class="col-md-6"><label class="form-label">Section Key</label><input class="form-control" value="{{ $item->section_key }}" disabled></div>
@foreach(['title'=>'Title','subtitle'=>'Subtitle','button_text'=>'Button Text','button_link'=>'Button Link','background_color'=>'Background Color','sort_order'=>'Sort Order'] as $field=>$labelText)<div class="col-md-6"><label class="form-label">{{ $labelText }}</label><input class="form-control @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field, $item->$field) }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endforeach
<div class="col-12"><label class="form-label">Content</label><textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="6">{{ old('content',$item->content) }}</textarea>@error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@foreach(['image'=>'Image','background_image'=>'Background Image'] as $field=>$labelText)<div class="col-md-6"><label class="form-label">{{ $labelText }}</label><input type="file" class="form-control @error($field) is-invalid @enderror" name="{{ $field }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror @if($item->$field)<img src="{{ Storage::url($item->$field) }}" class="img-thumbnail mt-2" style="max-height:120px" alt="Current {{ $labelText }}">@endif</div>@endforeach
<div class="col-md-3"><label class="form-label">Status</label><select class="form-select" name="status"><option value="1" @selected(old('status',$item->status ?? true))>Active</option><option value="0" @selected(! old('status',$item->status ?? true))>Inactive</option></select></div>
</div><div class="card-footer bg-white d-flex justify-content-between"><a class="btn btn-outline-secondary" href="{{ route('admin.homepage-sections.index') }}">Back</a><button class="btn btn-primary-brand">Save</button></div></form>
@endsection
