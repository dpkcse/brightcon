@extends('admin.layouts.app')
@section('title', 'License')
@section('page-heading', 'License and update entitlement')
@section('content')
<div class="card mb-4"><div class="card-body">
<h2 class="h5">License summary</h2><dl class="row mb-0">
<dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ str_replace('_', ' ', ucfirst($status)) }}</dd>
<dt class="col-sm-4">License type</dt><dd class="col-sm-8">{{ $currentActivation ? 'Single Site' : 'Not active' }}</dd>
<dt class="col-sm-4">Update entitlement</dt><dd class="col-sm-8">{{ app(\App\Services\Licensing\LicensePolicyService::class)->updatesAllowed() ? 'Available' : 'Unavailable' }}</dd>
<dt class="col-sm-4">Support entitlement</dt><dd class="col-sm-8">{{ in_array('support', data_get($currentActivation?->provider_data, 'entitlements', []), true) ? 'Available' : 'Unavailable' }}</dd>
<dt class="col-sm-4">Provider</dt><dd class="col-sm-8">{{ $currentActivation?->provider ?? 'None' }}</dd>
<dt class="col-sm-4">Production domain</dt><dd class="col-sm-8"><code>{{ $normalizedDomain }}</code></dd>
<dt class="col-sm-4">Installation UUID</dt><dd class="col-sm-8"><code>{{ $installationUuid }}</code></dd>
<dt class="col-sm-4">Public verification key</dt><dd class="col-sm-8">{{ $verificationKeyConfigured ? 'Configured' : 'Not configured' }}</dd>
</dl></div></div>
@if(!$currentActivation || $status !== 'active')
<div class="card mb-4"><div class="card-body"><h2 class="h5">Activation request</h2>
@if(!$portalEnabled)<p>Naxas portal activation is pending deployment. Manual signed-license activation remains available below.</p>
@elseif($activationRequest)
<p>Use this one-time activation request token on the Naxas Limited License Portal. After approval, return here and check the activation status.</p>
<dl class="row"><dt class="col-sm-4">Request token</dt><dd class="col-sm-8"><code id="activation-request-token">{{ $activationRequest->request_token_ciphertext ?? $activationRequest->masked_request_token }}</code> <button class="btn btn-sm btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('activation-request-token').textContent)">Copy token</button></dd>
<dt class="col-sm-4">Expiry</dt><dd class="col-sm-8">{{ $activationRequest->expires_at->toDayDateTimeString() }}</dd><dt class="col-sm-4">Current status</dt><dd class="col-sm-8">{{ ucfirst($activationRequest->status) }}</dd>
<dt class="col-sm-4">Product and version</dt><dd class="col-sm-8">{{ $activationRequest->product_reference }} {{ $activationRequest->application_version }}</dd></dl>
@if($activationRequest->request_token_ciphertext && in_array($activationRequest->status, ['pending', 'approved'], true) && $activationRequest->expires_at->isFuture())
<a class="btn btn-outline-primary" href="{{ $activationRequest->portal_url }}" target="_blank" rel="noopener noreferrer">Open Naxas License Portal</a>
<form class="d-inline" method="POST" action="{{ route('admin.license.check-activation') }}">@csrf<button class="btn btn-primary-brand" type="submit">Check activation status</button></form>
@else<form method="POST" action="{{ route('admin.license.request-activation') }}">@csrf<button class="btn btn-primary-brand" type="submit">Regenerate request</button></form>@endif
@else<form method="POST" action="{{ route('admin.license.request-activation') }}">@csrf<button class="btn btn-primary-brand" type="submit">Create activation request</button></form>@endif
</div></div>@endif
<div class="card"><div class="card-body"><h2 class="h5">Manual signed-license fallback</h2>
<p>Paste the long-lived signed license token supplied by Naxas Limited, or upload its text file. An activation request token is not a license.</p>
<form method="POST" action="{{ route('admin.license.activate') }}" enctype="multipart/form-data">@csrf<input type="hidden" name="provider" value="offline">
<label class="form-label" for="credential">Signed license token</label><textarea class="form-control mb-3" id="credential" name="credential" rows="5" autocomplete="off"></textarea>
<label class="form-label" for="license_file">Or signed license file</label><input class="form-control mb-3" id="license_file" name="license_file" type="file" accept=".txt,.license,text/plain">
<button class="btn btn-primary-brand" type="submit">Activate or replace license</button></form></div></div>
@endsection
