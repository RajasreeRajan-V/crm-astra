<?php

use App\Models\Lead;
use App\Models\Agent;
use App\Notifications\FollowUpNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;

// In the route closure, use the Schedule instance to set up your task
/*$schedule->call(function () {
    // Get today's date
    $today = Carbon::today()->toDateString();

    // Fetch leads with follow-up dates for today
    $leads = Lead::whereDate('follow_up_date', $today)->get();

    // Loop through each lead to send a notification to the assigned agent
    foreach ($leads as $lead) {
        $agent = $lead->agent; // Assumes `agent()` relation exists on the Lead model

        if ($agent) {
            // Send the notification to the agent
            Notification::send($agent, new FollowUpNotification([$lead]));
        }
    }

    // Log the scheduled task for verification
    Log::info("Follow-up notifications sent for leads with a follow-up date of today.");
})->daily();*/

/*$schedule->call(function () {
    app()->make(\App\Http\Controllers\AgentLeadController::class)->sendTodayFollowUpsEmails();
})->daily();*/