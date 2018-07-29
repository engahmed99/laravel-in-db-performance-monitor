<?php

namespace ASamir\InDbPerformanceMonitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ASamir\InDbPerformanceMonitor\LogRequests;
use ASamir\InDbPerformanceMonitor\LogQueries;
use ASamir\InDbPerformanceMonitor\LogErrors;

class InDbPerformanceMonitorController extends Controller {

    public function isAuthenticated() {
        return \Hash::check(session()->getId(), session('__asamir_token'));
    }

    public function __construct() {
        $this->middleware(function ($request, $next) {
            if (!$this->isAuthenticated())
                return redirect('admin-monitor');
            return $next($request);
        })->except('index');
    }

    public function index(Request $request) {
        if ($this->isAuthenticated())
            return redirect('admin-monitor/requests');
        if ($request->isMethod('POST')) {
            if (\Hash::check($request->get('password'), config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN'))) {
                session()->put('__asamir_token', bcrypt(session()->getId()));
                return redirect('admin-monitor/requests');
            }
            return redirect('admin-monitor')->with('alert-danger', 'Passsowrd is Not correct');
        }
        return view('inDbPerformanceMonitor::index');
    }

    public function logout(Request $request) {
        session()->remove('__asamir_token');
        return redirect('admin-monitor')->with('alert-success', 'You are logged out from admin monitor');
    }

    public function changePassword(Request $request) {
        if ($request->isMethod('POST')) {
            if (\Hash::check($request->get('password'), config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN'))) {
                if (strlen($request->get('new_password')) < 6 || $request->get('new_password') != $request->get('new_password_confirmed'))
                    return redirect('admin-monitor/changePassword')->with('alert-danger', 'New Passsowrd must be at least 6 digits and confirmed');
                // Set new passowrd
                $content = file_get_contents(config_path('inDbPerformanceMonitor.php'));
                $content = str_replace(config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN'), bcrypt($request->get('new_password')), $content);
                file_put_contents(config_path('inDbPerformanceMonitor.php'), $content);
                return redirect('admin-monitor/requests')->with('alert-success', 'Passsowrd changed successfully');
            }
            return redirect('admin-monitor/changePassword')->with('alert-danger', 'Passsowrd is Not correct');
        }
        return view('inDbPerformanceMonitor::changePassword');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getRequests(Request $request) {
        $query = LogRequests::query();
        $search = $request->get('search');
        $search_type = $request->get('search_type');
        $order_by = $request->get('order_by', 'created_at');
        $order_type = $request->get('order_type', 'desc');
        $has_errors = $request->get('has_errors');
        //
        if ($search) {
            if (in_array($search_type, ['%...', '!%...']))
                $search = '%' . $search;
            else if (in_array($search_type, ['...%', '!...%']))
                $search = $search . '%';
            else if (in_array($search_type, ['%...%', '!%...%']))
                $search = '%' . $search . '%';
            //
            if (in_array($search_type, ['%...%', '%...', '...%']))
                $search_type = 'like';
            else if (in_array($search_type, ['!%...%', '!%...', '!...%']))
                $search_type = 'not like';

            // Search using the keyword
            $query->where(function($q) use($search_type, $search, $has_errors) {
                if (in_array($search_type, ['not like', '!='])) {
                    $q->where('action', $search_type, $search)
                            ->where('route_uri', $search_type, $search)
                            ->where('route_static_prefix', $search_type, $search)
                            ->where('url', $search_type, $search)
                            ->where('type', $search_type, $search)
                            ->where('session_id', $search_type, $search)
                            ->where('ip', $search_type, $search)
                            ->where('archive_tag', $search_type, $search);
                    if ($has_errors)
                        $q->whereHas('error', function($qq) use($search_type, $search) {
                            $qq->where('message', $search_type, $search)
                                    ->where('file', $search_type, $search);
                        });
                } else {
                    $q->where('action', $search_type, $search)
                            ->orWhere('route_uri', $search_type, $search)
                            ->orWhere('route_static_prefix', $search_type, $search)
                            ->orWhere('url', $search_type, $search)
                            ->orWhere('type', $search_type, $search)
                            ->orWhere('session_id', $search_type, $search)
                            ->orWhere('ip', $search_type, $search)
                            ->orWhere('archive_tag', $search_type, $search);
                    if ($has_errors)
                        $q->orWhereHas('error', function($qq) use($search_type, $search) {
                            $qq->where('message', $search_type, $search)
                                    ->orWhere('file', $search_type, $search);
                        });
                }
            });
        }

        if ($request->get('queries_count') && is_numeric($request->get('queries_count')))
            $query->where('queries_total_count', '>=', $request->get('queries_count'));
        if ($request->get('has_errors'))
            $query->where('has_errors', '=', 1);
        if ($request->get('not_archived'))
            $query->where('archive_tag', '=', 0);
        if ($request->get('has_not_elequent'))
            $query->where('queries_not_elequent_count', '>', 0);
        if ($request->get('is_json_response'))
            $query->where('is_json_response', '=', 1);
        if ($request->get('from_date'))
            $query->where('created_at', '>=', $request->get('from_date'));
        if ($request->get('to_date'))
            $query->where('created_at', '<', date('Y-m-d', strtotime($request->get('to_date') . "+1 days")));

        $requests = $query->with('error')->orderBy($order_by, $order_type)->paginate();

        return view('inDbPerformanceMonitor::getRequests', compact('requests'));
    }

    public function showRequest(Request $request, $id) {
        $is_last_of_mine = 0;
        if ($id == -1) {
            $id = LogRequests::where('session_id', session()->getId())->max('id');
            $is_last_of_mine = -1;
            if (!$id)
                return redirect('admin-monitor/requests')->with('alert-danger', 'There are no requests made by your current session ID.');
        }
        if ($id == -2) {
            $id = LogRequests::where('ip', request()->ip())->max('id');
            $is_last_of_mine = -2;
            if (!$id)
                return redirect('admin-monitor/requests')->with('alert-danger', 'There are no requests made by your current IP.');
        }
        $logRequest = LogRequests::findOrFail($id);

        $search = $request->get('search');
        $query = LogQueries::where('request_id', $id);
        if ($search)
            $query->where(function($q) use($search) {
                $q->where('query', 'like', '%' . $search . '%')
                        ->orWhere('connection_name', 'like', '%' . $search . '%');
            });
        if ($request->get('is_not_elequent'))
            $query->where('is_elequent', '=', 0);
        $order = ['id', 'asc'];
        if ($request->get('order'))
            $order = explode('.', $request->get('order'));
        $logQueries = $query->orderBy($order[0], $order[1])->paginate();

        $logError = LogErrors::where('request_id', $id)->first();
        return view('inDbPerformanceMonitor::showRequest', compact(['logRequest', 'logQueries', 'logError', 'is_last_of_mine']));
    }

    public function runQuery(Request $request, $id) {
        $logQuery = LogQueries::findOrFail($id);
        $results = null;
        $exception = null;
        $start_time = microtime(true);
        try {
            if (substr(strtolower($logQuery->query), 0, 7) == 'select ')
                $results = \DB::select(\DB::raw($logQuery->query), json_decode($logQuery->bindings, true));
            else
                $results = \DB::statement(\DB::raw($logQuery->query), json_decode($logQuery->bindings, true));
        } catch (\Exception $e) {
            $exception = $e;
        }
        $end_time = microtime(true);
        $exec_time = round($end_time - $start_time, 2);

        return view('inDbPerformanceMonitor::runQuery', compact(['logQuery', 'results', 'exception', 'exec_time']));
    }

    public function archiveRequests() {
        LogRequests::where('archive_tag', '0')->update([
            'archive_tag' => date('YmdHis')
        ]);
        return redirect('admin-monitor/requests')->with('alert-success', 'Requests archived successfully');
    }

    public function statisticsReport(Request $request) {
        $model = new LogRequests();
        $table_name = $model->getTable();
        $conn_name = $model->getConnectionName();
        $query = \DB::connection($conn_name)->table($table_name)
                ->select('route_uri', 'type',
                //
                \DB::raw('min(queries_total_time) min_queries_time'), \DB::raw('avg(queries_total_time) avg_queries_time'), \DB::raw('max(queries_total_time) max_queries_time'),
                //
                \DB::raw('min(queries_total_count) min_queries_count'), \DB::raw('max(queries_total_count) max_queries_count'),
                //
                \DB::raw('min(queries_not_elequent_count) min_not_elequent_queries_count'), \DB::raw('max(queries_not_elequent_count) max_not_elequent_queries_count'),
                //
                \DB::raw('min(exec_time) min_exec_time'), \DB::raw('avg(exec_time) avg_exec_time'), \DB::raw('max(exec_time) max_exec_time'),
                //
                \DB::raw('count(id) requests_count'), \DB::raw('sum(has_errors) with_errors_count'), \DB::raw('(count(id)-sum(has_errors)) with_no_errors_count'),
                //
                \DB::raw('max(is_json_response) is_json_response'), \DB::raw('max(has_errors) has_errors'), \DB::raw('max(id) last_id')
        );
        $search = $request->get('search');
        if ($search)
            $query->where(function($q) use($search) {
                $q->where('route_uri', 'like', '%' . $search . '%')
                        ->orWhere('session_id', 'like', '%' . $search . '%');
            });
        if ($request->get('type'))
            $query->where('type', '=', strtoupper($request->get('type')));
        if ($request->get('not_archived'))
            $query->where('archive_tag', '=', 0);
        if ($request->get('from_date'))
            $query->where('created_at', '>=', $request->get('from_date'));
        if ($request->get('to_date'))
            $query->where('created_at', '<', date('Y-m-d', strtotime($request->get('to_date') . "+1 days")));
        $statistics = $query->groupBy('route_uri', 'type')
                ->orderBy($request->get('order_by', 'max_queries_time'), $request->get('order_type', 'desc'))
                ->paginate();
        return view('inDbPerformanceMonitor::statisticsReport', compact('statistics'));
    }

    public function errorsReport(Request $request) {
        $model_r = new LogRequests();
        $table_name_r = $model_r->getTable();
        $model_e = new LogErrors();
        $table_name_e = $model_e->getTable();
        $conn_name = $model_r->getConnectionName();
        $query = \DB::connection($conn_name)
                ->table($table_name_e)
                ->join($table_name_r, $table_name_e . '.request_id', '=', $table_name_r . '.id')
                ->select('route_uri', 'type', 'message',
                //
                \DB::raw('count(*) errors_count'),
                //
                \DB::raw('max(is_json_response) is_json_response'), \DB::raw('max(has_errors) has_errors'), \DB::raw('max(request_id) last_id')
        );
        $search = $request->get('search');
        if ($search)
            $query->where(function($q) use($search) {
                $q->where('route_uri', 'like', '%' . $search . '%')
                        ->orWhere('session_id', 'like', '%' . $search . '%')
                        ->orWhere('message', 'like', '%' . $search . '%')
                        ->orWhere('file', 'like', '%' . $search . '%');
            });
        if ($request->get('type'))
            $query->where('type', '=', strtoupper($request->get('type')));
        if ($request->get('not_archived'))
            $query->where('archive_tag', '=', 0);
        if ($request->get('from_date'))
            $query->where($table_name_r . '.created_at', '>=', $request->get('from_date'));
        if ($request->get('to_date'))
            $query->where($table_name_r . '.created_at', '<', date('Y-m-d', strtotime($request->get('to_date') . "+1 days")));
        $errors_stats = $query->groupBy('route_uri', 'type', 'message')
                ->orderBy($request->get('order_by', 'errors_count'), $request->get('order_type', 'desc'))
                ->paginate();
        return view('inDbPerformanceMonitor::errorsReport', compact('errors_stats'));
    }

}
