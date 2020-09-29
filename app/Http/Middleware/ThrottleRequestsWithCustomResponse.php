<?php

namespace App\Http\Middleware;
use Closure;

// Custom middleware for 429 Too Many Attempt JSON Response
class ThrottleRequestsWithCustomResponse extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    protected function handleRequest($request, Closure $next, array $limits)
    {
        foreach ($limits as $limit) {
            if ($this->limiter->tooManyAttempts($limit->key, $limit->maxAttempts)) {
                return response()->json(['message' => 'Too many login attempts. Please try again after in 5 minutes'], 429);
            }

            $this->limiter->hit($limit->key, $limit->decayMinutes * 60);
        }

        $response = $next($request);

        foreach ($limits as $limit) {
            $response = $this->addHeaders(
                $response,
                $limit->maxAttempts,
                $this->calculateRemainingAttempts($limit->key, $limit->maxAttempts)
            );
        }

        return $response;
    }
}