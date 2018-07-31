<?php

namespace ASamir\InDbPerformanceMonitor;

use Closure;

class InDbPerformanceMonitorSecurityMiddleware {

    /**
     * Check if you authorized to show this request
     * @return bool
     */
    public function isAuthenticated() {
        return \Hash::check(session()->getId(), session('__asamir_token'));
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (!config('inDbPerformanceMonitor.IN_DB_MONITOR_PANEL'))
            abort(404);
        if (!$this->isAuthenticated()) {
            session()->remove('__asamir_token');
            return redirect('admin-monitor');
        }

        return $next($request);
    }

}
