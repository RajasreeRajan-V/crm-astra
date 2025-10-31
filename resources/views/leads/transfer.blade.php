@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="card mx-auto shadow-lg" style="max-width: 800px; border-radius: 12px; border: 2px solid transparent; background: linear-gradient(#fff, #fff) padding-box, linear-gradient(135deg, #343a40, #495057) border-box;">
        <div class="card-body p-5">
            <h4 class="card-title text-center text-dark fw-bold mb-4">Transfer Leads</h4>

            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form for selecting leads and target agent -->
            <form action="{{ route('leads.transfer.store') }}" method="POST">
                @csrf

                <!-- Source Agent Selection -->
                <div class="mb-4">
                    <label for="source_agent_id" class="form-label fw-bold text-dark">Select Source User</label>
                    <select class="form-select form-select-lg rounded shadow-sm" id="source_agent_id" name="source_agent_id" onchange="fetchLeads(this.value)" required style="border: 1px solid #ddd; background-color: #f9f9f9; transition: all 0.3s;">
                        <option disabled selected>Choose a User</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('source_agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    @error('source_agent_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Leads List with Checkboxes -->
                <div id="leads-list" class="mb-4" style="display: none;">
                    <label class="form-label fw-bold text-dark">Select Leads to Transfer</label>
                    <div id="leads-checkboxes" class="row gx-3 gy-3">
                        <!-- Checkboxes will be dynamically populated here -->
                    </div>
                </div>

                <!-- Target Agent Selection -->
                <div class="mb-4">
                    <label for="target_agent_id" class="form-label fw-bold text-dark">Transfer to User</label>
                    <select class="form-select form-select-lg rounded shadow-sm" id="target_agent_id" name="target_agent_id" required style="border: 1px solid #ddd; background-color: #f9f9f9; transition: all 0.3s;">
                        <option disabled selected>Choose a User</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('target_agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                    @error('target_agent_id')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold" style="background: linear-gradient(135deg, #343a40, #495057); border: none; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); transition: transform 0.2s;">
                    Transfer Selected Leads
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    // JavaScript to fetch leads based on selected source agent
    function fetchLeads(agentId) {
        if (!agentId) return;
        let apiUrl = "{{ url('/api/leads-by-agent') }}/" + agentId;  // Using full URL
        
        fetch(apiUrl)
    
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(data => {
                const leadsList = document.getElementById('leads-list');
                const leadsCheckboxes = document.getElementById('leads-checkboxes');
                leadsCheckboxes.innerHTML = ''; // Clear any previous leads

                if (data.length > 0) {
                    data.forEach(lead => {
                        leadsCheckboxes.innerHTML += `
                            <div class="col-12 col-sm-6 col-md-4">
                                <div class="form-check p-2" style="background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                    <input class="form-check-input" type="checkbox" name="lead_ids[]" value="${lead.id}" id="lead-${lead.id}">
                                    <label class="form-check-label fw-bold text-dark" for="lead-${lead.id}">${lead.name}</label>
                                </div>
                            </div>`;
                    });
                    leadsList.style.display = 'block'; // Show leads list
                } else {
                    leadsList.style.display = 'none'; // Hide if no leads found
                }
            })
            .catch(error => {
                console.error('Error fetching leads:', error);
            });
    }
</script>
@endsection
