@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Update Lead Status</h1>
    <!-- Filter and Search Form -->
    <form action="{{ route('leads.updateStatusIndex') }}" method="GET" class="mb-3">
        <div class="row align-items-end">
            <!-- Filter by Status -->
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Filter by Status:</label>
                <select name="status" id="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $statusFilter === $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search by Name -->
            <div class="col-md-4 mb-3">
                <label for="search_term" class="form-label">Search by Name:</label>
                <input type="text" name="search_term" id="search_term" class="form-control" value="{{ request()->search_term }}">
            </div>

            <!-- Search and Reset Buttons -->
            <div class="col-md-4 mb-3 d-flex justify-content-start align-items-end">
                <button type="submit" class="btn btn-primary me-2">Search</button>
                <a href="{{ route('leads.updateStatusIndex') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>
    
    <div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Current Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $index => $lead)
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Auto-incremented ID -->
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->contact }}</td>
                    <td>{{ ucfirst($lead->status) }}</td>
                    <td>
                        <a href="{{ route('leads.editStatus', $lead->id) }}" class="btn btn-success btn-sm">Update Status</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
@endsection
