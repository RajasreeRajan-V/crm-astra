<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AgentAuth;
use Illuminate\Support\Facades\DB;

class AgentLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // Agents must be authenticated as agents (adjust guard as required)
    public function store(Request $request)
    {
        // Validate input
        // dd($request->all());
        $data = $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'accuracy' => 'nullable',
            'location_time' => 'nullable'
        ]);
        // dd($data);
        // Use authenticated agent ID; fallback for testing

        $locationTime = isset($data['location_time'])
        ? Carbon::parse($data['location_time'])->format('Y-m-d H:i:s')
        : Carbon::now()->format('Y-m-d H:i:s');
        $agentId = session('agent_id');

    $location = AgentLocation::create([
        'agent_id' => $agentId,
        'latitude' => $data['latitude'],
        'longitude' => $data['longitude'],
        'accuracy' => $data['accuracy'] ?? null,
        'location_time' => $locationTime,
        'user_agent' => $request->header('User-Agent'),
        'ip' => $request->ip(),
    ]);

    return response()->json(['success' => true, 'location' => $location], 201);
} 

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function latestAll()
    {
        // latest location per agent
        $sub = DB::table('agent_locations')
            ->selectRaw('agent_id, MAX(id) as max_id')
            ->groupBy('agent_id');

        $latest = DB::table('agent_locations as al')
            ->joinSub($sub, 't', function ($join) {
                $join->on('al.id', '=', 't.max_id');
            })
            ->join('agents as a', 'al.agent_id', '=', 'a.id')
            ->select(
                'al.*',
                'a.name as agent_name',
                'a.phone as agent_phone'
            )
            ->get();

        return response()->json($latest);
    }

    public function history($agentId)
    {
        $rows = AgentLocation::where('agent_id', $agentId)
            ->orderBy('location_time', 'desc')
            ->limit(500)
            ->get();

        return response()->json($rows);
    }
}
