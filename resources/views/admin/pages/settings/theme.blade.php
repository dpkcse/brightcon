@extends('admin.layouts.app')
@section('title','Theme Settings')
@section('page-heading','Theme Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.theme.update') }}" class="card border-0 shadow-sm"><div class="card-body row g-3">@csrf @method('PUT')
@foreach(['primary_color','secondary_color','footer_background_color','body_text_color','heading_text_color'] as $field)
<div class="col-md-4"><label class="form-label">{{ Str::headline($field) }}</label><input type="color" class="form-control form-control-color d-inline-block me-2" value="{{ old($field,$setting->$field ?: '#000000') }}" oninput="this.nextElementSibling.value=this.value"><input class="form-control d-inline-block w-auto @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field,$setting->$field) }}">@error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
@endforeach
@foreach(['body_font_family','heading_font_family','base_font_size','h1_font_size','h2_font_size','h3_font_size','button_radius','section_spacing'] as $field)
<div class="col-md-3"><label class="form-label">{{ Str::headline($field) }}</label><input class="form-control" name="{{ $field }}" value="{{ old($field,$setting->$field) }}"></div>
@endforeach
<div class="col-12"><label class="form-label">Custom CSS</label><textarea class="form-control" name="custom_css" rows="8">{{ old('custom_css',$setting->custom_css) }}</textarea><div class="form-help">CSS only. Script tags are stripped in the frontend partial.</div></div>
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary-brand">Save Theme</button></div></form>
@endsection
