@extends('frontend.layouts.app')
@php($companyName = $siteSettings?->company_name ?: config('cms.defaults.company_name') ?: config('cms.product.name'))
@section('title', 'Equipment List | '.$companyName)
@section('meta_description', 'Representative equipment capability list for construction and engineering works.')
@section('content')
@include('frontend.partials.page-header', ['title' => 'Equipment List', 'description' => 'Representative equipment capacity for construction operations.'])
<section class="container-xl section-spacing">
    @php($fallback = collect([['name'=>'Excavator','category'=>'Earthmoving','capacity'=>'20 Ton','quantity'=>2,'unit'=>'units'],['name'=>'Concrete Mixer','category'=>'Concrete Works','capacity'=>'500 L','quantity'=>4,'unit'=>'units'],['name'=>'Crane','category'=>'Lifting','capacity'=>'25 Ton','quantity'=>1,'unit'=>'unit']]))
    @php($rows = $equipment->isNotEmpty() ? $equipment : $fallback)
    <div class="equipment-table rounded-4 overflow-hidden"><table class="table table-hover mb-0"><thead><tr><th>Equipment Name</th><th>Category</th><th>Capacity</th><th>Quantity</th></tr></thead><tbody>@foreach($rows as $row)<tr><td data-label="Equipment Name">{{ data_get($row,'name') }}</td><td data-label="Category">{{ data_get($row,'category') ?: '—' }}</td><td data-label="Capacity">{{ data_get($row,'capacity') ?: '—' }}</td><td data-label="Quantity">{{ data_get($row,'quantity') !== null ? data_get($row,'quantity').' '.data_get($row,'unit') : '—' }}</td></tr>@endforeach</tbody></table></div>
</section>
@endsection
