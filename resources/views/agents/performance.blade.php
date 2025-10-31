@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4 text-dark">User Performance</h1>
    
    <!-- Chart Section (Full Width, Reduced Height) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm rounded-4 p-3">
                <h5 class="text-dark">Leads Overview</h5>
                <div style="height: 300px;">
                    <canvas id="leadsBarChart" style="width: 100%; height: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-striped shadow-sm rounded-4">
            <thead class="bg-dark text-white">
                <tr>
                    <th>User Name</th>
                    <th>Total Leads Assigned</th>
                    <th>Leads Converted</th>
                    <th>Conversion Rate (%)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $chartDataNames = [];
                    $chartDataAssigned = [];
                    $chartDataConverted = [];
                @endphp

                @foreach($agents as $agent)
                    @php
                        $chartDataNames[] = $agent->name;
                        $chartDataAssigned[] = $agent->leads_count;
                        $chartDataConverted[] = $agent->leads_converted;
                    @endphp

                    <tr>
                        <td>{{ $agent->name }}</td>
                        <td>{{ $agent->leads_count }}</td>
                        <td>{{ $agent->leads_converted }}</td>
                        <td>{{ $agent->leads_count > 0 ? round(($agent->leads_converted / $agent->leads_count) * 100, 2) : 0 }}</td>
                        <td>
                            <a href="{{ route('agents.details', $agent->id) }}" class="btn btn-info btn-sm">View Details</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const agentNames = {!! json_encode($chartDataNames) !!};
    const leadsAssigned = {!! json_encode($chartDataAssigned) !!};
    const leadsConverted = {!! json_encode($chartDataConverted) !!};

    const leadsBarChartCtx = document.getElementById('leadsBarChart').getContext('2d');
    const leadsBarChart = new Chart(leadsBarChartCtx, {
        type: 'bar',
        data: {
            labels: agentNames,
            datasets: [
                {
                    label: 'Leads Assigned',
                    backgroundColor: '#2196f3',
                    data: leadsAssigned
                },
                {
                    label: 'Leads Converted',
                    backgroundColor: '#4caf50',
                    data: leadsConverted
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>
@endsection
