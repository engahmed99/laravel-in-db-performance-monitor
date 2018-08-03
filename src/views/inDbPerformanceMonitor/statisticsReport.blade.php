@extends('inDbPerformanceMonitor::layout')

@section('title', 'Statistics Report')

@push('styles')
<style>
    .panel-custom>.panel-heading {
        color: #fff;
        background-color: #32c8de !important;
        border-color: #32c8de !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="panel panel-custom" style="padding: 0px; margin: 0px"> 
        <div class="panel-heading" style="text-align: center"> 
            <h2 class="" style="padding: 0px; margin: 0px">Requests Statistics Report</h2> 
        </div> 
        <div class="well">
            <form role="form" class="search-width">

                <!-- Search by -->
                <div class="form-group">

                    <div class="row">
                        <div class="col-md-1">
                            <label for="search">Search</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="search" name="search" placeholder="Route URI Or Session ID" value="{{request('search')}}">
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="type" name="type" placeholder="e.x GET,POST,..." value="{{request('type')}}">
                        </div>
                        <div class="col-md-1" style="text-align: center">
                            <label for="from_date">From</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="from_date" name="from_date" placeholder="From Date" value="{{request('from_date')}}">
                        </div>
                        <div class="col-md-2">
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="not_archived" id="not_archived" value="1" @if(request('not_archived') == '1'){{'checked'}}@endif > Not Archived
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!--Sort By-->
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-1">
                            <label for="order_by">Sort By</label>
                        </div>
                        <div class="col-md-4">
                            <select id="order_by" class="form-control" name="order_by" value="{{request('order_by')}}">
                                <option value="max_queries_time" @if(request('order_by') == 'max_queries_time'){{'selected'}}@endif >Max Queries Time</option>
                                <option value="min_queries_time" @if(request('order_by') == 'min_queries_time'){{'selected'}}@endif >Min Queries Time</option>
                                <option value="max_queries_count" @if(request('order_by') == 'max_queries_count'){{'selected'}}@endif >Max Queries Count</option>
                                <option value="min_queries_count" @if(request('order_by') == 'min_queries_count'){{'selected'}}@endif >Min Queries Count</option>
                                <option value="max_exec_time" @if(request('order_by') == 'max_exec_time'){{'selected'}}@endif >Max Exec. Time</option>
                                <option value="min_exec_time" @if(request('order_by') == 'min_exec_time'){{'selected'}}@endif >Min Exec. Time</option>
                                <option value="requests_count" @if(request('order_by') == 'requests_count'){{'selected'}}@endif >Requests Count</option>
                                <option value="with_errors_count" @if(request('order_by') == 'with_errors_count'){{'selected'}}@endif >Requests Errors Count</option>
                                <option value="route_uri" @if(request('order_by') == 'route_uri'){{'selected'}}@endif >Route URI Name</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select id="order_type" class="form-control" name="order_type" value="{{request('order_type')}}">
                                <option value="desc" @if(request('order_type') == 'desc'){{'selected'}}@endif >Descending</option>
                                <option value="asc" @if(request('order_type') == 'asc'){{'selected'}}@endif >Ascending</option>
                            </select>
                        </div>
                        <div class="col-md-1" style="text-align: center">
                            <label for="to_date">To</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="to_date" name="to_date" placeholder="From Date" value="{{request('to_date')}}">
                        </div>
                        <div class="col-md-2" style="text-align: center">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <button type="reset" class="btn btn-white">Reset</button>
                        </div>
                    </div>
                </div>
            </form>								
            <!-- Advance search ends -->
        </div>
    </div>

    <div class="alert-info" style="text-align: center"><h4 class="">Page {{$statistics->currentPage()}} of {{$statistics->lastPage()}} - Display {{$statistics->count()}} of {{$statistics->total()}} Records</h4></div>   

    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th style="text-align: center">#</th>
                <th>Route URI</th>
                <th style="text-align: center">Queries Count</th>
                <th style="text-align: center">Queries Time</th>
                <th style="text-align: center">Exec Time</th>
                <th style="text-align: center">Requests Count</th>
                <th style="text-align: center"></th>                
            </tr>
        </thead>
        <tbody>
            @foreach ($statistics as $i => $stat)
            <tr>
                <th style="text-align: center">{{($i+1+(($statistics->currentPage()-1)*$statistics->perPage()))}}</th>
                <th style="text-align: center">{{$stat->route_uri}}
                    <br/><span class="label label-success">{{$stat->type}}</span>
                    @if($stat->has_errors == '1')
                    <span class="label label-danger">Error</span>
                    @endif
                    @if($stat->is_json_response == '1')
                    <span class="label label-warning">JSON Response</span>
                    @endif
                </th>
                <td>
        <li><b>Min:</b> {{$stat->min_queries_count}} &rarr; <span style="color: red" title="Not elequent queries">({{$stat->min_not_elequent_queries_count}})</span></li>
        <li><b>Max:</b> {{$stat->max_queries_count}} &rarr; <span style="color: red" title="Not elequent queries">({{$stat->max_not_elequent_queries_count}})</span></li>
        </td>
        <td>
        <li><b>Min:</b> {{round($stat->min_queries_time, 3)}} ms</li>
        <li><b>Avg:</b> {{round($stat->avg_queries_time, 3)}} ms</li>
        <li><b>Max:</b> {{round($stat->max_queries_time, 3)}} ms</li>
        </td>
        <td>
        <li><b>Min:</b> {{round($stat->min_exec_time, 3)}} s</li>
        <li><b>Avg:</b> {{round($stat->avg_exec_time, 3)}} s</li>
        <li><b>Max:</b> {{round($stat->max_exec_time, 3)}} s</li>
        </td>
        <td>
        <li style="color: red"><b>With Errors:</b> {{$stat->with_errors_count}}</li>
        <li style="color: green"><b>Without Errors:</b> {{$stat->with_no_errors_count}}</li>
        <li><b>Total:</b> {{$stat->requests_count}}</li>
        </td>
        <td style="text-align: center"><br/><a href="{{url('admin-monitor/request/'.$stat->last_id)}}" class="btn btn-danger">Show Last Request</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    <div class="row" align='center'>
        @if($app_version_less_2)
        {!!$statistics->appends(request()->all())->render()!!}
        @else
        {{$statistics->appends(request()->all())->links()}}
        @endif
    </div>
</div>

@endsection

