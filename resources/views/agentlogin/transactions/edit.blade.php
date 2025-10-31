@extends('agentlogin.layout')

@section('title', 'Edit Transaction')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Transaction</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('agent.transactions.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="lead_id" class="form-label">Lead</label>
                    <input type="text" id="lead_id" class="form-control" value="{{ $transaction->lead->name }}" readonly>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount Paid</label>
                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" 
                        value="{{ old('amount', $transaction->previous_value - $transaction->new_value) }}" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ $transaction->notes }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('agent.transactions') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
