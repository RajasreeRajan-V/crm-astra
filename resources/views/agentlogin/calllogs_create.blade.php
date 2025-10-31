@extends('agentlogin.layout')
@section('content')
    <style>

        /* Main Content Styling */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        .form-container h2 {
            margin-bottom: 30px;
        }
    </style>

    <!-- Main Content -->
    <div class="content mt-5">
        <div class="form-container">
            <h2>Add Call Log</h2>

            <form action="{{ route('agent.calllog.store') }}" method="POST">
                @csrf

                <!-- Lead ID (Assigned Leads Only) -->
                <div class="mb-3">
                    <label for="lead_id" class="form-label">Lead Name</label>
                    <select id="lead_id" name="lead_id" class="form-control" required>
                        <option value="">Select a Lead</option>
                        @foreach ($assignedLeads as $lead)
                            <option value="{{ $lead->id }}">{{ $lead->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Agent ID (Automatically Generated) -->
                <div class="mb-3">
                    <label for="agent_id" class="form-label">User ID</label>
                    <input type="text" id="agent_id" name="agent_id" class="form-control" value="{{ Auth::guard('agent')->user()->id }}" readonly>
                </div>

                <!-- Call Time -->
                <div class="mb-3">
                    <label for="calltime" class="form-label">Call Time</label>
                    <input type="datetime-local" id="call_time" name="call_time" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"  max="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required >
                    <button type="button" class="btn btn-info mt-2" id="setNow">Now</button>
                </div>

                <!-- Duration -->
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (in minutes)</label>
                    <input type="number" id="duration" name="duration" class="form-control" required>
                </div>

                <!-- Notes -->
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" class="form-control" rows="4"></textarea>
                </div>

                <!-- Outcome -->
                <div class="mb-3">
                    <label for="outcome" class="form-label">Outcome</label>
                    <select id="outcome" name="outcome" class="form-control" required>
                        <option value="not interested">not interested</option>
                        <option value="interested">interested</option>
                        <option value="follow-up">follow-up</option>
                        <option value="closed">closed</option>
                    </select>
                </div>

                <div class="mb-3" id="follow-up-date-container" style="display: none;">
                <label for="follow_up_date" class="form-label">Follow-Up Date</label>
                <input type="date" id="follow_up_date" name="follow_up_date" class="form-control" >
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-success">Save Call Log</button>
                <a href="{{ route('agent.callLogs') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
    @endsection
