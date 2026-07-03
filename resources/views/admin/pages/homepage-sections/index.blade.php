@extends('admin.layouts.app')
@section('title','Homepage Sections')
@section('page-heading','Homepage Sections')
@section('content')
<div class="card border-0 shadow-sm"><div class="card-header bg-white"><strong>Homepage Sections</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Section</th><th>Key</th><th>Title</th><th>Sort</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $labels[$item->section_key] }}</td><td><code>{{ $item->section_key }}</code></td><td>{{ $item->title ?? '—' }}</td><td>{{ $item->sort_order }}</td><td><span class="badge {{ $item->status ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->status ? 'Active' : 'Inactive' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary-brand" href="{{ route('admin.homepage-sections.edit', $item) }}">Edit</a></td></tr>@endforeach</tbody></table></div></div></div>
@endsection
