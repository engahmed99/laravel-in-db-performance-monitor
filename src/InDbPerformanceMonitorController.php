<?php

namespace ASamir\InDbPerformanceMonitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ASamir\InDbPerformanceMonitor\LogRequests;
use ASamir\InDbPerformanceMonitor\LogQueries;
use ASamir\InDbPerformanceMonitor\LogErrors;
use ASamir\InDbPerformanceMonitor\LogIPs;

class InDbPerformanceMonitorController extends Controller {

    /**
     * Check if you authorized to show this request
     * @return bool
     */
    public function isAuthenticated() {
        return \Hash::check(session()->getId(), session('__asamir_token'));
    }

    /**
     * Attach authorization check middleware
     */
    public function __construct() {
        // Get laravel version
        $version = substr(app()->version(), 0, 3);
        if (in_array($version, ['5.0', '5.1']))
            \View::share('app_version_less_2', true);
        else
            \View::share('app_version_less_2', false);
        \View::share('app_version', substr(app()->version(), 0, 3));

        // Add security middleware
        $this->middleware('\ASamir\InDbPerformanceMonitor\InDbPerformanceMonitorSecurityMiddleware', ['except' => ['index']]);
    }

    /**
     * Handles Login page
     * and create admin monitor login token
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        if ($this->isAuthenticated())
            return redirect('admin-monitor/dashboard');
        if ($request->isMethod('POST')) {
            // Authenticated
            if (\Hash::check($request->get('password'), config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN'))) {
                session()->put('__asamir_token', bcrypt(session()->getId()));
                return redirect('admin-monitor/dashboard');
            }
            // Return with error
            return redirect('admin-monitor')->with('alert-danger', 'Passsowrd is Not correct');
        }
        // Render login view GET
        return view('inDbPerformanceMonitor::index');
    }

    /**
     * Display dashboard
     * @param Request $request
     */
    public function dashboard(Request $request) {
        $model = new LogRequests();
        $table_name = $model->getTable();
        $conn_name = $model->getConnectionName();

        // Get all archives
        $archives = \DB::connection($conn_name)->table($table_name)
                        ->select(\DB::raw('distinct(archive_tag) archive_tag'))->orderBy('archive_tag')->get();

        // Select requests types
        $query = \DB::connection($conn_name)->table($table_name)
                ->select('type', \DB::raw('count(*) total_c'),
                //
                \DB::raw('count(id) requests_count'), \DB::raw('sum(has_errors) with_errors_count'), \DB::raw('(count(id)-sum(has_errors)) with_no_errors_count')
        );
        if ($request->get('archive') || $request->get('archive') == '0')
            $query->where('archive_tag', $request->get('archive'));
        $requests_types = $query->groupBy('type')
                        ->orderBy('type', 'asc')->get();
        $requests_types = collect($requests_types);

        // Select requests by countries
        $model = new LogIPs();
        $table_name = $model->getTable();
        $conn_name = $model->getConnectionName();
        // Select aggregate functions
        $req_countries = \DB::connection($conn_name)->table($table_name)
                ->select('country', \DB::raw('count(*) req_total_c'),
                        //
                        \DB::raw('min(country_name) country_name'), \DB::raw('sum(total_c) req_total_sum'), \DB::raw('sum(total_c_error) req_total_sum_error')
                )
                ->groupBy('country')
                ->orderBy('country_name', 'asc')
                ->get();
        $req_countries = collect($req_countries);

        // Count not finished
        $not_finished_c = \DB::connection($conn_name)->table($table_name)
                ->where('is_finished', 0)
                ->count();

        return view('inDbPerformanceMonitor::dashboard', compact(['archives', 'requests_types', 'req_countries', 'not_finished_c']));
    }

    /**
     * Clear your admin monitor login token
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function logout(Request $request) {
        session()->remove('__asamir_token');
        return redirect('admin-monitor')->with('alert-success', 'You are logged out from admin monitor');
    }

    /**
     * Change your password
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function changePassword(Request $request) {
        if ($request->isMethod('POST')) {
            if (\Hash::check($request->get('password'), config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN'))) {
                // Validate password
                if (strlen($request->get('new_password')) < 6 || $request->get('new_password') != $request->get('new_password_confirmed'))
                    return redirect('admin-monitor/change-password')->with('alert-danger', 'New Passsowrd must be at least 6 digits and confirmed');
                // Set new passowrd
                $content = file_get_contents(config_path('inDbPerformanceMonitor.php'));
                $content = str_replace(config('inDbPerformanceMonitor.IN_DB_MONITOR_TOKEN'), bcrypt($request->get('new_password')), $content);
                file_put_contents(config_path('inDbPerformanceMonitor.php'), $content);
                return redirect('admin-monitor/requests')->with('alert-success', 'Passsowrd changed successfully');
            }
            return redirect('admin-monitor/change-password')->with('alert-danger', 'Passsowrd is Not correct');
        }
        return view('inDbPerformanceMonitor::changePassword');
    }

    /**
     * Display logged requests list
     *
     * @return \Illuminate\View\View
     */
    public function getRequests(Request $request) {
        // Initialize variables
        $query = LogRequests::query();
        $search = $request->get('search');
        $search_type = $request->get('search_type');
        $order_by = $request->get('order_by', 'id');
        $order_type = $request->get('order_type', 'desc');
        $has_errors = $request->get('has_errors');
        $type = strtoupper($request->get('type'));

        // Handle search where conditions
        if ($type)
            $query->where('type', '=', $type);
        if ($search) {
            // Search using the keyword
            $query->where(function($q) use($search_type, $search, $has_errors) {
                if ($search_type == 'not like') {
                    $q->where('action', $search_type, $search)
                            ->where('route_uri', $search_type, $search)
                            ->where('route_static_prefix', $search_type, $search)
                            ->where('url', $search_type, $search)
                            ->where('session_id', $search_type, $search)
                            ->where('ip', $search_type, $search)
                            ->where('archive_tag', $search_type, $search);
                    // Search in country name
                    $q->whereHas('ip_info', function($qq) use($search_type, $search) {
                        $qq->where('country_name', $search_type, $search);
                    });
                    // Search in error
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
                            ->orWhere('session_id', $search_type, $search)
                            ->orWhere('ip', $search_type, $search)
                            ->orWhere('archive_tag', $search_type, $search);
                    // Search in country name
                    $q->orWhereHas('ip_info', function($qq) use($search_type, $search) {
                        $qq->where('country_name', $search_type, $search);
                    });
                    // Search in error
                    if ($has_errors)
                        $q->orWhereHas('error', function($qq) use($search_type, $search) {
                            $qq->where('message', $search_type, $search)
                                    ->orWhere('file', $search_type, $search);
                        });
                }
            });
        }
        // continue handles search where conditions
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

        // Get requests
        $requests = $query->with(['ip_info', 'error'])->orderBy($order_by, $order_type)->paginate();

        return view('inDbPerformanceMonitor::getRequests', compact('requests'));
    }

    /**
     * Show certian request details
     * if $id == -1 => Get last request by my session ID
     * if $id == -2 => Get last request by my ip
     * else get request by $id
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\View\View
     */
    public function showRequest(Request $request, $id) {
        $is_last_of_mine = 0;
        // Get last request by my session id
        if ($id == -1) {
            $id = LogRequests::where('session_id', session()->getId())->max('id');
            $is_last_of_mine = -1;
            if (!$id)
                return redirect('admin-monitor/requests')->with('alert-danger', 'There are no requests made by your current session ID.');
        }
        // Get last request by my ip
        if ($id == -2) {
            $id = LogRequests::where('ip', request()->ip())->max('id');
            $is_last_of_mine = -2;
            if (!$id)
                return redirect('admin-monitor/requests')->with('alert-danger', 'There are no requests made by your current IP.');
        }

        // Get request
        $logRequest = LogRequests::findOrFail($id);

        // Get request queries 
        $model = new LogQueries();
        $table_name = $model->getTable();
        $conn_name = $model->getConnectionName();
        $query1 = LogQueries::where('request_id', $id);
        $query2 = \DB::connection($conn_name)->table($table_name)
                        ->where('request_id', $id)
                        ->select(
                                \DB::raw('query as query2'), \DB::raw('sum(time) sum_t'),
                                //
                                \DB::raw('min(connection_name) as connection_name2'), \DB::raw('max(id) as last_id'),
                                //
                                \DB::raw('count(*) total_c'), \DB::raw('sum(is_elequent) elequent_c'), \DB::raw('(count(*) - sum(is_elequent)) non_elequent_c')
                        )->groupBy('query');
        // ---
        if ($request->get('distinct_view') != '1')
            $query1->join(\DB::raw("(" . $query2->toSql() . ") as q2"), 'query', '=', 'query2')
                    ->mergeBindings($query2);
        // Set conditions
        $query1 = $this->setQuereiesWhere($query1, $request);
        $query2 = $this->setQuereiesWhere($query2, $request);
        // Set Order
        $order = ['id', 'asc'];
        if ($request->get('distinct_view') == '1')
            $order = ['sum_t', 'desc'];
        if ($request->get('order'))
            $order = explode('.', $request->get('order'));

        if ($request->get('distinct_view') == '1' && $order[0] == 'time')
            $order[0] = 'sum_t';
        $logQueries = null;
        if ($request->get('distinct_view') == '1')
            $logQueries = $query2->orderBy($order[0], $order[1])->paginate(6);
        else
            $logQueries = $query1->orderBy($order[0], $order[1])->paginate(6);

        // Get request error
        $logError = LogErrors::where('request_id', $id)->first();

        return view('inDbPerformanceMonitor::showRequest', compact(['logRequest', 'logQueries', 'logError', 'is_last_of_mine']));
    }

    /**
     * Set the where conditions of the inner queries search
     * @param Query $query
     * @param Request $request
     * @return Query
     */
    public function setQuereiesWhere($query, $request) {
        $search = $request->get('search');
        if ($search)
            $query->where(function($q) use($search) {
                $q->where('query', 'like', $search)
                        ->orWhere('connection_name', 'like', $search)
                        ->orWhere('bindings', 'like', $search);
            });
        if ($request->get('is_not_elequent'))
            $query->where('is_elequent', '=', 0);
        if ($request->get('reps_count') && $request->get('distinct_view') == '1')
            $query->having(\DB::raw('count(*)'), '>=', $request->get('reps_count'));
        else if ($request->get('reps_count'))
            $query->where('total_c', '>=', $request->get('reps_count'));
        return $query;
    }

    /**
     * Run certain query
     * @param Request $request
     * @param type $id
     * @return \Illuminate\View\View
     */
    public function runQuery(Request $request, $id) {
        // Get query data
        $logQuery = LogQueries::findOrFail($id);
        $results = null;
        $exception = null;
        $start_time = microtime(true);
        try {
            // Case select query => Get records
            if (substr(strtolower($logQuery->query), 0, 7) == 'select ')
                $results = \DB::connection($logQuery->connection_name)->select(\DB::raw($logQuery->query), json_decode($logQuery->bindings, true));
            else // Else run statement
                $results = \DB::connection($logQuery->connection_name)->statement(\DB::raw($logQuery->query), json_decode($logQuery->bindings, true));
        } catch (\Exception $e) {
            $exception = $e;
        }
        $end_time = microtime(true);
        $exec_time = round($end_time - $start_time, 2);

        return view('inDbPerformanceMonitor::runQuery', compact(['logQuery', 'results', 'exception', 'exec_time']));
    }

    /**
     * Archive all requests with tag=0 to tag = date(YmdHis)
     * @return \Illuminate\View\View
     */
    public function archiveRequests() {
        //Update and archive tags
        LogRequests::where('archive_tag', '0')->update([
            'archive_tag' => date('YmdHis')
        ]);

        return redirect('admin-monitor/requests')->with('alert-success', 'Requests archived successfully');
    }

    /**
     * Display statistics report about all requests
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function statisticsReport(Request $request) {
        $model = new LogRequests();
        $table_name = $model->getTable();
        $conn_name = $model->getConnectionName();
        // Select aggregate functions
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

        // Filter the result
        $search = $request->get('search');
        if ($search)
            $query->where(function($q) use($search, $table_name) {
                $q->where('route_uri', 'like', $search)
                        ->orWhere('session_id', 'like', $search)
                        ->orWhere('ip', 'like', $search)
                        ->orWhere(function ($qt) use($search, $table_name) {
                            $qt->whereExists(function ($q) use($search, $table_name) {
                                $model = new LogIPs();
                                $prefix = $model->getConnection()->getTablePrefix();
                                $q->select(\DB::raw(1))
                                ->from($model->getTable())
                                ->where('country_name', 'like', $search)
                                ->whereRaw($prefix . $model->getTable() . '.ip = ' . $prefix . $table_name . '.ip');
                            });
                        });
            });
        if ($request->get('type'))
            $query->where('type', '=', strtoupper($request->get('type')));
        if ($request->get('not_archived'))
            $query->where('archive_tag', '=', 0);
        if ($request->get('from_date'))
            $query->where('created_at', '>=', $request->get('from_date'));
        if ($request->get('to_date'))
            $query->where('created_at', '<', date('Y-m-d', strtotime($request->get('to_date') . "+1 days")));

        // Get the result
        $statistics = $query->groupBy('route_uri', 'type')
                ->orderBy($request->get('order_by', 'max_queries_time'), $request->get('order_type', 'desc'))
                ->paginate();

        return view('inDbPerformanceMonitor::statisticsReport', compact('statistics'));
    }

    /**
     * Display statistics report about all requests errors
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function errorsReport(Request $request) {

        $model_r = new LogRequests();
        $table_name_r = $model_r->getTable();
        $model_e = new LogErrors();
        $table_name_e = $model_e->getTable();
        $conn_name = $model_r->getConnectionName();
        // Select aggregate functions
        $query = \DB::connection($conn_name)
                ->table($table_name_e)
                ->join($table_name_r, $table_name_e . '.request_id', '=', $table_name_r . '.id')
                ->select('route_uri', 'type', 'message',
                //
                \DB::raw('count(*) errors_count'),
                //
                \DB::raw('max(is_json_response) is_json_response'), \DB::raw('max(has_errors) has_errors'), \DB::raw('max(request_id) last_id')
        );

        // Filter the result
        $search = $request->get('search');
        if ($search)
            $query->where(function($q) use($search, $table_name_r) {
                $q->where('route_uri', 'like', $search)
                        ->orWhere('session_id', 'like', $search)
                        ->orWhere('ip', 'like', $search)
                        ->orWhere('message', 'like', $search)
                        ->orWhere('file', 'like', $search)
                        ->orWhere(function ($qt) use($search, $table_name_r) {
                            $qt->whereExists(function ($q) use($search, $table_name_r) {
                                $model = new LogIPs();
                                $prefix = $model->getConnection()->getTablePrefix();
                                $q->select(\DB::raw(1))
                                ->from($model->getTable())
                                ->where('country_name', 'like', $search)
                                ->whereRaw($prefix . $model->getTable() . '.ip = ' . $prefix . $table_name_r . '.ip');
                            });
                        });
            });
        if ($request->get('type'))
            $query->where('type', '=', strtoupper($request->get('type')));
        if ($request->get('not_archived'))
            $query->where('archive_tag', '=', 0);
        if ($request->get('from_date'))
            $query->where($table_name_r . '.created_at', '>=', $request->get('from_date'));
        if ($request->get('to_date'))
            $query->where($table_name_r . '.created_at', '<', date('Y-m-d', strtotime($request->get('to_date') . "+1 days")));

        // Get the result
        $errors_stats = $query->groupBy('route_uri', 'type', 'message')
                ->orderBy($request->get('order_by', 'errors_count'), $request->get('order_type', 'desc'))
                ->paginate();
        return view('inDbPerformanceMonitor::errorsReport', compact('errors_stats'));
    }

    /**
     * Display statistics report about all archives
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function archivesReport(Request $request) {
        $model = new LogRequests();
        $table_name = $model->getTable();
        $conn_name = $model->getConnectionName();
        // Select aggregate functions
        $query = \DB::connection($conn_name)->table($table_name)
                ->select('archive_tag',
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

        // Get the result
        $archives = $query->groupBy('archive_tag')
                ->orderBy('archive_tag')
                ->get();

        return view('inDbPerformanceMonitor::archivesReport', compact('archives'));
    }

    public function deleteArchive(Request $request) {
        // Delete from Requests
        LogRequests::where('archive_tag', '=', $request->archive)->delete();

        // Delete from Queries
        LogQueries::whereDoesntHave('request')->delete();

        // Delete from Errors
        LogErrors::whereDoesntHave('request')->delete();

        // Delete from IPs
        LogIPs::whereDoesntHave('request')->delete();

        return redirect('admin-monitor/archives-report')->with('alert-success', 'Archive [ ' . $request->archive . ' ] deleted successfully');
    }

}
