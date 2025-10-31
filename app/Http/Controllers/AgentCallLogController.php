<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CallLog;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class AgentCallLogController extends Controller
{
    public function create()
    {
        // Get the leads assigned to the logged-in agent
        $agent = Auth::guard('agent')->user(); // Ensure you're using 'agent' guard
        $assignedLeads = Lead::where('assigned_agent_id',  $agent->id)
            ->orderBy('name', 'asc') // Order by name in ascending order
            ->get();

        return view('agentlogin.calllogs_create', compact('assignedLeads'));
    }

    public function store(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'lead_id' => 'required|exists:leads,id',  // Ensure the lead exists
            'agent_id' => 'required|exists:agents,id', // Ensure the agent exists
            'call_time' => 'required|date',            // Ensure the call time is valid
            'duration' => 'required|numeric|min:1',   // Duration should be numeric and at least 1 minute
            'notes' => 'nullable|string',
            'outcome' => 'required|string|in:not interested,interested,follow-up,closed', // Validate outcome
            'follow_up_date' => 'nullable|date|after_or_equal:today',
        ]);
        $lead = Lead::findOrFail($validated['lead_id']);
        // Create the new call log
        CallLog::create([
            'lead_id' => $validated['lead_id'],
            'agent_id' => $validated['agent_id'],
            'call_time' => $validated['call_time'],
            'duration' => $validated['duration'],
            'notes' => $validated['notes'],
            'outcome' => $validated['outcome'],
            'company_id' => $lead->company_id,
        ]);

        if ($lead->status === 'new') {
            $lead->status = 'contacted';
        }

        if (in_array($validated['outcome'], ['follow-up', 'interested'])) {
            $lead->follow_up_date = $validated['follow_up_date'] ?? now()->addDays(2)->toDateString();
        }

        $lead->save();

        // Redirect back to the call logs page with a success message
        return redirect()->route('agent.callLogs')->with('success', 'Call log added successfully.');
    }

    public function destroy($id)
    {
        $callLog = CallLog::findOrFail($id);
        $callLog->delete();

        return redirect()->route('agent.callLogs')->with('success', 'Call log deleted successfully.');
    }
}
