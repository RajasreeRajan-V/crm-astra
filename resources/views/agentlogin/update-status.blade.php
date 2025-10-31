@extends('agentlogin.layout') 

@section('content')   
<!-- Main Content -->
<style>
    .card {
        width: 80%; /* Adjust the width as needed */
        max-width: 800px; /* Optional: Set a maximum width for larger screens */
        margin: 20px auto; /* Center the card */
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>
<div class="content mt-5">
    <div class="card">
        <div class="card-header">
            Update Lead Status
        </div>
        <div class="card-body">
            <h2>Update Status for <strong>{{ $lead->name }}</strong></h2>

            <!-- Display error alert if the rate cannot be changed -->
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('agent.lead.update-status.submit', $lead->id) }}" method="POST">
                @csrf

                <!-- Status Dropdown -->
                <div class="form-group">
                    <label for="status" class="form-label">Lead Status</label>
                    <select name="status" id="status" class="form-select" required>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <br>

                <!-- Rate Field (Visible only when status is 'closed') -->
                <div id="rate-field" class="form-group" style="display: none;">
                    <label for="rate" class="form-label">Rate</label>
                    <input type="number" name="rate" id="rate" class="form-control"
                           value="{{ old('rate', $lead->rate) }}"
                           placeholder="Enter rate" min="0" step="0.01">
                    @error('rate')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <br>
                <div id="deal-item-field" class="form-group" style="display: none;">
                    <label for="deal_item" class="form-label">Deal Item</label>
                    <input type="text" name="deal_item" id="deal_item" class="form-control"
                    value="{{ old('deal_item', $lead->deal_item) }}"
                    placeholder="Enter deal item">
                    @error('deal_item')
                    <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary mt-3">Update Lead</button>
            </form>

            <!-- Back to Lead Details Button -->
            <a href="{{ route('agent.lead.details', $lead->id) }}" class="btn btn-secondary mt-3">Back to Lead Details</a>
        </div>
    </div>
</div>

<!-- JavaScript to toggle Rate field visibility -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusDropdown = document.getElementById('status');
        const rateField = document.getElementById('rate-field');
        const dealItemField = document.getElementById('deal-item-field');

        // Function to toggle fields
        const toggleFields = () => {
            if (statusDropdown.value === 'closed') {
                rateField.style.display = 'block';
                dealItemField.style.display = 'block';
            } else {
                rateField.style.display = 'none';
                dealItemField.style.display = 'none';
            }
        };

        // Initialize on page load
        toggleFields();

        // Update visibility when status changes
        statusDropdown.addEventListener('change', toggleFields);
    });
</script>
@endsection
