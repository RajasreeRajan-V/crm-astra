@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card mx-auto shadow-lg" style="max-width: 600px; border-radius: 15px; background: #f9f9f9;">
        <div class="card-header" style="background: linear-gradient(135deg, #343a40, #495057); color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
            <h3 class="mb-0">Update Lead Status</h3>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <strong>Lead Name:</strong> <span class="text-muted">{{ $lead->name }}</span>
            </div>
            <div class="mb-3">
                <strong>Assigned User:</strong> <span class="text-muted">{{ $lead->agent->name ?? 'Unassigned' }}</span>
            </div>
            <div class="mb-3">
                <strong>Current Status:</strong> <span class="text-muted">{{ ucfirst($lead->status) }}</span>
            </div>

            <!-- Form to update status -->
            <form action="{{ route('leads.updateStatus', $lead->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="new_status" class="form-label fw-bold">Select New Status:</label>
                    <select name="new_status" id="new_status" class="form-select form-select-lg shadow-sm border-dark rounded-3">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ $lead->status == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #343a40, #495057); color: white; border-radius: 25px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); padding: 10px 20px; width: 100%; transition: transform 0.2s;">
                        <strong>Update Status</strong>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
