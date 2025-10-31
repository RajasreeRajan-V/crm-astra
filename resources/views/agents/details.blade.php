@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4 text-dark">User: {{ $agent->name }}</h1>

    <!-- Agent Performance Details -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Total Leads Assigned</h5>
                    <p class="card-text text-muted">{{ $leadsCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Leads Converted</h5>
                    <p class="card-text text-muted">{{ $leadsConverted }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Total Calls Made</h5>
                    <p class="card-text text-muted">{{ $totalCalls }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Agent Performance Metrics -->
    <div class="row mb-5">
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Average Call Duration</h5>
                    <p class="card-text text-muted">{{ $averageCallDuration }} mins</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Average Response Time to Leads</h5>
                    <p class="card-text text-muted">{{ $averageResponseTime }} hrs</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4">
                <div class="card-body">
                    <h5 class="card-title text-dark">Average Time to Close a Lead</h5>
                    <p class="card-text text-muted">{{ $averageTimeToClose }} days</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
    <div class="col-md-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="card-title text-dark">Total Deal Amount Closed</h5>
                <p class="card-text text-muted">₹{{ number_format($totalDealAmount, 2) }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm rounded-4">
            <div class="card-body">
                <h5 class="card-title text-dark">Total Revenue Generated</h5>
                <p class="card-text text-muted">₹{{ number_format($totalRevenue, 2) }}</p>
            </div>
        </div>
    </div>
</div>

    <!-- Call Logs Section -->
    <h3 class="mb-4 text-dark">Call Logs</h3>
    <div class="table-responsive">
        @if($agent->callLogs->isEmpty())
            <p class="text-muted">No call logs available for this User.</p>
        @else
            <table class="table table-striped table-bordered shadow-sm rounded-4">
                <thead class="bg-dark text-white">
                    <tr>
                        <th>Lead Name</th>
                        <th>Call Time</th>
                        <th>Duration</th>
                        <th>Outcome</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agent->callLogs as $callLog)
                        <tr>
                            <td>{{ $callLog->lead->name ?? 'N/A' }}</td>
                            <td>{{ $callLog->call_time }}</td>
                            <td>{{ $callLog->duration }} mins</td>
                            <td>{{ $callLog->outcome }}</td>
                            <td>{{ $callLog->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
