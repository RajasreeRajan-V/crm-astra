@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-center text-md-start">All Leads</h1>

    <!-- Filter and Search Form -->
    <form action="{{ route('leads.index') }}" method="GET" id="filterForm" class="mb-4 row g-3">
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
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Leads Table -->
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Source</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $index => $lead)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $lead->name }}</td>
                        <td>{{ $lead->contact }}</td>
                        <td>{{ $lead->source }}</td>
                        <td>{{ $lead->email }}</td>
                        <td>{{ ucfirst($lead->status) }}</td>
                        <!--<td>{{ $lead->created_at->format('Y-m-d H:i:s') }}</td>-->
                       <td>
                          {{ \Carbon\Carbon::parse($lead->created_at)
                              ->timezone('Asia/Kolkata')
                              ->format('Y-m-d h:i A') }}
                        </td>

                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this lead?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center">No leads found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
