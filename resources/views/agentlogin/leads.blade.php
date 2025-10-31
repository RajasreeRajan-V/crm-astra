@extends('agentlogin.layout')
@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Assigned Leads</h1>
        <a href="{{ route('agent.create.lead') }}" class="btn btn-success">+ Add New Lead</a>
    </div>

    <!-- Filters -->
    <form action="{{ route('agent.leads') }}" method="GET" id="filterForm" class="mb-4 row g-3">
        <div class="col-lg-3 col-md-4 col-sm-6">
            <select 
                name="status" 
                class="form-select" 
                aria-label="Filter by Status">
                <option value="">Filter by Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <input 
                type="text" 
                name="search" 
                class="form-control" 
                placeholder="Search by Name" 
                value="{{ request('search') }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <input 
                type="date" 
                name="start_date" 
                class="form-control" 
                value="{{ request('start_date') }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-6">
            <input 
                type="date" 
                name="end_date" 
                class="form-control" 
                value="{{ request('end_date') }}">
        </div>

        <div class="col-lg-3 col-md-4 col-sm-12 d-flex align-items-end">
            <button type="submit" class="btn btn-primary me-2">Filter</button>
            <a href="{{ route('agent.leads') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Display Leads -->
    @if($leads->isEmpty())
        <p>No leads found.</p>
    @else
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Lead Name</th>
                            <th>Status</th>
                            <th>Assigned On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leads as $lead)
                            <tr>
                                <td>{{ $lead->name }}</td>
                                <td>{{ ucfirst($lead->status) }}</td>
                                <td>{{ $lead->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('agent.lead.details', $lead->id) }}" class="btn btn-sm btn-primary">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
