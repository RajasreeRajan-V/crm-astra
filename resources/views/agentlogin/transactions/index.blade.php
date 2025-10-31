@extends('agentlogin.layout')

@section('title', 'Transactions')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Transactions</h1>

    <div class="mb-4">
        <!-- Search & Filter Form -->
        <form action="{{ route('agent.transactions') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control" 
                    placeholder="Search by Lead Name or Deal Item" 
                    value="{{ request('search') }}">
            </div>

            <!-- Start Date Filter -->
            <div class="col-md-2">
                <input 
                    type="date" 
                    name="start_date" 
                    class="form-control" 
                    value="{{ request('start_date') }}">
            </div>

            <!-- End Date Filter -->
            <div class="col-md-2">
                <input 
                    type="date" 
                    name="end_date" 
                    class="form-control" 
                    value="{{ request('end_date') }}">
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Search</button>
                <a href="{{ route('agent.transactions') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>

    <div class="text-end mb-3">
        <a href="{{ route('agent.transactions.create') }}" class="btn btn-primary">Add New Transaction</a>
    </div>

    <div class="card">
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
                            <th>Deal Rate</th>
                            <th>Amount Paid</th>
                            <th>Previous Balance</th>
                            <th>New Balance</th>
                            <th>Date</th>
                            <th>Deal Item</th>
                            <th>Notes</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $transaction->lead->name }}</td>
                                <td>{{ $transaction->lead->rate ?? 'N/A' }}</td>
                                <td>{{ $transaction->previous_value - $transaction->new_value }}</td>
                                <td>{{ $transaction->previous_value }}</td>
                                <td>{{ $transaction->new_value }}</td>
                                <!--<td>{{ $transaction->created_at->format('d-m-Y H:i') }}</td>-->
                                <td>{{ $transaction->created_at->format('d-m-Y h:i A') }}</td>
                                <td>{{ $transaction->lead->deal_item }}</td>
                                <td>{{ $transaction->notes }}</td>
                                @if($transaction->created_at == $transaction->lead->balanceUpdateLogs()->latest()->first()->created_at) 
                                    <!-- Only show the "Edit" button for the most recent transaction -->
                                    <td><a href="{{ route('agent.transactions.edit', $transaction->id) }}" class="btn btn-warning btn-sm">Edit</a></td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                {{ $transactions->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </div>
</div>
@endsection
