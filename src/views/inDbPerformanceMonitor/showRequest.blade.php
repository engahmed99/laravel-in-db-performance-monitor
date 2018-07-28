@extends('inDbPerformanceMonitor::layout')
<style>
    .panel-custom>.panel-heading {
        color: #fff;
        background-color: #666666 !important;
        border-color: #666666 !important;
    }
</style>

@section('content')
<div class="container">
    <div class="panel panel-custom"> 
        <div class="panel-heading text-center">
            @if($is_last_of_mine == -1)
            <h2 style="padding: 0px; margin: 0px">Last Request of Mine ID = {{$logRequest->id}}, by my Session ID</h2> 
            @elseif($is_last_of_mine == -2)
            <h2 style="padding: 0px; margin: 0px">Last Request of Mine ID = {{$logRequest->id}}, by my IP</h2> 
            @else
            <h2 style="padding: 0px; margin: 0px">Previewing Request ID = {{$logRequest->id}}</h2> 
            @endif
        </div> 
        <div class="panel-body">
            <table class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: center">ID</th>
                        <th style="text-align: center">Creation Date</th>
                        <th>Action</th>
                        <th style="text-align: center">Queries Count</th>
                        <th style="text-align: center">Queries Time</th>
                        <th style="text-align: center">Exec Time</th>
                        <th style="text-align: center">Session</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th rowspan="2" style="text-align: center">{{($logRequest->id)}}</th>
                        <td style="text-align: center">{{$logRequest->created_at}}</td>
                        <td>
                <li><b>Action:</b> {{$logRequest->action}}</li>
                <li><b>Route URI:</b> {{$logRequest->route_uri}}</li>
                <li><b>Route Static:</b> {{$logRequest->route_static_prefix}}</li>
                <li><b>IP:</b> {{$logRequest->ip}}</li>
                <li><b>URL:</b> {{$logRequest->url}}</li>
                </td>
                <td style="text-align: center">{{$logRequest->queries_total_count}} &rarr; <span style="color: red" title="Not elequent queries">({{$logRequest->queries_not_elequent_count}})</span></td>
                <td style="text-align: center">
                    {{$logRequest->queries_total_time}} ms <br/>
                    {{$logRequest->queries_total_time/1000}} s
                </td>
                <td style="text-align: center">{{$logRequest->exec_time}} s</td>
                <td style="text-align: center">
                    <p><b>Session ID:</b> {{$logRequest->session_id}}</p>
                </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <p><b>Parameters: </b>
                            <span class="label label-success">{{$logRequest->type}}</span>
                            @if($logRequest->has_errors == '1')
                            <span class="label label-danger">Error</span>
                            @endif
                            @if($logRequest->is_json_response == '1')
                            <span class="label label-warning">JSON Response</span>
                            @endif
                        </p>
                        <pre><textarea id="params-textarea" class="form-control" readonly="" style="max-height: 300px">{{print_r(json_decode($logRequest->parameters, true))}}</textarea></pre>
                    </td>
                    <td colspan="3">
                        <p><b>Session Data:</b>
                        <pre><textarea id="session-textarea" class="form-control" readonly="" style="max-height: 300px">{{print_r(json_decode($logRequest->session_data, true))}}</textarea></pre>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel panel-info" id="queries-section"> 
        <div class="panel-heading"> 
            <h3 class="panel-title">Request Queries</h3>            
        </div> 
        <div class="panel-body">
            <div class="well">
                <form role="form" class="search-width">

                    <!-- Search by -->
                    <div class="form-group">

                        <div class="row">
                            <div class="col-md-1">
                                <label for="search">Search</label>
                            </div>
                            <div class="col-md-5">
                                <textarea class="form-control" id="search" name="search" placeholder="Query Or Connection Name">{{request('search')}}</textarea>
                            </div>
                            <div class="col-md-3">
                                <select id="order" class="form-control" name="order" value="{{request('order')}}">
                                    <option value="id.asc" @if(request('order_type') == 'id.asc'){{'selected'}}@endif >ID Ascending</option>
                                    <option value="id.desc" @if(request('order_type') == 'id.desc'){{'selected'}}@endif >ID Descending</option>
                                    <option value="time.asc" @if(request('order_type') == 'time.asc'){{'selected'}}@endif >Time Ascending</option>
                                    <option value="time.desc" @if(request('order_type') == 'time.desc'){{'selected'}}@endif >Time Descending</option>
                                    <option value="connection_name.asc" @if(request('order_type') == 'connection_name.asc'){{'selected'}}@endif >Connection Ascending</option>
                                    <option value="connection_name.desc" @if(request('order_type') == 'connection_name.desc'){{'selected'}}@endif >Connection Descending</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <div class="checkbox-inline">
                                    <label>
                                        <input type="checkbox" name="is_not_elequent" id="is_not_elequent" value="1" @if(request('is_not_elequent') == '1'){{'checked'}}@endif > Not Elequent
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2" style="text-align: center">
                                <button type="submit" class="btn btn-primary">Search</button>
                                <button type="reset" class="btn btn-white">Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- ====== -->

            <div style="text-align: center; margin-top: -10px" class="alert-info"><h4>Page {{$logQueries->currentPage()}} of {{$logQueries->lastPage()}} - Display {{$logQueries->count()}} of {{$logQueries->total()}} Records</h4></div>

            <table class="table table-hover table-striped table-bordered">
                @forelse($logQueries as $i=>$query)
                @if($i == 0)
                <thead>
                    <tr>
                        <th style="text-align: center"># (ID)</th>
                        <th style="text-align: center">Creation Date</th>
                        <th>Query</th>
                        <th>Bindings</th>
                        <th style="text-align: center">Time</th>
                        <th style="text-align: center">Connection Name</th>
                        <th style="text-align: center">Is Elequent</th>
                        <th></th>
                    </tr>
                </thead>
                @endif
                <tr>
                    <th style="text-align: center">{!!($i+1+(($logQueries->currentPage()-1)*$logQueries->perPage())).' <span class="label label-info">('.$query->id.')</span>'!!}</th>
                    <td style="text-align: center">{{$query->created_at}}</td>
                    <td><textarea readonly="" class="form-control">{{$query->query}}</textarea></td>
                    <td><textarea readonly="" class="form-control">{{$query->bindings}}</textarea></td>
                    <td style="text-align: center">
                        {{$query->time}} ms<br/>
                        {{($query->time/1000)}} s
                    </td>
                    <td style="text-align: center">{{$query->connection_name}}</td>
                    <td style="text-align: center">{{$query->is_elequent}}</td>
                    <td style="text-align: center"><a href="{{url('admin-monitor/run-query/'.$query->id)}}">Run</a></td>
                </tr>
                @empty
                <tr>
                    <th>The request has no queries</th>
                </tr>                        
                @endforelse
            </table>
            <div class="row" align='center'>
                {{$logQueries->appends(request()->all())->links()}}
            </div>
        </div> 
    </div>
    <div class="panel panel-danger"> 
        <div class="panel-heading"> 
            <h3 class="panel-title">Request Error</h3> 
        </div> 
        <div class="panel-body">
            <table class="table table-hover table-striped table-bordered">
                @if($logError)
                <tr>
                    <th>Creation Date</th><td>{{$logError->created_at}}</td>
                </tr>
                <tr>
                    <th>Message</th><td>{{$logError->message}}</td>
                </tr>
                <tr>
                    <th>Code</th><td>{{$logError->code}}</td>
                </tr>
                <tr>
                    <th>File</th><td>{{$logError->file}}</td>
                </tr>
                <tr>
                    <th>Line</th><td>{{$logError->line}}</td>
                </tr>
                <tr>
                    <th>Trace</th><td>{!!str_replace('#', '<br/>#', $logError->trace)!!}</td>
                </tr>
                @else
                <tr><td>The request has no errors</td></tr>
                @endif
            </table>
        </div>
    </div>
</div>

<script>
    document.getElementById('params-textarea').rows = document.getElementById('params-textarea').innerHTML.split("\n").length + 2;
    document.getElementById('session-textarea').rows = document.getElementById('session-textarea').innerHTML.split("\n").length + 2;
</script>
@if(request()->all())
<script>
    window.location = "#queries-section";
</script>
@endif
@endsection

