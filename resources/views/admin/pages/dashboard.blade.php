@extends('admin.layouts.app')
@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')
@section('content')
<div class="row g-4">
 @foreach([['Total Projects',$totalProjects],['Total Services',$totalServices],['Gallery Images',$totalGalleryImages],['Contact Messages',$totalContactMessages],['Unread Messages',$unreadContactMessages]] as [$label,$value])
 <div class="col-md-6 col-xl"><div class="card stat-card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">{{ $label }}</div><div class="display-6 fw-bold">{{ $value ?? 0 }}</div></div></div></div>
 @endforeach
 <div class="col-lg-7"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Recent Contact Messages</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th></tr></thead><tbody>@forelse($recentContactMessages as $message)<tr><td>{{ $message->full_name }}</td><td>{{ $message->email }}</td><td>{{ $message->subject ?? '—' }}</td><td><span class="badge {{ $message->is_read ? 'text-bg-secondary' : 'text-bg-danger' }}">{{ $message->is_read ? 'Read' : 'Unread' }}</span></td></tr>@empty<tr><td colspan="4" class="text-center text-muted py-4">No contact messages yet.</td></tr>@endforelse</tbody></table></div></div></div></div>
 <div class="col-lg-5"><div class="card border-0 shadow-sm"><div class="card-header bg-white fw-semibold">Quick Links</div><div class="card-body d-grid gap-2">@foreach([['General Settings',route('admin.settings.general.edit')],['Theme Settings',route('admin.settings.theme.edit')],['Menu Items',route('admin.menu-items.index')],['Social Links',route('admin.social-links.index')],['Footer Links',route('admin.footer-links.index')]] as [$label,$url])<a class="btn btn-outline-primary-brand text-start" href="{{ $url }}">{{ $label }}</a>@endforeach</div></div></div>
</div>
@endsection
