@extends('admin.layouts.app')
@section('page-heading','Equipment')
@section('content')
<div class="d-flex justify-content-between mb-3"><form><input class="form-control" name="search" placeholder="Search"></form><a class="btn btn-primary-brand" href="{{ route('admin.equipment.create') }}">Add record</a></div><div class="card table-responsive"><table class="table"><thead><tr><th>Name</th><th>Status</th><th>Order</th><th></th></tr></thead><tbody>@forelse($items as $item)<tr><td>{{ $item->name }}</td><td><span class="badge text-bg-secondary">{{ ucfirst($item->status) }}</span></td><td>{{ $item->display_order }}</td><td><a href="{{ route('admin.equipment.edit',$item) }}">Edit</a> <form class="d-inline" method="POST" action="{{ route('admin.equipment.destroy',$item) }}">@csrf @method('DELETE')<button class="btn btn-link text-danger">Archive</button></form></td></tr>@empty<tr><td colspan="4">No records found.</td></tr>@endforelse</tbody></table>{{ $items->links() }}</div>
@endsection
