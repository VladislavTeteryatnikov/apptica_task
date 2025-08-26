<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        $response = $next($request);
        $duration = round(microtime(true) - $startTime, 3);

        Log::channel('api_requests')->info('API Request LOG', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status_code' => $response->status(),
            'input_date' => $request->query('date'),
            'duration_ms' => $duration * 1000,
            'timestamp' => now()->toISOString(),
        ]);

        return $response;
    }
}
