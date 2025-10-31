@extends('agentlogin.layout')

@section('title', 'User Dashboard')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 mt-3 fw-bold">User Dashboard</h1>
    <div class="row g-4">
        <!-- Performance Metrics Section -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white rounded-top">
                    <h5 class="mb-0">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total Leads Assigned:</span>
                            <span class="badge bg-success">{{ $totalLeads }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Converted Leads:</span>
                            <span class="badge bg-info">{{ $convertedLeads }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Conversion Rate:</span>
                            <span class="badge bg-warning text-dark">{{ $conversionRate }}%</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Total Calls:</span>
                            <span class="badge bg-danger">{{ $totalCalls }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Call Success Rate:</span>
                            <span class="badge bg-success">{{ $callSuccessRate }}%</span>
                        </li>
                    </ul>

                    <!-- Conversion Rate Doughnut Chart (Below Performance Metrics) -->
                    <div class="mt-3">
                        <canvas id="conversionChart" width="200" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Call Logs Section (Right Side) -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white rounded-top">
                    <h5 class="mb-0">Recent Call Logs</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @forelse ($callLogs as $callLog)
                            <li class="list-group-item">
                                <div>
                                    <strong>Lead:</strong> {{ $callLog->lead->name }}<br>
                                    <strong>Outcome:</strong> {{ ucfirst($callLog->outcome) }}<br>
                                    <strong>Call Time:</strong> {{ $callLog->call_time }}
                                </div>
                            </li>
                        @empty
                            <p class="text-muted">No recent call logs available.</p>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Follow-ups Section -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark rounded-top">
                    <h5 class="mb-0">Upcoming Follow-Ups</h5>
                </div>
                <div class="card-body">
                    @if ($followUps->isEmpty())
                        <p class="text-muted">No upcoming follow-ups.</p>
                    @else
                        <ul class="list-group">
                            @foreach ($followUps as $followUp)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>Lead:</strong> {{ $followUp->name }}<br>
                                        <strong>Follow-Up Date:</strong> {{ \Carbon\Carbon::parse($followUp->follow_up_date)->toDateString() }}
                                    </div>
                                    <a href="{{ route('agent.lead.followup', $followUp->id) }}" class="btn btn-sm btn-success">Update Follow-Up</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <!-- Assigned Leads Section -->
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white rounded-top">
                    <h5 class="mb-0">Assigned Leads</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($assignedLeads as $lead)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Lead Name:</strong> {{ $lead->name }}<br>
                                    <strong>Status:</strong> {{ ucfirst($lead->status) }}
                                </div>
                                <a href="{{ route('agent.lead.details', $lead->id) }}" class="btn btn-sm btn-primary">View Details</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/agent-tracker.js') }}"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Chart Script -->
<script>
    const ctx = document.getElementById('conversionChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Converted', 'Not Converted'],
            datasets: [{
                label: 'Lead Conversion',
                data: [{{ $convertedLeads }}, {{ $totalLeads - $convertedLeads }}],
                backgroundColor: ['#28a745', '#dc3545'],
                borderColor: ['#ffffff'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
