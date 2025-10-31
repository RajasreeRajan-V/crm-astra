<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'agent.auth' => \App\Http\Middleware\AgentAuth::class, // Register agent auth middleware
    ];
}
