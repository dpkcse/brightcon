@extends('admin.layouts.app')
@section('title','General Settings')
@section('page-heading','General Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3">@csrf @method('PUT')
<div class="col-12"><h2 class="h5">General &amp; Branding</h2></div>
@foreach(['company_name'=>'Company Name','tagline'=>'Tagline','email'=>'Email','phone'=>'Phone','default_language'=>'Default Language','timezone'=>'Timezone','copyright_text'=>'Copyright Text','developer_name'=>'Developer Name','developer_link'=>'Developer Link'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field, $setting->$field) }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
@foreach(['product_name'=>'Product Name','product_version'=>'Product Version','company_short_name'=>'Company Short Name','date_format'=>'Date Format','powered_by_text'=>'Powered By Text'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field,$setting->$field) }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
<div class="col-12"><label class="form-label">Company Description</label><textarea class="form-control" name="company_description" rows="3">{{ old('company_description',$setting->company_description) }}</textarea></div>
<div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="3">{{ old('address',$setting->address) }}</textarea></div>
@foreach(['logo'=>'Logo','favicon'=>'Favicon','profile_pdf'=>'Profile PDF'] as $field=>$label)
<div class="col-md-4"><label class="form-label">{{ $label }}</label><input type="file" class="form-control @error($field) is-invalid @enderror" name="{{ $field }}">@if($setting->$field)<div class="form-help mt-1">Current: <a href="{{ asset('storage/'.$setting->$field) }}" target="_blank">View file</a></div>@endif @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
@foreach(['dark_logo_path'=>'Dark Logo','light_logo_path'=>'Light Logo','open_graph_image_path'=>'Open Graph Image','twitter_card_image_path'=>'Twitter Card Image'] as $field=>$label)
<div class="col-md-3"><label class="form-label">{{ $label }}</label><input type="file" class="form-control" name="{{ $field }}" accept="image/jpeg,image/png,image/webp">@if($setting->$field)<a class="small" href="{{ asset('storage/'.$setting->$field) }}" target="_blank">Current</a>@endif</div>
@endforeach
<div class="col-12"><hr><h2 class="h5">Contact &amp; SEO</h2></div>
@foreach(['secondary_email'=>'Secondary Email','contact_recipient_email'=>'Contact Recipient Email','secondary_phone'=>'Secondary Phone','whatsapp_number'=>'WhatsApp Number','default_seo_title'=>'Default SEO Title','canonical_base_url'=>'Canonical Base URL','google_analytics_id'=>'Google Analytics ID','google_tag_manager_id'=>'Google Tag Manager ID','search_console_verification'=>'Search Console Verification'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field,$setting->$field) }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
@foreach(['default_meta_description'=>'Default Meta Description','default_meta_keywords'=>'Default Meta Keywords','business_hours'=>'Business Hours','secondary_address'=>'Secondary Address'] as $field=>$label)<div class="col-md-6"><label class="form-label">{{ $label }}</label><textarea class="form-control" name="{{ $field }}" rows="2">{{ old($field,$setting->$field) }}</textarea></div>@endforeach
<div class="col-md-6"><label class="form-label">Robots Directive</label><select class="form-select" name="robots_directive">@foreach(['index, follow','noindex, follow','index, nofollow','noindex, nofollow'] as $option)<option @selected(old('robots_directive',$setting->robots_directive ?: 'index, follow')===$option)>{{ $option }}</option>@endforeach</select></div>
@foreach(['show_powered_by'=>'Show Powered By','contact_form_enabled'=>'Contact Form Enabled','contact_phone_enabled'=>'Contact Phone Enabled','contact_subject_enabled'=>'Contact Subject Enabled','organization_schema_enabled'=>'Organization Schema Enabled'] as $field=>$label)<div class="col-md-4"><input type="hidden" name="{{ $field }}" value="0"><label><input type="checkbox" name="{{ $field }}" value="1" @checked(old($field,$setting->$field ?? config('cms.defaults.'.$field)))> {{ $label }}</label></div>@endforeach
<div class="col-12"><hr><h2 class="h5">Maintenance</h2></div><div class="col-md-4"><label class="form-label">Website Status</label><select class="form-select" name="website_status"><option value="active" @selected(old('website_status',$setting->website_status ?: 'active')==='active')>Active</option><option value="maintenance" @selected(old('website_status',$setting->website_status)==='maintenance')>Maintenance</option></select></div><div class="col-md-8"><label class="form-label">Maintenance Message</label><input class="form-control" name="maintenance_message" value="{{ old('maintenance_message',$setting->maintenance_message) }}"></div>

<div class="col-12"><hr class="my-3"><h2 class="h5 mb-1">Contact Map</h2><p class="text-muted mb-2">Configure the map shown with the company information on the Contact page.</p></div>
<div class="col-12">
    <div class="form-check form-switch">
        <input type="hidden" name="show_contact_map" value="0">
        <input class="form-check-input @error('show_contact_map') is-invalid @enderror" type="checkbox" role="switch" id="show_contact_map" name="show_contact_map" value="1" @checked(old('show_contact_map', $setting->show_contact_map))>
        <label class="form-check-label fw-semibold" for="show_contact_map">Show Map on Contact Page</label>
        @error('show_contact_map')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="col-12">
    <label class="form-label" for="google_map_embed_url">Google Maps Embed URL</label>
    <textarea class="form-control @error('google_map_embed_url') is-invalid @enderror" id="google_map_embed_url" name="google_map_embed_url" rows="3" placeholder="https://www.google.com/maps/embed?...">{{ old('google_map_embed_url', $setting->google_map_embed_url) }}</textarea>
    <div class="form-text">Google Maps → Search location → Share → Embed a map → Copy HTML or map URL. Complete iframe code is accepted; only its safe Google-hosted src URL will be saved.</div>
    @error('google_map_embed_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="col-md-6"><label class="form-label" for="map_location_name">Map Location Name</label><input class="form-control @error('map_location_name') is-invalid @enderror" id="map_location_name" name="map_location_name" value="{{ old('map_location_name', $setting->map_location_name) }}" maxlength="255">@error('map_location_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-6"><label class="form-label" for="map_address">Map Address</label><input class="form-control @error('map_address') is-invalid @enderror" id="map_address" name="map_address" value="{{ old('map_address', $setting->map_address) }}" maxlength="500">@error('map_address')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="map_latitude">Latitude <span class="text-muted fw-normal">(optional)</span></label><input class="form-control @error('map_latitude') is-invalid @enderror" type="number" step="any" min="-90" max="90" id="map_latitude" name="map_latitude" value="{{ old('map_latitude', $setting->map_latitude) }}">@error('map_latitude')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="map_longitude">Longitude <span class="text-muted fw-normal">(optional)</span></label><input class="form-control @error('map_longitude') is-invalid @enderror" type="number" step="any" min="-180" max="180" id="map_longitude" name="map_longitude" value="{{ old('map_longitude', $setting->map_longitude) }}">@error('map_longitude')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
<div class="col-md-4"><label class="form-label" for="map_zoom">Zoom Level <span class="text-muted fw-normal">(1–20)</span></label><input class="form-control @error('map_zoom') is-invalid @enderror" type="number" min="1" max="20" id="map_zoom" name="map_zoom" value="{{ old('map_zoom', $setting->map_zoom ?? 15) }}">@error('map_zoom')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary-brand">Save Settings</button></div></form>
@endsection
