@extends('agentlogin.layout')
@section('content')
<style>
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .add-btn {
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .filter-form {
        margin-bottom: 20px;
    }

    .filter-btn {
        margin-top: 30px;
    }

    .cancel-btn {
        margin-top: 30px;
    }
</style>

<!-- Main Content -->
<div class="content mt-5">
    <h1>Call Logs</h1>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('agent.callLogs') }}" class="row filter-form">
        <div class="col-md-4">
            <label for="lead_name" class="form-label">Lead Name</label>
            <input type="text" name="lead_name" id="lead_name" class="form-control" value="{{ request('lead_name') }}">
        </div>
        <div class="col-md-4">
            <label for="call_date" class="form-label">Call Date</label>
            <input type="date" name="call_date" id="call_date" class="form-control" value="{{ request('call_date') }}"  max="{{ now()->toDateString() }}">
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary filter-btn">Filter</button>
            <a href="{{ route('agent.callLogs') }}" class="btn btn-secondary cancel-btn">Cancel</a>
        </div>
    </form>

    <!-- Button to Add Call Log -->
    <a href="{{ route('agent.calllog.create') }}" class="btn btn-primary add-btn">Add Call Log</a>

    @if($callLogs->isEmpty())
        <p>No call logs found.</p>
    @else
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Lead Name</th> <!-- Updated -->
                    <th>Call Time</th>
                    <th>Duration</th>
                    <th>Notes</th>
                    <th>Outcome</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($callLogs as $index => $log)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $log->lead->name ?? 'N/A' }}</td> <!-- Display Lead Name -->
                        <td>{{ $log->call_time }}</td>
                        <td>{{ $log->duration }}</td>
                        <td>{{ $log->notes }}</td>
                        <td>{{ $log->outcome }}</td>
                        <td class="d-flex align-items-center">
                            <!-- Edit Button -->
                            <a href="{{ route('agent.calllog.edit', $log->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>

                            <!-- Delete Button -->
                            <form action="{{ route('agent.calllog.delete', $log->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this call log?');" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
