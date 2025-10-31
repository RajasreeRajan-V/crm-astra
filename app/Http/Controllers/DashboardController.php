<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Agent;
use App\Models\CallLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = Auth::user()->company_id;
        // Lead statuses count
        $leadStatusCounts = Lead::where('company_id', $companyId)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        // Call success rate - percentage of calls resulting in a positive outcome
        $totalCalls = CallLog::where('company_id', $companyId)->count();
        $successfulCalls = CallLog::where('company_id', $companyId)
            ->whereIn('outcome', ['interested', 'closed'])
            ->count();
        $callSuccessRate = $totalCalls > 0 ? ($successfulCalls / $totalCalls) * 100 : 0;

        // Summaries - e.g., total leads, total agents, and total calls
        $totalLeads = Lead::where('company_id', $companyId)->count();
        $conversionRate = ($totalLeads > 0) ? (Lead::where('company_id', $companyId)->where('status', 'closed')
            ->count() / $totalLeads) * 100 : 0;
        $totalAgents = Agent::where('company_id', $companyId)->count();
        $totalCallLogs = CallLog::where('company_id', $companyId)->count();

        $leadSourceBreakdown = Lead::where('company_id', $companyId)
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->pluck('count', 'source');

        $pendingFollowupsCount = Lead::where('company_id', $companyId)
            ->where('status', 'follow-up')
            ->count();

        // Total Deal from closed leads
        $totalDealAmount = Lead::where('company_id', $companyId)
            ->where('status', 'closed')
            ->sum('rate');

        // Total Balance (sum of balance of all leads)
        $totalBalance = Lead::where('company_id', $companyId)
        ->where('status', 'closed')
        ->sum('balance');

        // Total Revenue = Total Deal Amount - Total Balance
        $totalRevenue = $totalDealAmount - $totalBalance;

        // Pass data to the view
        return view('dashboard.index', compact(
            'leadStatusCounts',
            'callSuccessRate',
            'totalLeads',
            'totalAgents',
            'totalCallLogs',
            'conversionRate',
            'leadSourceBreakdown',
            'pendingFollowupsCount',
            'totalDealAmount',
            'totalBalance',
            'totalRevenue',
        ));
    }

    public function leadsByStatus($status)
    {
        $companyId = Auth::user()->company_id;
        // Retrieve leads with the specified status
        $leads = Lead::where('company_id', $companyId)
            ->where('status', $status)
            ->get();

        // Pass data to the view
        return view('dashboard.leads_by_status', compact('leads', 'status'));
    }
}
