@extends('admin.layouts.app')
@section('title','Theme Settings')
@section('page-heading','Theme Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.theme.update') }}" class="card border-0 shadow-sm"><div class="card-body row g-3">@csrf @method('PUT')
@foreach(['primary_color','secondary_color','footer_background_color','body_text_color','heading_text_color','accent_color','header_background_color','header_text_color','button_background_color','button_text_color','link_color','link_hover_color'] as $field)
<div class="col-md-4"><label class="form-label">{{ Str::headline($field) }}</label><input type="color" class="form-control form-control-color d-inline-block me-2" value="{{ old($field,$setting->$field ?: '#000000') }}" oninput="this.nextElementSibling.value=this.value"><input class="form-control d-inline-block w-auto @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field,$setting->$field) }}">@error($field)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror</div>
@endforeach
@foreach(['body_font_family','heading_font_family','base_font_size','h1_font_size','h2_font_size','h3_font_size','button_radius','section_spacing','card_border_radius','input_border_radius'] as $field)
<div class="col-md-3"><label class="form-label">{{ Str::headline($field) }}</label><input class="form-control" name="{{ $field }}" value="{{ old($field,$setting->$field) }}"></div>
@endforeach
<div class="col-md-4"><label class="form-label">Header Logo Variant</label><select class="form-select" name="header_logo_variant">@foreach(['default','dark','light'] as $variant)<option value="{{ $variant }}" @selected(old('header_logo_variant',$setting->header_logo_variant ?: 'default')===$variant)>{{ ucfirst($variant) }}</option>@endforeach</select></div>
<div class="col-12"><input type="hidden" name="custom_css_enabled" value="0"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="custom_css_enabled" value="1" id="custom_css_enabled" @checked(old('custom_css_enabled',$setting->custom_css_enabled))><label class="form-check-label" for="custom_css_enabled">Enable administrator custom CSS</label></div></div>
<div class="col-12"><label class="form-label">Custom CSS</label><textarea class="form-control @error('custom_css') is-invalid @enderror" name="custom_css" rows="8" maxlength="20000">{{ old('custom_css',$setting->custom_css) }}</textarea>@error('custom_css')<div class="invalid-feedback">{{ $message }}</div>@enderror<div class="form-help">Trusted administrator configuration only. Imports, URLs, browser execution primitives, data URLs, and HTML are rejected. Existing unsafe text is preserved but cannot be enabled.</div>@if($setting->custom_css && !\App\Support\CustomCssPolicy::isSafe($setting->custom_css))<div class="alert alert-warning mt-2">Stored CSS needs manual security review and is not rendered.</div>@endif</div>
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary-brand">Save Theme</button></div></form>
@endsection
