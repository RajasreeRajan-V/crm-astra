<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Agent;
use Illuminate\Http\Request;
use App\Http\Controllers\AgentController;
use App\Notifications\NewLeadNotification;
use App\Notifications\TransferLeadNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class LeadController extends Controller
{
    // Display all leads
    public function index(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $query = Lead::where('company_id', $companyId);

        // Filter by status if provided
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

        // Order by created_at in descending order (newest first)
        $leads = $query->orderBy('created_at', 'desc')->get();

        $statuses = ['new', 'contacted', 'interested', 'followup', 'closed'];

        return view('leads.index', compact('leads', 'statuses'));
    }


    // Show lead details
    public function show($id)
    {
        $lead = Lead::findOrFail($id); // Find lead by ID
        return view('leads.show', compact('lead')); // Return a view with lead details
    }

    public function edit($id)
    {
        $lead = Lead::findOrFail($id); // Find lead by ID
        $sources = [
            'email' => 'Email',
            'website' => 'Website',
            'social_media' => 'Social Media',
            'referral' => 'Referral',
            'advertisement' => 'Advertisement',
        ]; // Define sources for the dropdown
        $agents = Agent::where('company_id', Auth::user()->company_id)->get();

        return view('leads.edit', compact('lead', 'sources', 'agents')); // Return the edit view with lead data
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|numeric|digits_between:10,12',
            'source' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:leads,email,'. $id,
            'assigned_agent_id' => 'required|exists:agents,id',
            'deal_item' => 'nullable|string|max:255',
        ]);

        $lead = Lead::findOrFail($id);

        if ($lead->status !== 'closed') {
            $request->validate([
                'rate' => 'nullable|numeric|min:0',
            ]);
        }

        $previousAgentId = $lead->assigned_agent_id;
        $lead->update([
            'name' => $request->name,
            'contact' => $request->contact,
            'source' => $request->source,
            'email' => $request->email,
            'assigned_agent_id' => $request->assigned_agent_id,
            'deal_item' => $request->deal_item,
            'rate' => $lead->status !== 'closed' ? $request->rate : $lead->rate, // ✅ Fixed here
        ]);

        // Check if the assigned agent has changed
        if ($previousAgentId !== $lead->assigned_agent_id) {
            // Find the new agent
            $newAgent = Agent::find($lead->assigned_agent_id);

            // If the new agent exists, send them a notification
            if ($newAgent) {
                $newAgent->notify(new NewLeadNotification($lead));
            }
        }

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.'); // Redirect to leads index
    }

    // Add new leads
    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|numeric|digits_between:10,12',
            'source' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'assigned_agent_id' => 'nullable|exists:agents,id', // Optional agent ID
            'deal_item' => 'nullable|string|max:255', // New field
            'rate' => 'nullable|numeric|min:0',
            'referral_id' => 'nullable|exists:leads,id' // Validate referral ID
        ]);

        $leadData = $request->except('assigned_agent_id');
        $leadData['company_id'] = $companyId;
         if (!empty($request->rate)) {
        $leadData['balance'] = $request->rate;
    }
        $lead = Lead::create($leadData);

        // Check if an agent is manually assigned
        if ($request->has('assigned_agent_id') && $request->assigned_agent_id) {
            $lead->assigned_agent_id = $request->assigned_agent_id;
            $lead->save();

            // Notify the manually assigned agent
            $agent = Agent::find($request->assigned_agent_id);
            Notification::send($agent, new NewLeadNotification($lead));

            Log::info("Manually assigned Lead ID {$lead->id} to Agent ID {$agent->id}");
        } else {
            // Use the automatic assignment logic
            app(AgentController::class)->assignLeadToAgent($lead);
        }

        return redirect()->route('leads.index'); // Redirect to leads index
    }



    public function create()
    {
        $companyId = Auth::user()->company_id;
        $leads = Lead::where('company_id', $companyId)->orderBy('name', 'asc')->get();
        $agents = Agent::where('company_id', $companyId)->get();
        return view('leads.create', compact('agents', 'leads'));
    }

    public function transfer()
    {
        $companyId = Auth::user()->company_id;
        $leads = Lead::where('company_id', $companyId)->get();
        $agents = Agent::where('company_id', $companyId)->get();
        return view('leads.transfer', compact('leads', 'agents')); // Return the transfer view with leads and agents
    }

    // Store the transferred lead (assign a lead to an agent)
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'target_agent_id' => 'required|exists:agents,id|different:source_agent_id',
        ], [
            'target_agent_id.different' => 'Source agent and target agent cannot be the same.',
        ]);
        $leads = Lead::whereIn('id', $request->lead_ids)->get();
        // Update leads to the new agent
        Lead::whereIn('id', $request->lead_ids)->update(['assigned_agent_id' => $request->target_agent_id]);

        $targetAgent = Agent::findOrFail($request->target_agent_id);
        $targetAgent->notify(new TransferLeadNotification($leads));

        return redirect()->route('leads.index')->with('success', 'Selected leads successfully transferred.');
    }


    public function getLeadsByAgent($agent_id)
    {
        // Fetch leads assigned to the given agent ID
        $leads = Lead::where('assigned_agent_id', $agent_id)
            ->orderBy('name', 'asc')
            ->get(['id', 'name']);

        // Return as JSON response for the AJAX call
        return response()->json($leads);
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }

    public function updateStatusIndex(Request $request)
    {
        // Get status filter if set
        $companyId = Auth::user()->company_id;
        $statusFilter = $request->get('status');
        $searchTerm = $request->get('search_term');
        $leads = Lead::where('company_id', $companyId)->when($statusFilter, function ($query) use ($statusFilter) {
                return $query->where('status', $statusFilter);
            })
            ->when($searchTerm, function ($query) use ($searchTerm) {
                return $query->where('name', 'like', '%' . $searchTerm . '%');
            })
            ->orderBy('name', 'asc')
            ->get();

        $statuses = ['new', 'contacted', 'interested', 'followup', 'closed']; // Lead status options

        return view('leads.update-status-index', compact('leads', 'statuses', 'statusFilter'));
    }

    public function editStatus($id)
    {
        $lead = Lead::with('Agent')->findOrFail($id);
        $statuses = ['new', 'contacted', 'interested', 'follow-up', 'closed'];

        return view('leads.edit-status', compact('lead', 'statuses'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'new_status' => 'required|in:new,contacted,interested,follow-up,closed',
        ]);

        $lead = Lead::findOrFail($id);
        $lead->status = $request->new_status;
        $lead->save();

        return redirect()->route('leads.updateStatusIndex')->with('success', 'Lead status updated successfully.');
    }

    public function showUnassignedLeads()
    {
        $companyId = Auth::user()->company_id;
        // Fetch unassigned leads (leads without an assigned agent)
        $unassignedLeads = Lead::where('company_id', $companyId)
            ->whereNull('assigned_agent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Fetch all agents
        $agents = Agent::where('company_id', $companyId)->get();

        // Return the unassigned leads view
        return view('leads.unassigned', compact('unassignedLeads', 'agents'));
    }


    public function transferUnassignedLeads(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array|min:1', // Ensures at least one lead is selected
            'target_agent_id' => 'required|exists:agents,id',
        ], [
            'lead_ids.required' => 'No leads selected.', // Custom error message
            'lead_ids.array' => 'Invalid selection of leads.',
            'lead_ids.min' => 'No leads selected.', // Additional custom error message for empty selection
            'target_agent_id.required' => 'Please select an agent to transfer the leads.',
            'target_agent_id.exists' => 'The selected agent is invalid.',
        ]);

        DB::beginTransaction();
        try {
            // Update unassigned leads with the selected agent
            Lead::whereIn('id', $request->lead_ids)
                ->whereNull('assigned_agent_id') // Ensure they are unassigned
                ->update(['assigned_agent_id' => $request->target_agent_id]);

            // Notify the agent about the transfer
            $leads = Lead::whereIn('id', $request->lead_ids)->get();
            $targetAgent = Agent::findOrFail($request->target_agent_id);
            $targetAgent->notify(new TransferLeadNotification($leads));

            DB::commit();

            return redirect()->route('leads.unassigned')->with('success', 'Unassigned leads successfully transferred.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to transfer unassigned leads. Please try again.');
        }
    }
}
