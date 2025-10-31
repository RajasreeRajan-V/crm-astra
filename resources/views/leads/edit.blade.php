@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4" 
                 style="background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px);">
                <!-- Card Header -->
                <div class="card-header text-center text-white rounded-top-4" 
                     style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
                    <h4 class="mb-0">Edit Lead</h4>
                </div>
                <!-- Card Body -->
                <div class="card-body p-5">
                    @if ($errors->any())
    <div class="alert alert-danger rounded-3 shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
                    <form action="{{ route('leads.update', $lead->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Name Input -->
                        <div class="form-group mb-4">
                            <label for="name" class="form-label fw-bold text-secondary">Name</label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg shadow-sm rounded-pill border-0" 
                                id="name" 
                                name="name" 
                                value="{{ $lead->name }}" 
                                required
                                placeholder="Enter lead name"
                                style="background: rgba(240, 240, 240, 0.9);">
                        </div>
                        
                        <!-- Contact Input -->
                        <div class="form-group mb-4">
                            <label for="contact" class="form-label fw-bold text-secondary">Contact</label>
                            <input 
                                type="text" 
                                class="form-control form-control-lg shadow-sm rounded-pill border-0" 
                                id="contact" 
                                name="contact" 
                                value="{{ $lead->contact }}" 
                                required
                                placeholder="Enter contact details"
                                style="background: rgba(240, 240, 240, 0.9);">
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="email" class="form-label fw-bold text-secondary">Email</label>
                            <input 
                                type="email" 
                                class="form-control form-control-lg shadow-sm rounded-pill border-0" 
                                id="email" 
                                name="email" 
                                value="{{ $lead->email }}" 
                                placeholder=""
                                style="background: rgba(240, 240, 240, 0.9);">
                        </div>
                        
                        <!-- Source Dropdown -->
                        <div class="form-group mb-4">
                            <label for="source" class="form-label fw-bold text-secondary">Source</label>
                            <select 
                                class="form-select form-select-lg shadow-sm rounded-pill border-0" 
                                id="source" 
                                name="source" 
                                style="background: rgba(240, 240, 240, 0.9);">
                                @foreach($sources as $source)
                                    <option value="{{ $source }}" 
                                            {{ $lead->source == $source ? 'selected' : '' }}>
                                        {{ ucfirst($source) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Assigned Agent Dropdown -->
                        <div class="form-group mb-4">
                            <label for="assigned_agent_id" class="form-label fw-bold text-secondary">Assigned User</label>
                            <select 
                                class="form-select form-select-lg shadow-sm rounded-pill border-0" 
                                id="assigned_agent_id" 
                                name="assigned_agent_id" 
                                required
                                style="background: rgba(240, 240, 240, 0.9);">
                                <option value="">Select User</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" 
                                            {{ $lead->assigned_agent_id == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Rate and Deal Item (Only when status is NOT 'closed') -->
                        @if($lead->status !== 'closed')
                            <div class="form-group mb-4">
                            <label for="rate" class="form-label fw-bold text-secondary">Rate</label>
                            <input 
                            type="text" 
                            class="form-control form-control-lg shadow-sm rounded-pill border-0" 
                            id="rate" 
                            name="rate" 
                            value="{{ $lead->rate }}" 
                            placeholder="Enter rate"
                            style="background: rgba(240, 240, 240, 0.9);">
                            </div>
                        @endif

                        <!-- Deal Item (Always show this field) -->
                            <div class="form-group mb-4">
                            <label for="deal_item" class="form-label fw-bold text-secondary">Deal Item</label>
                            <input 
                            type="text" 
                            class="form-control form-control-lg shadow-sm rounded-pill border-0" 
                            id="deal_item" 
                            name="deal_item" 
                            value="{{ $lead->deal_item }}" 
                            placeholder="Enter deal item"
                            style="background: rgba(240, 240, 240, 0.9);">
                            </div>
                       
                        
                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('leads.index') }}" 
                               class="btn btn-outline-secondary px-4 py-2 rounded-pill me-2"
                               style="transition: all 0.3s ease; border: 2px solid #6a11cb;">
                               Cancel
                            </a>
                            <button type="submit" 
                                    class="btn btn-gradient px-4 py-2 rounded-pill text-white"
                                    style="background: linear-gradient(135deg, #6a11cb, #2575fc); 
                                           border: none; transition: all 0.3s ease;">
                                Update Lead
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
