<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentLocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\AgentAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

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
        'latitude' => 'nullable',
        'longitude' => 'nullable',
        'accuracy' => 'nullable',
        'location_time' => 'nullable',
        'tracking_status' => 'nullable|string'
    ]);

    $agentId = session('agent_id'); // or Auth::guard('agent')->id()
    $status = $request->input('tracking_status') ?? 'inactive';

    Log::info('Incoming Location', [
    'agent_id' => $agentId,
    'data' => $request->all()
    ]);

    // Get last latest location, even if 'is_latest' flag wasn’t set properly
    $lastLatest = AgentLocation::where('agent_id', $agentId)
        ->orderByDesc('location_time')
        ->first();

    //  Use new coordinates if available; otherwise reuse old ones
    if (isset($data['latitude']) && isset($data['longitude']) && $data['latitude'] !== null && $data['longitude'] !== null) {
        $encryptedLatitude = Crypt::encryptString($data['latitude']);
        $encryptedLongitude = Crypt::encryptString($data['longitude']);
    } elseif ($lastLatest) {
        $encryptedLatitude = $lastLatest->latitude;
        $encryptedLongitude = $lastLatest->longitude;
    } else {
        $encryptedLatitude = null;
        $encryptedLongitude = null;
    }

    $encryptedUserAgent = Crypt::encryptString($request->header('User-Agent') ?? 'unknown');
    $encryptedIp = Crypt::encryptString($request->ip() ?? 'unknown');

    // Always record full history
    AgentLocation::create([
        'agent_id' => $agentId,
        'latitude' => $encryptedLatitude,
        'longitude' => $encryptedLongitude,
        'accuracy' => $data['accuracy'] ?? null,
        'location_time' => now(),
        'user_agent' => $encryptedUserAgent,
        'ip' => $encryptedIp,
        'is_latest' => false,
        'tracking_status' => $status,
    ]);

    // Update latest location separately
    AgentLocation::updateOrCreate(
        ['agent_id' => $agentId],
        [
            'latitude' => $encryptedLatitude,
            'longitude' => $encryptedLongitude,
            'accuracy' => $data['accuracy'] ?? null,
            'location_time' => now(),
            'user_agent' => $encryptedUserAgent,
            'ip' => $encryptedIp,
            'tracking_status' => $status,
            'is_latest' => true,
        ]
    );

    return response()->json(['success' => true, 'status' => $status]);
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
            $item->ip = Crypt::decryptString($item->ip); 
        } catch (\Exception $e) {
            $item->latitude = 0;
            $item->longitude = 0;
            $item->ip = 'unknown';
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
