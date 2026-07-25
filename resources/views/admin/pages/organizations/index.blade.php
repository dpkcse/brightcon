@extends('admin.layouts.app')
@section('title', 'Organizations')
@section('page-heading', 'Organizations')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center"><strong>Organizations</strong><a class="btn btn-primary-brand btn-sm" href="{{ route('admin.organizations.create') }}">Add New</a></div>
    <div class="table-responsive"><table class="table align-middle mb-0"><thead><tr><th>Name</th><th>Logo</th><th>Order</th><th>Homepage</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    @forelse($items as $item)<tr><td>{{ $item->name }}</td><td>@if($item->logo)<img src="{{ Storage::url($item->logo) }}" style="width:100px;height:50px;object-fit:contain" alt="">@else — @endif</td><td>{{ $item->display_order }}</td><td>{{ $item->is_featured ? 'Featured' : 'Hidden' }}</td><td><span class="badge {{ $item->is_active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span></td><td class="text-end"><a class="btn btn-sm btn-outline-primary-brand" href="{{ route('admin.organizations.edit', $item) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('admin.organizations.destroy', $item) }}" onsubmit="return confirm('Delete this organization?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
    @empty<tr><td colspan="6" class="text-center text-muted py-4">No organizations found.</td></tr>@endforelse
    </tbody></table></div><div class="card-footer bg-white">{{ $items->links() }}</div>
</div>
@endsection
