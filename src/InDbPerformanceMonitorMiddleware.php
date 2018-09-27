<?php

namespace ASamir\InDbPerformanceMonitor;

use Closure;
use \ASamir\InDbPerformanceMonitor\LogRequests;

class InDbPerformanceMonitorMiddleware {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        // Log the request in case it is valid
        if (LogRequests::isValidPathToLog($request->getPathInfo()))
            LogRequests::startInDbMonitor();

        return $next($request);
    }

    /**
     * Runs before the request finish
     * @param type $request
     * @param type $response
     */
    public function terminate($request, $response) {
        //
        if ($request->input('__asamir_request_id')) {
            $session_data = session()->all();
            unset($session_data['__asamir_token']);
            if ($session_data && config('inDbPerformanceMonitor.IN_DB_MONITOR_NEGLICT_SESSION_DATA') == true)
                $session_data = ['%__ALL_HIDDEN__%'];

            LogRequests::find($request->input('__asamir_request_id'))->update([
                'session_id' => session()->getId(),
                'session_data' => serialize($session_data),
                'exec_time' => (microtime(true) - LARAVEL_START),
                'route_uri' => ((\Route::current()) ? \Route::current()->uri() : ''),
                'route_static_prefix' => ((\Route::current()) ? \Route::current()->getCompiled()->getStaticPrefix() : ''),
                'is_json_response' => ((json_decode($response->getContent()) != null) ? 1 : 0)
            ]);
        }
    }

}
