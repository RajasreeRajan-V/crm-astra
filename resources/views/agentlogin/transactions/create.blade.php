@extends('agentlogin.layout')

@section('title', 'Add Transaction')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Add New Transaction</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('agent.transactions.store') }}" method="POST" id="transaction-form">
                @csrf
                <div class="mb-3">
                    <label for="lead_id" class="form-label">Select Lead</label>
                    <select name="lead_id" id="lead_id" class="form-control" required>
                        <option value="">-- Select a Lead --</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="current_balance" class="form-label">Current Balance</label>
                    <input type="text" id="current_balance" class="form-control" value="N/A" readonly>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount Paid</label>
                    <input type="number" name="amount" id="amount" class="form-control" min="0" required>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Transaction</button>
                <a href="{{ route('agent.transactions') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
    // Fetch and display the current balance of the selected lead
    document.getElementById('lead_id').addEventListener('change', function () {
        const leadId = this.value;

        if (leadId) {
            fetch("{{ url('/agent/lead') }}/" + leadId + "/balance")
                .then(response => response.json())
                .then(data => {
                    if (data.balance !== undefined) {
                        document.getElementById('current_balance').value = data.balance;
                    } else {
                        document.getElementById('current_balance').value = 'N/A';
                        alert('Unable to fetch balance for the selected lead.');
                    }
                })
                .catch(error => {
                    document.getElementById('current_balance').value = 'N/A';
                    console.error('Error fetching balance:', error);
                });
        } else {
            document.getElementById('current_balance').value = 'N/A';
        }
    });

    // Validate that the entered amount does not exceed the current balance
    document.getElementById('transaction-form').addEventListener('submit', function (event) {
        const currentBalance = parseFloat(document.getElementById('current_balance').value);
        const amountPaid = parseFloat(document.getElementById('amount').value);

        if (!isNaN(currentBalance) && amountPaid > currentBalance) {
            event.preventDefault(); // Prevent form submission
            alert('The entered amount exceeds the current balance. Please enter a valid amount.');
        }
    });
</script>
@endsection
