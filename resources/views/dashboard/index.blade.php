@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Dashboard Overview</h1>

    <!-- Summary Section -->
    <div class="row mb-5">
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Total Leads</h5>
                    <p class="display-4">{{ $totalLeads }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <p class="display-4">{{ $totalAgents }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Call Logs</h5>
                    <p class="display-4">{{ $totalCallLogs }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-5">
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5>Lead Conversion Rate</h5>
                    <p class="display-4">{{ number_format($conversionRate, 2) }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5>Pending Follow-ups</h5>
                    <p class="display-4">{{ $pendingFollowupsCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphical Analytics Section -->
    <div class="row mb-5">
        <div class="col-md-6">
            <h3 class="mb-3">Lead Status Distribution</h3>
            <canvas id="leadStatusChart"></canvas>
        </div>
        <div class="col-md-6">
            <h3 class="mb-3">Lead Source Breakdown</h3>
            <canvas id="leadSourceChart"></canvas>
        </div>
    </div>

    <!-- Lead Status Count Section -->
    <h3>Lead Status Counts</h3>
    <div class="row mb-5">
        @foreach($leadStatusCounts as $status => $count)
        <div class="col-md-4 col-sm-6 col-lg-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>{{ ucfirst($status) }}</h5>
                    <h4>{{ $count }}</h4>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('dashboard.leadsByStatus', $status) }}" class="btn btn-primary btn-sm">View Details</a>
                        <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#agentCountsModal-{{ $status }}">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for agent count by lead status -->
        <div class="modal fade" id="agentCountsModal-{{ $status }}" tabindex="-1" aria-labelledby="agentCountsModalLabel-{{ $status }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agentCountsModalLabel-{{ $status }}">User Lead Count for {{ ucfirst($status) }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @php
                        $adminCompanyId = auth()->user()->company_id;
                        $agentLeadCounts = \App\Models\Lead::where('status', $status)
                            ->whereHas('agent', function ($query) use ($adminCompanyId) {
                                $query->where('company_id', $adminCompanyId);
                            })
                            ->selectRaw('assigned_agent_id, COUNT(*) as count')
                            ->groupBy('assigned_agent_id')
                            ->with('agent')
                            ->get();
                        @endphp
                        <ul>
                            @foreach($agentLeadCounts as $agentCount)
                            <li>{{ $agentCount->agent->name ?? 'Unassigned' }}: {{ $agentCount->count }} leads</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row">
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5>Call Success Rate</h5>
                    <p class="display-4">{{ number_format($callSuccessRate, 2) }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5>Total Deal Amount</h5>
                    <p class="display-4">₹{{ number_format($totalDealAmount) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-12 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5>Total Revenue</h5>
                    <p class="display-4">₹{{ number_format($totalRevenue) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const statusCtx = document.getElementById('leadStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($leadStatusCounts->toArray())),
            datasets: [{
                data: @json(array_values($leadStatusCounts->toArray())),
                backgroundColor: [
                    '#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                ],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Lead Status Distribution' }
            }
        }
    });

    const sourceCtx = document.getElementById('leadSourceChart').getContext('2d');
    new Chart(sourceCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($leadSourceBreakdown->toArray())),
            datasets: [{
                label: 'Leads',
                data: @json(array_values($leadSourceBreakdown->toArray())),
                backgroundColor: '#4BC0C0'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Lead Source Breakdown' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
