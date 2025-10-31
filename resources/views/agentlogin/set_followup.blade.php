@extends('agentlogin.layout')
@section('title', 'Set Follow-Up')
@section('content')
<!-- Main Content -->
    <div class="form-container mt-5">
            <h2>Set Follow-Up</h2>

            <form action="{{ route('agent.lead.updateFollowUp', $lead->id) }}" method="POST">
                @csrf
                <!-- Follow-Up Date -->
                <div class="mb-3">
                    <label for="follow_up_date" class="form-label">New Follow-Up Date</label>
                    <input type="date" id="follow_up_date" name="follow_up_date" class="form-control" style="max-width: 600px;" required>
                    </div>

                <!-- Buttons -->
                <button type="submit" class="btn btn-primary">Update Follow-Up</button>
                <a href="{{ route('agent.dashboard', $lead->id) }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    @endsection
