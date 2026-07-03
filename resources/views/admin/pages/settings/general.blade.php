@extends('admin.layouts.app')
@section('title','General Settings')
@section('page-heading','General Settings')
@section('content')
<form method="POST" action="{{ route('admin.settings.general.update') }}" enctype="multipart/form-data" class="card border-0 shadow-sm"><div class="card-body row g-3">@csrf @method('PUT')
@foreach(['company_name'=>'Company Name','tagline'=>'Tagline','email'=>'Email','phone'=>'Phone','default_language'=>'Default Language','timezone'=>'Timezone','copyright_text'=>'Copyright Text','developer_name'=>'Developer Name','developer_link'=>'Developer Link'] as $field=>$label)
<div class="col-md-6"><label class="form-label">{{ $label }}</label><input class="form-control @error($field) is-invalid @enderror" name="{{ $field }}" value="{{ old($field, $setting->$field) }}">@error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
<div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="address" rows="3">{{ old('address',$setting->address) }}</textarea></div>
@foreach(['logo'=>'Logo','favicon'=>'Favicon','profile_pdf'=>'Profile PDF'] as $field=>$label)
<div class="col-md-4"><label class="form-label">{{ $label }}</label><input type="file" class="form-control @error($field) is-invalid @enderror" name="{{ $field }}">@if($setting->$field)<div class="form-help mt-1">Current: <a href="{{ asset('storage/'.$setting->$field) }}" target="_blank">View file</a></div>@endif @error($field)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
@endforeach
</div><div class="card-footer bg-white text-end"><button class="btn btn-primary-brand">Save Settings</button></div></form>
@endsection
