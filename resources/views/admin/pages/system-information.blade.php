@extends('admin.layouts.app')
@section('title','System Information') @section('page-heading','System Information')
@section('content')<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Read-only safe diagnostics</strong></div><div class="card-body"><dl class="row mb-0">@foreach($information as $label=>$value)<dt class="col-md-4">{{ $label }}</dt><dd class="col-md-8">{{ $value }}</dd>@endforeach</dl></div></div>@endsection
