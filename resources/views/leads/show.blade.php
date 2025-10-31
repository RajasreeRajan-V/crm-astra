@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center" style="color: #4c5c96;">Lead Details</h1>
    <div class="card border-0 shadow rounded-4" style="background: rgba(76, 92, 150, 0.05);">
        <!-- Card Header -->
        <div class="card-header text-white rounded-top-4" style="background: linear-gradient(135deg, #4c5c96, #7a8fbf);">
            <h5 class="mb-0 text-center">Lead Information</h5>
        </div>
        <!-- Card Body -->
        <div class="card-body">
           <!-- First Row: Name, Contact, Source -->
<div class="row mb-4 pb-3" style="border-bottom: 1px solid rgba(76, 92, 150, 0.2);">
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Name:</strong></h6>
        <p class="fs-6">{{ $lead->name }}</p>
    </div>
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Contact:</strong></h6>
        <p class="fs-6">{{ $lead->contact }}</p>
    </div>
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Email:</strong></h6>
        <p class="fs-6">{{ $lead->email }}</p>
    </div>
</div>

<!-- Second Row: Assigned Agent, Status -->
<div class="row mb-4 pb-3" style="border-bottom: 1px solid rgba(76, 92, 150, 0.2);">
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Source:</strong></h6>
        <p class="fs-6">{{ $lead->source }}</p>
    </div>
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Assigned User:</strong></h6>
        <p class="fs-6">{{ $lead->agent->name ?? 'N/A' }}</p>
    </div>
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Status:</strong></h6>
        <p class="fs-6">{{ ucfirst($lead->status) }}</p>
    </div>
    @if($lead->source == 'Referral' && !is_null($lead->referral_id))
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Referred By:</strong></h6>
        <p class="fs-6">{{ $lead->referral->name ?? 'N/A' }}</p>
    </div>
    @endif
</div>

<!-- Third Row: Rate, Balance, Deal Item -->
@if($lead->status == 'closed' || (!is_null($lead->rate) || !is_null($lead->deal_item)))
<div class="row mb-4 pb-3" style="border-bottom: 1px solid rgba(76, 92, 150, 0.2);">
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Deal Rate:</strong></h6>
        <p class="fs-6">{{ $lead->rate ?? 'N/A' }}</p>
    </div>
    @if($lead->status == 'closed')
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Balance:</strong></h6>
        <p class="fs-6">{{ $lead->balance ?? 'N/A' }}</p>
    </div>
    @endif
    <div class="col-md-4">
        <h6 class="text-dark"><strong>Deal Item:</strong></h6>
        <p class="fs-6">{{ $lead->deal_item ?? 'N/A' }}</p>
    </div>
</div>
@endif


        <!-- Card Footer -->
        <div class="card-footer d-flex justify-content-between" style="background: rgba(76, 92, 150, 0.1);">
            <a href="{{ route('leads.edit', $lead->id) }}" 
               class="btn btn-outline-primary rounded-pill px-4 py-2"
               style="transition: all 0.3s ease;">
               Edit Lead
            </a>
            <a href="{{ route('leads.index') }}" 
               class="btn btn-outline-secondary rounded-pill px-4 py-2"
               style="transition: all 0.3s ease;">
               Back to Leads
            </a>
        </div>
    </div>
</div>
@endsection
