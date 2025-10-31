@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Add New Transaction</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf

                <!-- Select Lead -->
                <div class="mb-3">
                    <label for="lead_id" class="form-label">Select Lead</label>
                    <select name="lead_id" id="lead_id" class="form-select select2" required>
                        <option value="">-- Select a Lead --</option>
                        @foreach($closedLeads as $lead)
                            <option value="{{ $lead->id }}">
                            {{ $lead->name }}{{ $lead->deal_item ? ' (' . $lead->deal_item . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('lead_id')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Current Balance (Uneditable) -->
                <div class="mb-3">
                    <label for="current_balance" class="form-label">Current Balance</label>
                    <input type="text" id="current_balance" class="form-control" readonly>
                </div>

                <!-- Amount Paid -->
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount Paid</label>
                    <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" required>
                    @error('amount')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Notes (Optional) -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    @error('notes')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-success">Save Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 for Searchable Dropdown -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>

$(document).ready(function () {
    $('.select2').select2({
        placeholder: "Search and select a lead",
        allowClear: true
    });

    let currentBalance = 0;

    // Fetch Lead Balance on Selection Change
    $('#lead_id').on('change', function () {
        var leadId = $(this).val();
        if (leadId) {
            $.ajax({
                url: "{{ url('/transactions/get-lead-balance') }}/" + leadId,
                type: "GET",
                success: function (response) {
                    if (response.balance !== undefined) {
                        currentBalance = parseFloat(response.balance);
                        $('#current_balance').val(currentBalance);
                    } else {
                        $('#current_balance').val('N/A');
                        currentBalance = 0;
                    }
                },
                error: function () {
                    $('#current_balance').val('Error fetching balance');
                    currentBalance = 0;
                }
            });
        } else {
            $('#current_balance').val('');
            currentBalance = 0;
        }
    });

    // Validate amount before form submission
    $('form').on('submit', function (e) {
        let enteredAmount = parseFloat($('#amount').val());

        if (enteredAmount > currentBalance) {
            e.preventDefault(); // Stop form submission
            alert("The entered amount cannot exceed the current balance.");
        }
    });
});
</script>
@endsection
