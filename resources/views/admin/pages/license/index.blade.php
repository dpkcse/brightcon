@extends('admin.layouts.app')
@section('title', 'License')
@section('page-heading', 'License and update entitlement')
@section('content')
<div class="card"><div class="card-body">
    <h2 class="h5">Status: {{ str_replace('_', ' ', ucfirst($status)) }}</h2>
    <p>{{ $decision->notice ?: 'The license is active.' }}</p>
    <p><strong>Update entitlement:</strong> {{ app(\App\Services\Licensing\LicensePolicyService::class)->updatesAllowed() ? 'Available' : 'Unavailable' }}</p>
    <form method="POST" action="{{ route('admin.license.activate') }}">@csrf
        <label class="form-label" for="provider">Provider</label>
        <select class="form-select mb-3" id="provider" name="provider">
            @foreach($providers as $id => $provider)<option value="{{ $id }}">{{ $provider['label'] }}</option>@endforeach
        </select>
        <label class="form-label" for="credential">Signed license</label>
        <textarea class="form-control mb-3" id="credential" name="credential" rows="5" required autocomplete="off"></textarea>
        <button class="btn btn-primary-brand" type="submit">Activate or replace license</button>
    </form>
</div></div>
@endsection
