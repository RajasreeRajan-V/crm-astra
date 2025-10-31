<?php

namespace App\Http\Controllers;

use App\Models\BalanceUpdateLog;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class TransactionController extends Controller
{
    public function index(Request $request)
{
    $query = BalanceUpdateLog::with('lead', 'updatedBy')
        ->where('updated_by', auth('agent')->id()); // Filter transactions by logged-in agent

    // Apply search filter if provided
    if ($request->has('search') && $request->search != '') {
        $query->whereHas('lead', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('deal_item', 'like', '%' . $request->search . '%'); // Search deal_item
        });
    }

    // Filter by Start Date
    if ($request->has('start_date') && !empty($request->start_date)) {
        $query->whereDate('created_at', '>=', $request->start_date);
    }

    // Filter by End Date
    if ($request->has('end_date') && !empty($request->end_date)) {
        $query->whereDate('created_at', '<=', $request->end_date);
    }

    $transactions = $query->orderBy('created_at', 'desc')->paginate(10);

    return view('agentlogin.transactions.index', compact('transactions'));
}

    // Show the create transaction form
    public function create()
    {
    $leads = Lead::where('assigned_agent_id', auth('agent')->id()) // Filter leads by logged-in agent
        ->where('status', 'closed') // Only include leads with status = 'closed'
        ->orderBy('name', 'asc') // Order by name in ascending order
        ->get();

    return view('agentlogin.transactions.create', compact('leads'));
    }

    // Store a new transaction
    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        // Update lead balance and log transaction
        $previousBalance = $lead->balance;
        $newBalance = $previousBalance - $request->amount;

        $lead->update(['balance' => $newBalance]);

        $companyId = Auth::guard('agent')->user()->company_id;

        BalanceUpdateLog::create([
            'lead_id' => $lead->id,
            'field_updated' => 'balance',
            'previous_value' => $previousBalance,
            'new_value' => $newBalance,
            'updated_by' => auth('agent')->id(),
            'updated_at' => now(),
            'notes' => $request->notes,
            'company_id' => $companyId,
        ]);

        return redirect()->route('agent.transactions')->with('success', 'Transaction recorded successfully.');
    }

    public function getLeadBalance($id)
    {
        $lead = Lead::where('assigned_agent_id', auth('agent')->id())
        ->where('id', $id)
        ->first();

        if (!$lead) {
        return response()->json(['error' => 'Lead not found'], 404);
        }

        return response()->json(['balance' => $lead->balance]);
    }

    public function AdminIndex(Request $request)
    {
        $admin = Auth::user(); // Admin user
        // Fetch all transactions with related data (leads & agents)
        $query = BalanceUpdateLog::with(['lead', 'updatedBy'])
        ->whereHas('lead', function ($q) use ($admin) {
            $q->where('company_id', $admin->company_id);
        });
        // Apply search filters (Lead Name or Deal Item)
        if ($request->has('search') && !empty($request->search)) {
            $query->whereHas('lead', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('deal_item', 'like', '%' . $request->search . '%');
            });
        }
    
        // Filter by Agent
        if ($request->has('agent_id') && !empty($request->agent_id)) {
            $query->where('updated_by', $request->agent_id);
        }
    
        // Filter by Date Range
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
    
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
    
        // Fetch transactions
        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
    
        // Get all agents for filtering dropdown
        $agents = \App\Models\Agent::where('company_id', $admin->company_id)->get();
    
        return view('agents.transactions', compact('transactions', 'agents'));
    }    

    public function edit($id)
    {
        // Get the transaction to edit
        $transaction = BalanceUpdateLog::with('lead')
            ->where('id', $id)
            ->firstOrFail();  // Fetch the transaction first
    
        // Return the edit view with the transaction data
        return view('agentlogin.transactions.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $transaction = BalanceUpdateLog::findOrFail($id);
        $lead = $transaction->lead;
        $balance = $transaction->previous_value;
        $request->validate([
            'amount' => ['required','numeric','min:0',
            function ($attribute, $value, $fail) use ($balance) {
                if ($value > $balance) {
                $fail('The amount cannot be greater than the current balance.');
                 }
               },
        ],
            'notes' => 'nullable|string|max:500',
            ]);


    // Get the lead and update the balance
        $previousBalance = $transaction->previous_value;
        $newAmount = $request->amount;

    // Recalculate new balance
        $newBalance = $previousBalance - $newAmount; // Adjust balance based on the change

        $lead->update(['balance' => $newBalance]);

    // Update the transaction log
        $transaction->update([
            'new_value' => $newBalance,
            'notes' => $request->notes,
            'updated_at' => now(),
        ]);

     return redirect()->route('agent.transactions')->with('success', 'Transaction updated successfully.');
    }

}
