<?php

namespace App\Http\Controllers;

use App\Models\CallLog;
use App\Models\Lead;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\FollowUpNotification;
use Illuminate\Support\Facades\Auth;


class CallLogController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'agent_id' => 'required|exists:agents,id',
            'call_time' => 'required|date',
            'duration' => 'nullable|integer',
            'notes' => 'nullable|string',
            'outcome' => 'required|in:not interested,interested,follow-up,closed',
            'rate' => 'nullable|numeric',    // For closed status
            'balance' => 'nullable|numeric'  // For closed status
        ]);

        // Log the call
        $callLog = CallLog::create($request->all());
        $lead = Lead::findOrFail($request->lead_id);

        // Update status and send notification if needed
        if ($request->outcome === 'follow-up') {
            $lead->status = 'follow-up';
            // Send a follow-up notification to the agent
            Notification::send($callLog->agent, new FollowUpNotification($lead));
        } elseif ($request->outcome === 'closed') {
            $lead->status = 'closed';
            $lead->rate = $request->rate;       // Set rate
            $lead->balance = $request->balance; // Set balance
        }

        // Save lead changes
        $lead->save();

        return redirect()->route('calls.index', $request->lead_id); // Redirect to call logs for the lead
    }

    // Display call logs for a lead
    public function index(Request $request)
    {
        $admin = Auth::user(); // Admin user
        $companyId = $admin->company_id;
        // Start with a base query that fetches all call logs
        $query = CallLog::with(['lead', 'agent'])
            ->where('company_id', $companyId);

        // Filter by agent ID if provided
        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->input('agent_id'));
        }

        // Search by lead name if provided
        if ($request->filled('lead_search')) {
            $query->whereHas('lead', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->input('lead_search') . '%');
            });
        }

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('call_time', [$request->input('start_date'), $request->input('end_date')]);
        }

        // Get the filtered results and paginate them
        $callLogs = $query
            ->orderBy('call_time', 'desc')
            ->paginate(10);

        // Fetch all agents to populate the filter options
        $agents = Agent::where('company_id', $companyId)->get();

        return view('calls.index', compact('callLogs', 'agents'));
    }

    // Schedule a follow-up (implement logic as needed)
    public function scheduleFollowUp($lead_id)
    {
        // Implement logic for scheduling a follow-up
        return redirect()->route('leads.show', $lead_id); // Redirect to the lead's details page
    }

    public function edit($id)
    {
        // Fetch the call log by ID
        $callLog = CallLog::findOrFail($id);

        // Return the edit view with the call log data
        return view('agentlogin.calllogedit', compact('callLog'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'call_time' => 'required|date',
            'duration' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'outcome' => 'required|string|in:not interested,interested,follow-up,closed',
            'follow_up_date' => 'nullable|date',
            'rate' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
        ]);

        $callLog = CallLog::findOrFail($id);

        // Update Call Log data
        $callLog->update([
            'lead_id' => $request->lead_id,
            'call_time' => $request->call_time,
            'duration' => $request->duration,
            'notes' => $request->notes,
            'outcome' => $request->outcome,
        ]);

        // Update related Lead
        $lead = Lead::findOrFail($request->lead_id);

        if ($lead->status === 'new') {
            $lead->status = 'contacted';
        }

        if (in_array($request->outcome, ['interested', 'follow-up'])) {
            if ($request->filled('follow_up_date')) {
                $lead->follow_up_date = $request->follow_up_date;
            } elseif (empty($lead->follow_up_date)) {
                $lead->follow_up_date = now()->addDays(2)->toDateString();
            }
        }

        $lead->save();

        return redirect()->route('agent.callLogs')->with('success', 'Call log updated successfully.');
    }
}
