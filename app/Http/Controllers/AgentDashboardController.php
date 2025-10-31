<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\CallLog;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\BalanceUpdateLog;

class AgentDashboardController extends Controller
{
    public function index()
    {
        // Get the authenticated agent's data
        $agent = auth()->guard('agent')->user();
        $agentId = $agent->id;  // You can use $agent->id as the agent is already authenticated

        // Calculate performance metrics for the agent
        $totalLeads = $agent->leads->count();  // Assuming 'leads' is a relationship defined in the Agent model
        $convertedLeads = $agent->leads->where('status', 'closed')->count();
        $conversionRate = $totalLeads ? number_format(($convertedLeads / $totalLeads) * 100, 2) : 0;

        $totalCalls = $agent->callLogs->count();  // Assuming 'callLogs' is a relationship defined in the Agent model
        $successfulCalls = $agent->callLogs->whereIn('outcome', ['interested', 'closed'])->count();
        $callSuccessRate = $totalCalls ? number_format(($successfulCalls / $totalCalls) * 100, 2) : 0;

        // Latest 5 call logs for the agent
        $callLogs = CallLog::where('agent_id', $agentId)
            ->latest()
            ->limit(5)
            ->get();

        // Upcoming follow-ups for the agent (leads with follow-up date greater than or equal to today)
        $followUps = Lead::where('assigned_agent_id', $agentId)
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '>=', Carbon::now())
            ->orderBy('follow_up_date', 'asc')
            ->get();

        // All leads assigned to the agent
        $assignedLeads = Lead::where('assigned_agent_id', $agentId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Pass all the data to the view
        return view('agentlogin.dashboard', compact(
            'totalLeads',
            'convertedLeads',
            'conversionRate',
            'totalCalls',
            'callSuccessRate',
            'callLogs',
            'followUps',
            'assignedLeads'
        ));
    }

    public function leads(Request $request)
    {
        $query = Lead::where('assigned_agent_id', auth('agent')->id());

        // Search by lead name
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Search by name if provided
        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date') && !empty($request->start_date) && !empty($request->end_date)) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Get leads in descending order (newest first)
        $leads = $query->orderBy('created_at', 'desc')->get();

        $statuses = ['new', 'contacted', 'interested', 'followup', 'closed'];

        return view('agentlogin.leads', compact('leads', 'statuses'));
    }


    public function edit()
    {
        $agent = auth('agent')->user(); // Get the logged-in agent
        return view('agentlogin.editprofile', compact('agent'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email,' . auth('agent')->id(),
            // 'phone_no' => 'required|numeric|digits_between:10,12|unique:agents,phone_no,' . auth('agent')->id(),
            'phone_no' => [
            'required',
            'regex:/^[0-9]{10,12}$/',
            'unique:agents,phone_no,' . auth('agent')->id() . ',id',
            ],

            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $agent = auth('agent')->user();
        $agent->name = $request->name;
        $agent->email = $request->email;
        $agent->phone_no = $request->phone_no;


        if ($request->password) {
            $agent->password = Hash::make($request->password);
        }

        $agent->save();

        return redirect()->route('agent.profile')->with('success', 'Profile updated successfully.');
    }

    public function details($id)
    {
        $lead = Lead::where('assigned_agent_id', auth('agent')->id())->findOrFail($id);

        $callLogs = CallLog::where('lead_id', $id)->get();

        // Fetch balance update logs for the lead
        $balanceUpdateLogs = BalanceUpdateLog::where('lead_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('agentlogin.details', compact('lead', 'callLogs', 'balanceUpdateLogs'));
    }


    public function editLead($id)
    {
        $lead = Lead::where('assigned_agent_id', auth('agent')->id())->findOrFail($id);
        return view('agent.edit-lead', compact('lead'));
    }

    public function updateLead(Request $request, $id)
    {
        $lead = Lead::where('assigned_agent_id', auth('agent')->id())->findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $lead->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('agent.lead.details', $lead->id)->with('success', 'Lead updated successfully.');
    }

    public function callindex(Request $request)
    {
        $agentId = auth('agent')->id();

        // Fetch query parameters for filtering
        $leadName = $request->input('lead_name');  // Change from lead_id to lead_name
        $callDate = $request->input('call_date');

        // Build the query for call logs
        $query = CallLog::with('lead')->where('agent_id', $agentId);

        if (!empty($leadName)) {
            $query->whereHas('lead', function ($q) use ($leadName) {
                $q->where('name', 'like', '%' . $leadName . '%'); // Search by lead name
            });
        }

        if (!empty($callDate)) {
            $query->whereDate('call_time', $callDate);
        }

        $callLogs = $query->orderBy('call_time', 'desc')->get();

        return view('agentlogin.callindex', compact('callLogs', 'leadName', 'callDate'));
    }



    public function show()
    {
        $agent = auth('agent')->user();
        return view('agentlogin.profile', compact('agent'));
    }
}
