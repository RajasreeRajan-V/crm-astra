<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\BalanceUpdateLog;
use Illuminate\Support\Facades\Auth;

class AdminTransactionController extends Controller
{
    public function create()
    {
        $admin = Auth::user(); // Admin user
        $companyId = $admin->company_id;
        $closedLeads = Lead::where('company_id', $companyId)
        ->where('status', 'Closed')
        ->orderBy('name', 'asc')
        ->get(); // Fetch all leads with "Closed" status
        return view('agents.add_transaction', compact('closedLeads'));
    }
    
    public function getLeadBalance($id)
    {
        $admin = Auth::user();
        $companyId = $admin->company_id;
        $lead = Lead::where('id', $id)
            ->where('company_id', $companyId)
            ->first();
    
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
    
        return response()->json(['balance' => $lead->balance]);
    }

    public function store(Request $request)
    {
        $companyId = Auth::user()->company_id;
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        if ($request->amount > $lead->balance) {
            return back()->withErrors(['amount' => 'The entered amount cannot exceed the current balance.'])->withInput();
        }

        // Update lead balance and log transaction
        $previousBalance = $lead->balance;
        $newBalance = $previousBalance - $request->amount;

        $lead->update(['balance' => $newBalance]);

        BalanceUpdateLog::create([
            'lead_id' => $lead->id,
            'field_updated' => 'balance',
            'previous_value' => $previousBalance,
            'new_value' => $newBalance,
            'updated_by' => $lead->assigned_agent_id,
            'updated_at' => now(),
            'notes' => $request->notes,
            'company_id' => $companyId,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }
}
