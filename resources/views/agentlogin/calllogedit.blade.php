@extends('agentlogin.layout')

@section('content')
<div class="container mt-5">
    <h1>Edit Call Log</h1>
    <form method="POST" action="{{ route('agent.calllog.update', $callLog->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="lead_id" class="form-label">Lead ID</label>
            <input type="text" name="lead_id" id="lead_id" class="form-control" value="{{ $callLog->lead_id }}" readonly>
        </div>
        <div class="mb-3">
            <label for="call_time" class="form-label">Call Time</label>
            <input type="datetime-local" name="call_time" id="call_time" class="form-control" value="{{ \Carbon\Carbon::parse($callLog->call_time)->format('Y-m-d\TH:i') }}" required>
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">Duration</label>
            <input type="text" name="duration" id="duration" class="form-control" value="{{ $callLog->duration }}" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea name="notes" id="notes" class="form-control">{{ $callLog->notes }}</textarea>
        </div>

        <div class="mb-3">
            <label for="outcome" class="form-label">Outcome</label>
            <select name="outcome" id="outcome" class="form-control" required>
                <option value="">Select outcome</option>
                <option value="not interested" {{ $callLog->outcome == 'not interested' ? 'selected' : '' }}>Not Interested</option>
                <option value="interested" {{ $callLog->outcome == 'interested' ? 'selected' : '' }}>Interested</option>
                <option value="follow-up" {{ $callLog->outcome == 'follow-up' ? 'selected' : '' }}>Follow-Up</option>
                <option value="closed" {{ $callLog->outcome == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>

        <div class="mb-3" id="follow_up_date_container" style="display: none;">
            <label for="follow_up_date" class="form-label">Follow-Up Date</label>
            <input type="date" name="follow_up_date" id="follow_up_date" class="form-control"
                value="{{ old('follow_up_date', optional($callLog->lead)->follow_up_date ? \Carbon\Carbon::parse($callLog->lead->follow_up_date)->format('Y-m-d') : '') }}">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

{{-- Move script INSIDE content or into a layout's script section --}}
<script>
    function toggleOutcomeFields() {
        const outcome = document.getElementById('outcome').value;
        document.getElementById('follow_up_date_container').style.display =
            (outcome === 'interested' || outcome === 'follow-up') ? 'block' : 'none';
        document.getElementById('closed_fields').style.display =
            (outcome === 'closed') ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleOutcomeFields();
        document.getElementById('outcome').addEventListener('change', toggleOutcomeFields);
    });
</script>
@endsection
