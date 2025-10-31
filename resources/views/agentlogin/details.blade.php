@extends('agentlogin.layout')

@section('title', 'Details')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Lead Details</h1>

    <!-- Lead Details Card -->
    <div class="card">
        <div class="card-header">
            Lead Information
        </div>
        <div class="card-body">
            <h5 class="card-title mb-3">Name: <span class="text-primary">{{ $lead->name }}</span></h5>
            <p>
                <strong>Phone:</strong> {{ $lead->contact }} <br>
                <strong>Status:</strong> <span class="badge bg-info">{{ ucfirst($lead->status) }}</span> <br>
                <strong>Source:</strong> {{ $lead->source }} <br>
                @if($lead->source === 'Referral' && !empty($lead->referral_id))
                <strong>Referred By:</strong> {{ $lead->referral->name }} <br>
                @endif
                <strong>Assigned On:</strong> {{ $lead->created_at->format('d-m-Y') }} <br>

                <!-- If status is "closed", always display Rate, Balance, and Deal Item -->
                @if(strtolower($lead->status) === 'closed')
                    <strong>Rate:</strong> {{ $lead->rate }} <br>
                    <strong>Balance:</strong> {{ $lead->balance }} <br>
                    <strong>Deal Item:</strong> {{ $lead->deal_item }} <br>
                @else
                    <!-- If status is NOT "closed", display Rate & Deal Item only if they are NOT NULL -->
                    @if(!is_null($lead->rate))
                        <strong>Rate:</strong> {{ $lead->rate }} <br>
                    @endif
                    @if(!is_null($lead->deal_item))
                        <strong>Deal Item:</strong> {{ $lead->deal_item }} <br>
                    @endif
                @endif
            </p>
            <!-- Action Buttons -->
            <div class="mt-4">
                <a href="{{ route('agent.leads.edit', $lead->id) }}" class="btn btn-primary me-2">Edit Details</a>
                <a href="{{ route('agent.lead.update-status', $lead->id) }}" class="btn btn-success me-2">Update Status</a>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('agent.leads') }}" class="btn btn-secondary">Back to Leads</a>
        </div>
    </div>

    <!-- Call Logs Section -->
    <div class="mt-5">
        <h2>Call Logs</h2>
        <div class="card">
            <div class="card-body">
                @if($callLogs->isEmpty())
                    <p class="text-muted">No call logs available for this lead.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Call Date</th>
                                <th>Duration</th>
                                <th>Notes</th>
                                <th>Outcome</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($callLogs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $log->call_time }}</td>
                                    <td>{{ $log->duration }}</td>
                                    <td>{{ $log->notes }}</td>
                                    <td>{{ $log->outcome }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
            </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="mt-5">
        <h2>Transaction History</h2>
        <div class="card">
            <div class="card-body">
                @if($balanceUpdateLogs->isEmpty())
                    <p class="text-muted">No transaction history available for this lead.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Total Deal Amount</th>
                                <th>Amount Paid</th>
                                <th>New Balance</th>
                                <th>Date</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($balanceUpdateLogs as $log)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $lead->rate }}</td>
                                    <td>{{ $log->previous_value - $log->new_value }}</td>
                                    <td>{{ $log->new_value }}</td>
                                    <td>{{ $log->created_at->format('d-m-Y H:i') }}</td>
                                    <td>{{ $log->notes }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
