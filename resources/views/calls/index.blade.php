@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>Call Logs</h2>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('callLogs.index') }}" class="mb-4">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <label for="agent_id" class="form-label">Agent</label>
                <select name="agent_id" id="agent_id" class="form-select">
                    <option value="">All Agents</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6 col-lg-3">
                <label for="lead_search" class="form-label">Search Lead</label>
                <input type="text" name="lead_search" id="lead_search" class="form-control" placeholder="Enter lead name" value="{{ request('lead_search') }}">
            </div>

            <div class="col-md-6 col-lg-3">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>

            <div class="col-md-6 col-lg-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('callLogs.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <!-- Call Logs Table -->
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Lead Name</th>
                    <th>Agent Name</th>
                    <th>Call Time</th>
                    <th>Duration (mins)</th>
                    <th>Outcome</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($callLogs as $log)
                    <tr>
                        <td>{{ $log->lead->name ?? 'N/A' }}</td>
                        <td>{{ $log->agent->name ?? 'N/A' }}</td>
                        <td>{{ $log->call_time }}</td>
                        <td>{{ $log->duration }}</td>
                        <td>{{ ucfirst($log->outcome) }}</td>
                        <td>{{ $log->notes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No call logs found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center">
    <nav>
        {{ $callLogs->links('pagination::bootstrap-5') }}
    </nav>
</div>
</div>
@endsection