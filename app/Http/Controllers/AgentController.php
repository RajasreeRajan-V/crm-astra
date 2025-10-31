<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\AgentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewLeadNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;



class AgentController extends Controller
{
    private function getAdminCompanyId()
    {
        return Auth::user()->company_id;
    }

    // Assign lead to an agent
    public function assignLead($lead_id, $agent_id)
    {
        $lead = Lead::findOrFail($lead_id);
        $lead->assigned_agent_id = $agent_id; // Assign the lead to the specified agent
        $lead->save(); // Save changes
        return redirect()->route('leads.index')->with('success', 'Lead assigned to agent successfully.'); // Redirect to leads index
    }

    public function assignLeadToAgent(Lead $lead)
    {
        $companyId = $lead->company_id;
        $agents = Agent::where('company_id', $companyId)->get();
        if ($agents->isEmpty()) {
            Log::error('No agents available to assign leads.');
            return; // Skip if no agents are available
        }

        // Retrieve the last assigned agent index from the database
        $tracking = AgentTracking::firstOrCreate(
            ['company_id' => $companyId],
            ['last_assigned_agent_index' => -1]
        );

        $nextIndex = ($tracking->last_assigned_agent_index + 1) % $agents->count();

        // Update the index for round-robin, looping back to 0 when reaching the end
        $agent = $agents->values()[$nextIndex];

        // Assign the lead to the selected agent
        $lead->assigned_agent_id = $agent->id;
        $lead->save();

        // Update the last assigned agent index in the database
        $tracking->last_assigned_agent_index = $nextIndex;
        $tracking->save();

        // Send notification to the agent about the new lead
        Notification::send($agent, new NewLeadNotification($lead));

        Log::info("Assigned Lead ID {$lead->id} to Agent ID {$agent->id}");
    }




    // List all agents
    public function index()
    {
        $companyId = $this->getAdminCompanyId();
        $agents = Agent::where('company_id', $companyId)->get();
        return view('agents.index', compact('agents')); // Return a view with the agents
    }

    public function create()
    {
        return view('agents.create'); // Return the create agent view
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email',
            'phone_no' => 'required|numeric|digits_between:10,15|unique:agents,phone_no',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $agent = Agent::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_no' => $request->phone_no,
            'password' => bcrypt($request->password),
            'company_id' => $this->getAdminCompanyId(),
        ]);

        return redirect()->route('agents.index');
    }

    public function performance()
    {
        $companyId = $this->getAdminCompanyId();
        $agents = Agent::where('company_id', $companyId)
        ->withCount([
            'leads as leads_count',  // Counts all assigned leads
            'leads as leads_converted' => function ($query) {
                $query->where('status', 'closed');  // Counts only converted leads
            }
        ])->get();

        return view('agents.performance', compact('agents'));
    }

    public function show($id)
    {
        $agent = Agent::where('company_id', $this->getAdminCompanyId())->with('leads')->findOrFail($id);
        return view('agents.show', compact('agent'));
    }

    public function edit($id)
    {
        $agent = Agent::where('company_id', $this->getAdminCompanyId())->findOrFail($id);
        return view('agents.edit', compact('agent'));
    }

    public function update(Request $request, $id)
    {
        $agent = Agent::where('company_id', $this->getAdminCompanyId())->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email,' . $id,
            'phone_no' => 'required|numeric|digits_between:10,12|unique:agents,phone_no,' . $id,
        ]);

        $agent->update($request->all());

        return redirect()->route('agents.index')->with('success', 'Agent updated successfully.');
    }

    public function destroy($id)
    {
        $agent = Agent::where('company_id', $this->getAdminCompanyId())->findOrFail($id);
        Lead::where('assigned_agent_id', $id)->update(['assigned_agent_id' => null]);
        $agent->delete();

        return redirect()->route('agents.index')->with('success', 'Agent deleted successfully.');
    }

    public function showAgentDetails($agentId)
    {
        $companyId = $this->getAdminCompanyId();
        $agent = Agent::with(['callLogs', 'callLogs.lead', 'leads'])
            ->where('company_id', $companyId)
            ->findOrFail($agentId);

        // Calculate the number of leads assigned to the agent
        $leadsCount = $agent->leads->count();

        // Calculate the number of converted leads (closed leads)
        $leadsConverted = $agent->leads->where('status', 'closed')->count();

        // Total calls made
        $totalCalls = $agent->callLogs->count();

        // Calculate the average call duration
        $totalDuration = $agent->callLogs->sum('duration');
        $averageCallDuration = $totalCalls > 0 ? round($totalDuration / $totalCalls, 2) : 0;

        // Calculate average response time
        $totalResponseTime = 0;
        $leadCount = 0;
        foreach ($agent->callLogs as $callLog) {
            $lead = $callLog->lead;
            if ($lead && $callLog->call_time && $lead->created_at) {
                $callTime = Carbon::parse($callLog->call_time);
                $createdAt = Carbon::parse($lead->created_at);
                $responseTime = $createdAt->diffInHours($callTime);
                $totalResponseTime += $responseTime;
                $leadCount++;
            }
        }
        $averageResponseTime = $leadCount > 0 ? round($totalResponseTime / $leadCount, 2) : 0;

        // Calculate the average time to close a lead
        $totalTimeToClose = 0;
        $closedLeadCount = 0;
        foreach ($agent->leads as $lead) {
            if ($lead->status === 'closed') {
                $timeToClose = $lead->created_at->diffInDays($lead->updated_at);
                $totalTimeToClose += $timeToClose;
                $closedLeadCount++;
            }
        }
        $averageTimeToClose = $closedLeadCount > 0 ? round($totalTimeToClose / $closedLeadCount, 2) : 0;

        // Total Deal Amount Closed by the Agent
        $totalDealAmount = $agent->leads->where('status', 'closed')->sum('rate');

        // Total Balance Amount (sum of balance for the agent's leads)
        $totalBalance = $agent->leads->sum('balance');

        // Total Revenue Generated by the Agent
        $totalRevenue = $totalDealAmount - $totalBalance;

        return view('agents.details', compact(
            'agent',
            'leadsCount',
            'leadsConverted',
            'totalCalls',
            'averageCallDuration',
            'averageResponseTime',
            'averageTimeToClose',
            'totalDealAmount',
            'totalRevenue'
        ));
    }
}
