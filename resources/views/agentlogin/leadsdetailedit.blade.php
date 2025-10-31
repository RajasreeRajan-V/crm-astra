@extends('agentlogin.layout')

@section('title', 'Edit Lead Details')

@section('content')
<div class="content-container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header">
            <h1 class="form-title">Edit Lead Details</h1>
        </div>
        <div class="card-body">
            <!-- Display success or error messages -->
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Edit Lead Form -->
            <form action="{{ route('agent.leads.update', $lead->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- For PUT request -->

                <!-- Lead Name -->
                <div class="mb-3">
                    <label for="name" class="form-label">Lead Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $lead->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contact Number -->
                <div class="mb-3">
                    <label for="contact" class="form-label">Contact Number</label>
                    <input type="text" id="contact" name="contact" class="form-control" value="{{ old('contact', $lead->contact) }}" required>
                    @error('contact')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lead Source (Dropdown) -->
                <div class="mb-3">
                    <label for="source" class="form-label">Lead Source</label>
                    <select id="source" name="source" class="form-control" required>
                        <option value="Website" {{ old('source', $lead->source) == 'Website' ? 'selected' : '' }}>Website</option>
                        <option value="Social Media" {{ old('source', $lead->source) == 'Social Media' ? 'selected' : '' }}>Social Media</option>
                        <option value="Email" {{ old('source', $lead->source) == 'Email' ? 'selected' : '' }}>Email</option>
                        <option value="Referral" {{ old('source', $lead->source) == 'Referral' ? 'selected' : '' }}>Referral</option>
                        <option value="Advertisement" {{ old('source', $lead->source) == 'Advertisement' ? 'selected' : '' }}>Advertisement</option>
                    </select>
                    @error('source')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lead Status (Uneditable) -->
                <div class="mb-3">
                    <label for="status" class="form-label">Lead Status</label>
                    <input type="text" id="status" name="status" class="form-control" value="{{ ucfirst($lead->status) }}" disabled>
                </div>

                <!-- Rate and Balance (Rate Uneditable if Status is Closed) -->
                @if($lead->status === 'closed')
                    <div class="mb-3">
                        <label for="rate" class="form-label">Rate</label>
                        <input type="number" id="rate" class="form-control" value="{{ $lead->rate }}" disabled>
                        <input type="hidden" name="rate" value="{{ $lead->rate }}">
                    </div>
                    <div class="mb-3">
                    <label for="balance" class="form-label">Balance</label>
                    <input type="number" id="balance" name="balance" class="form-control" 
                        value="{{ old('balance') && old('balance') <= $lead->balance ? old('balance') : $lead->balance }}" 
                        min="0" step="0.01" required>
                            @error('balance')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                    </div>
                    <div class="mb-3">
                        <label for="deal_item" class="form-label">Deal Item</label>
                        <input type="text" id="deal_item" name="deal_item" class="form-control" value="{{ old('deal_item', $lead->deal_item) }}">
                        @error('deal_item')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <!-- Hidden inputs to retain current values -->
                    <input type="hidden" name="rate" value="{{ $lead->rate }}">
                    <input type="hidden" name="balance" value="{{ $lead->balance }}">
                    <input type="hidden" name="deal_item" value="{{ $lead->deal_item }}">
                @endif

                <!-- Action Buttons -->
                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Update Lead</button>
                    <a href="{{ route('agent.lead.details', $lead->id) }}" class="btn btn-secondary">Back to Lead Details</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
