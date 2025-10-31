@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg rounded-4" style="border: 2px solid #ddd;">
        <div class="card-body p-5">
            <h5 class="card-title text-center" style="color: #343a40;">Unassigned Leads</h5>

            <!-- Display success or error messages -->
            @if (session('success'))
                <div class="alert alert-success fade show" role="alert">
                    {{ session('success') }}
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger fade show" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Display validation errors -->
            @if ($errors->any())
                <div class="alert alert-danger fade show" role="alert">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form for transferring unassigned leads -->
            <form action="{{ route('leads.unassigned.transfer') }}" method="POST">
                @csrf

                <!-- List of unassigned leads with checkboxes -->
                <div class="mb-4">
                    <label class="form-label fw-bold" style="color: #343a40;">Select Leads to Transfer</label>
                    @if($unassignedLeads->isEmpty())
                        <p class="text-muted">No unassigned leads found.</p>
                    @else
                        @foreach($unassignedLeads as $lead)
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="lead_ids[]" value="{{ $lead->id }}" id="lead-{{ $lead->id }}">
                                <label class="form-check-label" for="lead-{{ $lead->id }}" style="font-weight: 500;">{{ $lead->name }}</label>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Target agent selection -->
                <div class="mb-4">
                    <label for="target_agent_id" class="form-label fw-bold" style="color: #343a40;">Transfer to User</label>
                    <select class="form-select form-select-lg shadow-sm rounded-3" id="target_agent_id" name="target_agent_id" required>
                        <option disabled selected>Choose a User</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Transfer button -->
                <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #343a40, #495057); color: white; border-radius: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 10px 20px; transition: transform 0.2s;">
                    <strong>Transfer Leads</strong>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
