@extends('inDbPerformanceMonitor::layout')

@section('title', 'Show Request '.$logRequest->id)

@push('styles')
<style>
    .panel-custom>.panel-heading {
        color: #fff;
        background-color: #666666 !important;
        border-color: #666666 !important;
    }
    .popover-title {
        background-color: #f8a841 !important;
    }
    .popover-textarea {
        background-color: #f8a841 !important;
    }
</style>
@endpush

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
            <div class="table-responsive">

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
                            <td style="text-align: center">
                                {{$logRequest->created_at}}
                                <br/>
                                <br/>
                                <span class="label label-default">Archive: {{$logRequest->archive_tag}}</span>
                            </td>
                            <td><ul>
                                    <li><b>Action:</b> {{$logRequest->action}}</li>
                                    <li><b>Route URI:</b> {{$logRequest->route_uri}}</li>
                                    <li><b>Route Static:</b> {{$logRequest->route_static_prefix}}</li>
                                    <li><b>IP:</b> {{$logRequest->ip}} &nbsp;&nbsp; <span class="label label-info">{{$logRequest->ip_info->country_name}}</span></li>
                                    <li><b>URL:</b> {{$logRequest->url}}</li>
                                </ul>
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
                                <textarea id="params-textarea" class="form-control" readonly="" style="">{{print_r(unserialize($logRequest->parameters))}}</textarea>
                            </td>
                            <td colspan="3">
                                <p><b>Session Data:</b>
                                    <textarea id="session-textarea" class="form-control" readonly="" style="">{{print_r(unserialize($logRequest->session_data))}}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                                <textarea class="form-control" id="search" name="search" placeholder="Query, Bindings, Or Connection Name => e.x. select * from%" data-toggle="tooltip" data-placement="right" title="Remember: you can use sql like wildcards like % or _ e.x. %/customers%, /sales%">{{request('search')}}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-8">
                                        <select id="order" class="form-control" name="order" value="{{request('order')}}">
                                            <option value="id.asc" @if(request('order') == 'id.asc'){{'selected'}}@endif >Creation Date Ascending</option>
                                            <option value="id.desc" @if(request('order') == 'id.desc'){{'selected'}}@endif >Creation Date Descending</option>
                                            <option value="time.asc" @if(request('order') == 'time.asc'){{'selected'}}@endif >Time Ascending</option>
                                            <option value="time.desc" @if(request('order') == 'time.desc'){{'selected'}}@endif >Time Descending</option>
                                            <option value="total_c.asc" @if(request('order') == 'total_c.asc'){{'selected'}}@endif >Repetition Count Ascending</option>
                                            <option value="total_c.desc" @if(request('order') == 'total_c.desc'){{'selected'}}@endif >Repition Count Descending</option>
                                            <option value="connection_name.asc" @if(request('order') == 'connection_name.asc'){{'selected'}}@endif >Connection Ascending</option>
                                            <option value="connection_name.desc" @if(request('order') == 'connection_name.desc'){{'selected'}}@endif >Connection Descending</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" id="reps_count" name="reps_count" placeholder="Repititions >= X" value="{{request('reps_count')}}">
                                    </div>
                                </div>
                                <br/>
                                <div class="row">
                                    <div class="col-md-8" style="text-align: center">
                                        <div class="checkbox-inline">
                                            <label>
                                                <input type="checkbox" name="is_not_elequent" id="is_not_elequent" value="1" @if(request('is_not_elequent') == '1'){{'checked'}}@endif > Not Elequent
                                            </label>
                                        </div>
                                        <div class="checkbox-inline">
                                            <label>
                                                <input type="checkbox" name="distinct_view" id="distinct_view" value="1" @if(request('distinct_view') == '1'){{'checked'}}@endif > View Distinct Queries
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="text-align: center">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!-- ====== -->

            <div style="text-align: center; margin-top: -10px" class="alert-info"><h4>Page {{$logQueries->currentPage()}} of {{$logQueries->lastPage()}} - Display {{$logQueries->count()}} of {{$logQueries->total()}} Records</h4></div>

            @if(request('distinct_view') == '1')
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    @forelse($logQueries as $i=>$query)
                    @if($i == 0)
                    <thead>
                        <tr>
                            <th style="text-align: center">#</th>
                            <th>Query</th>
                            <th style="text-align: center">Repeated</th>
                            <th style="text-align: center">Total Time</th>
                            <th style="text-align: center">Connection Name</th>
                            <th></th>
                        </tr>
                    </thead>
                    @endif
                    <tr>
                        <th style="text-align: center">{!!($i+1+(($logQueries->currentPage()-1)*$logQueries->perPage()))!!}</th>
                        <td><textarea readonly="" class="form-control">{{$query->query2}}</textarea></td>
                        <td><ul>
                                <li style="color: red"><b>Non Elequent:</b> {{$query->non_elequent_c}}</li>
                                <li style="color: green"><b>Elequent:</b> {{$query->elequent_c}}</li>
                                <li><b>Total:</b> {{$query->total_c}}</li>
                            </ul>
                        </td>
                        <td style="text-align: center">{{$query->sum_t}} ms<br/>
                            {{($query->sum_t/1000)}} s
                        </td>
                        <td style="text-align: center">{{$query->connection_name2}}</td>
                        <td style="text-align: center"><br/><a href="{{url('admin-monitor/run-query/'.$query->last_id)}}">Run Last Query</a></td>
                    </tr>
                    @empty
                    <tr>
                        <th>The request has no queries</th>
                    </tr>                        
                    @endforelse

                </table>
            </div>
            @else
            <div class="table-responsive">
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
                            <th style="text-align: center">Repeated</th>
                            <th style="text-align: center">Is Elequent</th>
                            <th></th>
                        </tr>
                    </thead>
                    @endif
                    <tr>
                        <th style="text-align: center">{!!($i+1+(($logQueries->currentPage()-1)*$logQueries->perPage())).' <span class="label label-info">('.$query->id.')</span>'!!}</th>
                        <td style="text-align: center">{{$query->created_at}}</td>
                        <td><textarea readonly="" class="form-control query-textarea">{{$query->query}}</textarea></td>
                        <td><textarea readonly="" class="form-control bind-textarea">{{$query->getBindingsPrint()}}</textarea></td>
                        <td style="text-align: center">
                            {{$query->time}} ms<br/>
                            {{($query->time/1000)}} s
                        </td>
                        <td style="text-align: center">{{$query->connection_name}}</td>
                        <td style="text-align: center">{{$query->total_c}}</td>
                        <td style="text-align: center">@if($query->is_elequent == 1) Yes @else No @endif</td>
                        <td style="text-align: center"><a href="{{url('admin-monitor/run-query/'.$query->id)}}">Run</a></td>
                    </tr>
                    @empty
                    <tr>
                        <th>The request has no queries</th>
                    </tr>                        
                    @endforelse
                </table>
            </div>
            @endif
            <div class="row" align='center'>
                @if($app_version_less_2)
                {!!$logQueries->appends(request()->all())->render()!!}
                @else
                {{$logQueries->appends(request()->all())->links()}}
                @endif
            </div>
        </div> 
    </div>
    <div class="panel panel-danger"> 
        <div class="panel-heading"> 
            <h3 class="panel-title">Request Error</h3> 
        </div> 
        <div class="panel-body">
            <div class="table-responsive">

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
                        <th>Trace</th><td>{!!str_replace("\n", "<br/>---<br/>", $logError->trace)!!}</td>
                    </tr>
                    @else
                    <tr><td>The request has no errors</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>

<script>
    $(function () {
        // Set params & session textarea sizes
        $("#params-textarea")[0].rows = $("#params-textarea").html().split("\n").length + 2;
        $("#session-textarea")[0].rows = $("#session-textarea").html().split("\n").length + 2;
        if ($("#params-textarea")[0].rows > 15)
            $("#params-textarea")[0].rows = 15;
        if ($("#session-textarea")[0].rows > 15)
            $("#session-textarea")[0].rows = 15;

        $(".query-textarea, .bind-textarea").click(function () {
            // Initialize
            var query_e = null;
            var bind_e = null;
            if ($(this).hasClass('query-textarea')) {
                query_e = $(this);
                bind_e = $(this).parent().next().find('textarea').eq(0);
            } else {
                query_e = $(this).parent().prev().find('textarea').eq(0);
                bind_e = $(this);
            }
            // Reset
            $('[data-toggle="popover"]').not(query_e).not(bind_e).popover('destroy');
            $('.popover-textarea').removeClass('popover-textarea');
            // Set query popover listener
            query_e.attr('data-toggle', 'popover');
            query_e.attr('data-placement', 'left');
            query_e.attr('data-trigger', 'manual');
            query_e.attr('title', 'Full Query');
            query_e.on('inserted.bs.popover', function () {
                query_e.next().find('.popover-title').eq(0).html('Full Query <a class="pull-right popover-close-link" title="Close">x</a>');
                query_e.next().find('.popover-content').eq(0).html(query_e.html());
            });
            query_e.popover('show');
            query_e.addClass('popover-textarea');
            // Set bindings popover listener
            bind_e.attr('data-toggle', 'popover');
            bind_e.attr('data-placement', 'right');
            bind_e.attr('data-trigger', 'manual');
            bind_e.attr('title', 'Bindings');
            bind_e.on('inserted.bs.popover', function () {
                bind_e.next().find('.popover-title').eq(0).html('Bindings &nbsp;&nbsp;&nbsp;<a class="pull-right popover-close-link" title="Close">x</a>');
                bind_e.next().find('.popover-content').eq(0).html('<pre>' + bind_e.html() + '</pre>');
            });
            bind_e.popover('show');
            bind_e.addClass('popover-textarea');
            // Set close link event
            $(".popover-close-link").click(function (e) {
                e.preventDefault();
                $('[data-toggle="popover"]').popover('destroy');
                $('.popover-textarea').removeClass('popover-textarea');
                return false;
            });
        });

    });
</script>
@if(request()->all())
<script>
    window.location = "#queries-section";
</script>
@endif
@endsection

