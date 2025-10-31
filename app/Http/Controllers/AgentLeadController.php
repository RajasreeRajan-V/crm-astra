<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use Illuminate\Support\Facades\Log;
use App\Models\Agent;
use App\Notifications\FollowUpNotification;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewLeadNotification;
use Carbon\Carbon;
use App\Models\BalanceUpdateLog;
use Illuminate\Support\Facades\Auth;

class AgentLeadController extends Controller
{
    public function edit($id)
    {
        // Fetch the lead by ID
        $lead = Lead::findOrFail($id);

        // Return the edit view with the lead details
        return view('agentlogin.leadsdetailedit', compact('lead'));
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        // Validate inputs
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'contact' => 'required|string|max:15',
            'contact' => [
                'required',
                'regex:/^[0-9]{10,12}$/',
                ],
            'source' => 'nullable|string|max:255',
            'rate' => 'nullable|numeric|min:0',
            'balance' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($lead) {
                    if ($value > $lead->balance) {
                        $fail('The new balance cannot be greater than the existing balance.');
                    }
                }
            ],
            'deal_item' => 'nullable|string|max:255',
        ]);

        // Track changes (only rate and balance)
        $changes = [];
        if ($lead->rate != $validated['rate']) {
            $changes['rate'] = [
                'previous' => $lead->rate,
                'new' => $validated['rate']
            ];
        }
        if ($lead->balance != $validated['balance']) {
            $changes['balance'] = [
                'previous' => $lead->balance,
                'new' => $validated['balance']
            ];
        }

        // Update lead details
        $lead->update([
            'name' => $validated['name'],
            'contact' => $validated['contact'],
            'source' => $validated['source'],
            'rate' => $validated['rate'] ?? $lead->rate,
            'balance' => $validated['balance'] ?? $lead->balance,
            'deal_item' => $validated['deal_item'],
        ]);

        // Log changes to the BalanceUpdateLog table
        foreach ($changes as $field => $values) {
            BalanceUpdateLog::create([
                'lead_id' => $lead->id,
                'field_updated' => $field,
                'previous_value' => $values['previous'],
                'new_value' => $values['new'],
                'updated_by' => Auth::guard('agent')->id(),
                'updated_at' => now(),
                'company_id' => $lead->company_id,
            ]);
        }

        return redirect()->route('agent.lead.details', $lead->id)->with('success', 'Lead updated successfully.');
    }




    public function showUpdateStatusForm($id)
    {
        $lead = Lead::findOrFail($id); // Ensure the lead exists
        $statuses = ['new', 'contacted', 'interested', 'follow-up', 'closed'];
        return view('agentlogin.update-status', compact('lead', 'statuses'));
    }


    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:new,contacted,interested,follow-up,closed',
            'rate' => $request->status === 'closed' ? 'required|numeric|min:0' : 'nullable',
            'deal_item' => $request->status === 'closed' ? 'nullable|string|max:255' : 'nullable',
        ]);

        $lead = Lead::findOrFail($id);

        // Check if transactions exist
        $hasTransactions = BalanceUpdateLog::where('lead_id', $id)->exists();

        if ($request->status === 'closed' && $hasTransactions && $lead->rate != $request->rate) {
            return redirect()->back()->with('error', 'Rate cannot be changed as transactions already exist for this lead.');
        }

        // Track changes
        $changes = [];

        // Only update rate if it's the first time closing
        if ($request->status === 'closed' && !$hasTransactions) {
            if ($lead->rate != $request->rate) {
                $changes['rate'] = $request->rate;
                $changes['balance'] = $request->rate; // Set balance = rate only if no previous balance
            }
        }

        // Always allow deal_item to be updated when status is closed
        if ($request->status === 'closed' && $lead->deal_item !== $request->deal_item) {
            $changes['deal_item'] = $request->deal_item;
        }

        // Update the lead, ensuring balance is NOT overridden if transactions exist
        $lead->update([
            'status' => $request->status,
            'rate' => ($request->status === 'closed' && !$hasTransactions) ? $request->rate : $lead->rate,
            'balance' => (!$hasTransactions && isset($changes['rate'])) ? $request->rate : $lead->balance,
            'deal_item' => $request->status === 'closed' ? $request->deal_item : null,
        ]);

        return redirect()->route('agent.lead.details', $id)->with('success', 'Lead status updated successfully.');
    }



    public function setFollowUpForm($id)
    {
        $lead = Lead::findOrFail($id);
        return view('agentlogin.set_followup', compact('lead'));
    }

    public function updateFollowUp(Request $request, $id)
    {
        $request->validate([
            'follow_up_date' => 'required|date|after:today',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->follow_up_date = $request->follow_up_date;
        $lead->save();

        return redirect()->route('agent.dashboard', $id)->with('success', 'Follow-up date updated successfully.');
    }

    public function sendTodayFollowUpsEmails()
    {
        $today = Carbon::today()->toDateString(); // Get today's date

        // Fetch leads that have a follow-up scheduled for today
        $leads = Lead::whereDate('follow_up_date', $today)->get();

        // Group the leads by their assigned agent
        $agents = $leads->groupBy(function ($lead) {
            return $lead->agent_id; // Assuming each lead has an 'agent_id' field
        });

        // Loop through each agent and send them the follow-up notifications
        foreach ($agents as $agentLeads) {
            $agent = $agentLeads->first()->agent; // Get the agent from the first lead in the collection

            if ($agent) {
                // Send the notification to the agent with all the leads assigned to them
                Notification::send($agent, new FollowUpNotification($agentLeads));
            }
        }
    }

    public function create()
    {
        $authAgent = Auth::guard('agent')->user();
        $leads = Lead::whereHas('agent', function ($query) use ($authAgent) {
            $query->where('company_id', $authAgent->company_id);
        })->orderBy('name', 'asc')->get(); // Fetch existing leads for referral selection
        $agents = Agent::where('company_id', $authAgent->company_id)->get();
            return view('agentlogin.addlead', compact('agents', 'leads'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|numeric|digits_between:8,15',
            'source' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'assigned_agent_id' => 'nullable|exists:agents,id', // Optional agent ID
            'deal_item' => 'nullable|string|max:255', // New field
            'rate' => 'nullable|numeric|min:0',
            'referral_id' => 'nullable|exists:leads,id' // Validate referral ID
        ]);

        $authAgent = Auth::guard('agent')->user();
        $data = $request->except('assigned_agent_id');
         if (!empty($request->rate)) {
        $data['balance'] = $request->rate;
        }
        $data['company_id'] = $authAgent->company_id;

        $lead = Lead::create($data);

        // Check if an agent is manually assigned
       $lead->assigned_agent_id = $request->assigned_agent_id;
        $lead->save();

        // Notify the assigned agent
        $agent = Agent::find($request->assigned_agent_id);
        Notification::send($agent, new NewLeadNotification($lead));

        Log::info("Lead ID {$lead->id} assigned to Agent ID {$agent->id}");
        return redirect()->route('agent.leads'); // Redirect to leads index
    }
}
