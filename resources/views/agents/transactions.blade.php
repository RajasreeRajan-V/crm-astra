@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">All Transactions</h1>


     <!-- Add New Transaction Button -->
     <div class="mt-2 mb-2 d-flex justify-content-end">
        <a href="{{ route('transactions.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Add New Transaction
        </a>
    </div>
    <!-- Search & Filter Form -->
    <form action="{{ route('transactions.index') }}" method="GET" class="row g-3 align-items-end">
        <!-- Search Input -->
        <div class="col-md-3">
            <label for="search" class="form-label">Search (Lead Name or Deal Item)</label>
            <input type="text" name="search" class="form-control" placeholder="Search by Lead Name or Deal Item" value="{{ request('search') }}">
        </div>

        <!-- Agent Filter -->
        <div class="col-md-3">
            <label for="agent_id" class="form-label">Filter by Agent</label>
            <select name="agent_id" id="agentFilter" class="form-select">
                <option value="">All Agents</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                        {{ $agent->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Start Date Filter -->
        <div class="col-md-2">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <!-- End Date Filter -->
        <div class="col-md-2">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        <!-- Buttons -->
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="card mt-3">
        <div class="card-body">
            @if($transactions->isEmpty())
                <p class="text-muted">No transactions available.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Lead</th>
                                <th>Agent</th>
                                <th>Amount Paid</th>
                                <th>Previous Balance</th>
                                <th>New Balance</th>
                                <th>Date</th>
                                <th>Deal Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction->lead->name ?? '' }}</td>
                                    <td>{{ $transaction->updatedBy->name ?? ''}}</td>
                                    <td>{{ $transaction->previous_value - $transaction->new_value }}</td>
                                    <td>{{ $transaction->previous_value }}</td>
                                    <td>{{ $transaction->new_value }}</td>
                                    <td>{{ $transaction->created_at->format('d-m-Y H:i:s') }}</td>
                                    <td>{{ $transaction->lead->deal_item ?? '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                {{ $transactions->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('agentFilter').addEventListener('change', function() {
        this.form.submit(); // Auto-submit when agent is selected
    });
</script>
@endsection
