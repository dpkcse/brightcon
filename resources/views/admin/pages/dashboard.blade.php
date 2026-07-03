@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')
@section('content')
<div class="row g-4">
    <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body p-4"><h1 class="h3">Admin Dashboard</h1><p class="text-muted mb-0">Phase 1 foundation is ready. CMS modules will be implemented in future phases.</p></div></div></div>
    @foreach(['General Settings','Homepage CMS','Projects','Services'] as $label)
        <div class="col-md-6 col-xl-3"><div class="card stat-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Placeholder</div><div class="h5 mb-0">{{ $label }}</div></div></div></div>
    @endforeach
</div>
@endsection
