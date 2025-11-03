<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AgentAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
class AgentLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       return view('agents.track_agent');
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
    $data = $request->validate([
        'latitude' => 'required',
        'longitude' => 'required',
        'accuracy' => 'nullable',
        'location_time' => 'nullable'
    ]);

    $agentId = session('agent_id'); // or Auth::guard('agent')->id();

     AgentLocation::create([
        'agent_id' => $agentId,
        'latitude' => Crypt::encryptString($data['latitude']),
        'longitude' => Crypt::encryptString($data['longitude']),
        'accuracy' => $data['accuracy'] ?? null,
        'location_time' => now(),
        'user_agent' => Crypt::encryptString($request->header('User-Agent') ?? 'unknown'),
        'ip' => $request->ip(),
    ]);
    AgentLocation::updateOrCreate(
        ['agent_id' => $agentId, 'is_latest' => true], // Add a new column: `is_latest` boolean
        [
            'latitude' => Crypt::encryptString($data['latitude']),
            'longitude' => Crypt::encryptString($data['longitude']),
            'accuracy' => $data['accuracy'] ?? null,
            'location_time' => now(),
            'user_agent' => Crypt::encryptString($request->header('User-Agent') ?? 'unknown'),
            'ip' => $request->ip(),
        ]
    );

    return response()->json(['success' => true]);
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
                'a.phone_no as agent_phone'
            )
            ->get();

        $latest = $latest->map(function ($item) {
            try {
                $item->latitude = Crypt::decryptString($item->latitude);
                $item->longitude = Crypt::decryptString($item->longitude);
            } catch (\Exception $e) {
                // If decryption fails, fallback to 0
                $item->latitude = 0;
                $item->longitude = 0;
            }
            return $item;
        });

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
