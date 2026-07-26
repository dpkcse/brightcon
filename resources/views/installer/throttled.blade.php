@extends('installer.layout')

@section('content')
<h1>Please wait before trying again</h1>
<div class="note" role="alert">Too many installation attempts were submitted. Please wait briefly and try again. Your database password and administrator password were not stored.</div>
@if($retryAfter > 0)
<p>You can retry in approximately <strong>{{ $retryAfter }} seconds</strong>.</p>
@else
<p>Wait briefly before returning to the installer.</p>
@endif
<a class="button" href="{{ route($returnRoute) }}">Back to {{ $returnRoute === 'install.review' ? 'review' : 'installer' }}</a>
@endsection
