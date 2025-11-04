<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgentLocation;
use App\Models\Agent;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class AdminLocationController extends Controller
{
    public function getLatestLocations()
    {
        $agents = Agent::whereHas('latestLocation')->with('latestLocation')->get();

        $data = $agents->map(function ($a) {
            $location = $a->latestLocation;

            if (!$location) {
                return [
                    'agent_id' => $a->id,
                    'agent_name' => $a->name,
                    'agent_phone' => $a->phone_no ?? '-',
                    'latitude' => null,
                    'longitude' => null,
                    'location_time' => null,
                    'status' => 'Inactive'
                ];
            }

            try {
                $lat = Crypt::decryptString($location->latitude);
                $lng = Crypt::decryptString($location->longitude);
            } catch (\Exception $e) {
                $lat = $lng = null;
            }

            // Determine if agent is active (within 2 minutes)
            $isActive = Carbon::parse($location->location_time)->gt(Carbon::now()->subMinutes(2));

            return [
                'agent_id' => $a->id,
                'agent_name' => $a->name,
                'agent_phone' => $a->phone_no ?? '-',
                'latitude' => $lat,
                'longitude' => $lng,
                'location_time' => $location->location_time,
                'status' => $isActive ? 'Active' : 'Inactive',
            ];
        });

        return response()->json($data);
    }

    public function getAgentLocation($agentId)
    {
        $location = AgentLocation::where('agent_id', $agentId)
            ->latest('location_time')
            ->first();

        if (!$location) return response()->json([], 404);

        try {
            $lat = Crypt::decryptString($location->latitude);
            $lng = Crypt::decryptString($location->longitude);
        } catch (\Exception $e) {
            $lat = $lng = null;
        }

        $isActive = Carbon::parse($location->location_time)->gt(Carbon::now()->subMinutes(2));

        return response()->json([
            'agent_id' => $location->agent_id,
            'latitude' => $lat,
            'longitude' => $lng,
            'location_time' => $location->location_time,
            'tracking_status' => $location->tracking_status,
        ]);
    }
}
