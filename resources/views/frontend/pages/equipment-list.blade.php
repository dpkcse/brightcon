@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('app.name'))
@section('title', 'Equipment List | '.$companyName)
@section('meta_description', 'Representative equipment capability list for construction and engineering works.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Equipment List', 'description' => 'Representative equipment capacity for construction operations.'])
<section class="container-xl section-spacing">
    {{-- Future phase can replace this fallback array with a CMS-managed equipment table. --}}
    @php($equipment = [['Excavator','Earthmoving','20 Ton','2','Available'],['Concrete Mixer','Concrete Works','500 L','4','Available'],['Dump Truck','Transport','10 m³','6','Available'],['Crane','Lifting','25 Ton','1','On Demand'],['Road Roller','Road Works','10 Ton','2','Available'],['Generator','Power Backup','100 kVA','3','Available']])
    <div class="equipment-table rounded-4 overflow-hidden"><table class="table table-hover mb-0"><thead><tr><th>Equipment Name</th><th>Category</th><th>Capacity</th><th>Quantity</th><th>Status</th></tr></thead><tbody>@foreach($equipment as $row)<tr>@foreach($row as $cell)<td data-label="{{ ['Equipment Name','Category','Capacity','Quantity','Status'][$loop->index] }}">{{ $cell }}</td>@endforeach</tr>@endforeach</tbody></table></div>
</section>
@endsection
