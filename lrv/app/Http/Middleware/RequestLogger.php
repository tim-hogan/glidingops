<?php
// app/Http/Middleware/RequestLogger.php

namespace App\Http\Middleware;

use Closure;

class RequestLogger
{
    public function handle( $request, Closure $next )
    {
        \Log::debug( 'LOGGING REQUEST', [ $request ] );

        $response = $next( $request );

        \Log::debug( 'LOGGING RESPONSE', [ $response ] );

        return $response;
    }
}