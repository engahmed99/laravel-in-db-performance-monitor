@extends('inDbPerformanceMonitor::layout')

@section('title', 'Errors Report')

<style>
    .panel-custom>.panel-heading {
        color: #fff;
        background-color: #ed5441 !important;
        border-color: #ed5441 !important;
    }
</style>

@section('content')
<div class="container">
    <div class="panel panel-custom" style="padding: 0px; margin: 0px"> 
        <div class="panel-heading" style="text-align: center"> 
            <h2 class="" style="padding: 0px; margin: 0px">Errors Statistics Report</h2> 
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
                            <input type="text" class="form-control" id="search" name="search" placeholder="Route URI, Session ID, Error Message Or File" value="{{request('search')}}">
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
                                <option value="errors_count" @if(request('order_by') == 'errors_count'){{'selected'}}@endif >Errors Count</option>
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

    <div class="alert-info" style="text-align: center"><h4 class="">Page {{$errors_stats->currentPage()}} of {{$errors_stats->lastPage()}} - Display {{$errors_stats->count()}} of {{$errors_stats->total()}} Records</h4></div>   

    <table class="table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th style="text-align: center">#</th>
                <th>Route URI</th>
                <th style="text-align: center">Error Message</th>
                <th style="text-align: center">Errors Count</th>
                <th style="text-align: center"></th>                
            </tr>
        </thead>
        <tbody>
            @foreach ($errors_stats as $i => $stat)
            <tr>
                <th style="text-align: center">{{($i+1+(($errors_stats->currentPage()-1)*$errors_stats->perPage()))}}</th>
                <th style="text-align: center">{{$stat->route_uri}}
                    <br/><span class="label label-success">{{$stat->type}}</span>
                    @if($stat->has_errors == '1')
                    <span class="label label-danger">Error</span>
                    @endif
                    @if($stat->is_json_response == '1')
                    <span class="label label-warning">JSON Response</span>
                    @endif
                </th>
                <td style="text-align: center;">{{$stat->message}}</td>
                <td style="text-align: center;color: red">{{$stat->errors_count}}</td>
                <td style="text-align: center"><a href="{{url('admin-monitor/request/'.$stat->last_id)}}" class="btn btn-danger">Show Last Request</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="row" align='center'>
        {{$errors_stats->appends(request()->all())->links()}}
    </div>
</div>

@endsection

