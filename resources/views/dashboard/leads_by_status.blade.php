@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Leads with Status: {{ ucfirst($status) }}</h1>

    @if($leads->isEmpty())
        <p>No leads found with this status.</p>
    @else
        <!-- Responsive Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Source</th>
                        <th>Status</th>
                        <th>Assigned User</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->contact }}</td>
                            <td>{{ $lead->email }}</td>
                            <td>{{ $lead->source }}</td>
                            <td>{{ ucfirst($lead->status) }}</td>
                            <td>{{ $lead->agent->name ?? 'Unassigned' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <a href="{{ route('dashboard.index') }}" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
@endsection
