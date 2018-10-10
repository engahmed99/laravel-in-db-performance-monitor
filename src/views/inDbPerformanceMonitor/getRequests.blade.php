@extends('inDbPerformanceMonitor::layout')

@section('title', 'Requests List')

@section('content')
<div class="container">
    <div class="panel panel-primary" style="padding: 0px; margin: 0px"> 
        <div class="panel-heading" style="text-align: center"> 
            <h2 class="" style="padding: 0px; margin: 0px">List Of all Requests</h2> 
        </div> 
        <div class="well">
            <form role="form" class="search-width">

                <!-- Search by -->
                <div class="form-group">

                    <div class="row">
                        <div class="col-md-1">
                            <label for="search">Search</label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" class="form-control" id="search" name="search" placeholder="e.x. action,ip,url,route,session_id,archive_tag,.. => %/orders%" value="{{request('search')}}" data-toggle="tooltip" data-placement="right" title="Remember: you can use sql like wildcards like % or _ e.x. %/customers%, /sales%">
                        </div>
                        <div class="col-md-2">
                            <select id="search_type" class="form-control" name="search_type" value="{{request('search_type')}}">
                                <option value="like" @if(request('search_type') == 'like'){{'selected'}}@endif >Like</option>
                                <option value="not like" @if(request('search_type') == 'not like'){{'selected'}}@endif >Not Like</option>
                            </select>
                        </div>
                        <div class="col-md-1" style="text-align: center">
                            <label for="from_date">From</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="from_date" name="from_date" placeholder="From Date" value="{{request('from_date')}}">
                        </div>
                    </div>

                </div>

                <!-- Sory by -->

                <div class="form-group">

                    <div class="row">
                        <div class="col-md-1">
                            <label for="order_by">Sort By</label>
                        </div>
                        <div class="col-md-6">
                            <select id="order_by" class="form-control" name="order_by" value="{{request('order_by')}}">
                                <option value="id" @if(request('order_by') == 'id'){{'selected'}}@endif >Creation Date</option>
                                <option value="queries_total_time" @if(request('order_by') == 'queries_total_time'){{'selected'}}@endif >Queries Total Time</option>
                                <option value="queries_total_count" @if(request('order_by') == 'queries_total_count'){{'selected'}}@endif >Queries Total Count</option>
                                <option value="exec_time" @if(request('order_by') == 'exec_time'){{'selected'}}@endif >Execution Time</option>
                                <option value="archive_tag" @if(request('order_by') == 'archive_tag'){{'selected'}}@endif >Archive Tag</option>
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
                    </div>		
                </div>

                <!-- Filters -->
                <div class="form-group">

                    <div class="row">
                        <div class="col-md-1">
                            <label for="queries_count">Filters</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" id="type" name="type" placeholder="e.x. GET,POST,..." value="{{request('type')}}">
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" id="queries_count" name="queries_count" placeholder="Queries >= X" value="{{request('queries_count')}}">
                        </div>
                        <div class="col-md-5" style="text-align: center">
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="has_not_elequent" id="has_not_elequent" value="1" @if(request('has_not_elequent') == '1'){{'checked'}}@endif > Not Elequent
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="is_json_response" id="is_json_response" value="1" @if(request('is_json_response') == '1'){{'checked'}}@endif > JSON Response
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="has_errors" id="has_errors" value="1" @if(request('has_errors') == '1'){{'checked'}}@endif > Errors
                                </label>
                            </div>
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="not_archived" id="not_archived" value="1" @if(request('not_archived') == '1'){{'checked'}}@endif > Not Archived
                                </label>
                            </div>                            
                        </div>
                        <div class="col-md-2" style="text-align: center">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <button type="reset" class="btn btn-danger" onclick="confirmArchive()">Archive</button>
                        </div>
                    </div>
                </div>
            </form>								
            <!-- Advance search ends -->
        </div>
    </div>

    <div class="alert-info" style="text-align: center"><h4 class="">Page {{$requests->currentPage()}} of {{$requests->lastPage()}} - Display {{$requests->count()}} of {{$requests->total()}} Records</h4></div>

    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th style="text-align: center"># (ID)</th>
                    <th style="text-align: center">Creation Date</th>
                    <th>Action</th>
                    <th style="text-align: center">Queries Count</th>
                    <th style="text-align: center">Queries Time</th>
                    <th style="text-align: center">Exec Time</th>
                    <th style="text-align: center">Session</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $i => $req)
                <tr>
                    <th rowspan="2" style="text-align: center">{!!($i+1+(($requests->currentPage()-1)*$requests->perPage())).' <span class="label label-info">('.$req->id.')</span>'!!}</th>
                    <th rowspan="2" style="text-align: center">
                        <a href="{{url('admin-monitor/request/'.$req->id)}}">{{$req->created_at}} --></a> 
                        <br/>
                        <br/>
                        <span class="label label-default">Archive: {{$req->archive_tag}}</span>
                    </th>
                    <td rowspan="2"><ul>
                            <li><b>Action:</b> {{$req->action}}</li>
                            <li><b>Route URI:</b> {{$req->route_uri}}</li>
                            <li><b>Route Static:</b> {{$req->route_static_prefix}}</li>
                            <li><b>IP:</b> {{$req->ip}} &nbsp;&nbsp; <span class="label label-info">{{$req->ip_info->country_name}}</span></li>
                            <li><b>URL:</b> {{$req->url}}</li>
                        </ul>
                    </td>
                    <td style="text-align: center">{{$req->queries_total_count}} &rarr; <span style="color: red" title="Not elequent queries">({{$req->queries_not_elequent_count}})</span></td>
                    <td style="text-align: center">
                        {{$req->queries_total_time}} ms <br/>
                        {{$req->queries_total_time/1000}} s
                    </td>
                    <td style="text-align: center">{{$req->exec_time}} s</td>
                    <td rowspan="2" style="text-align: center">
                        <p><b>Session ID:</b> {{$req->session_id}}</p>
                        <p><b>Session Data:</b><textarea readonly="" class="form-control">{{ json_encode(unserialize($req->session_data)) }}</textarea></p>
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center">
                        <b>Parameters: </b>
                        <span class="label label-success">{{$req->type}}</span>
                        @if($req->has_errors == '1')
                        <span class="label label-danger" title="@if($req->error){{$req->error->message.' => file '.$req->error->file.' => line:'.$req->error->line }}@endif">Error</span>
                        @endif
                        @if($req->is_json_response == '1')
                        <span class="label label-warning">JSON Response</span>
                        @endif
                        <textarea readonly="" class="form-control">{{ json_encode(unserialize($req->parameters)) }}</textarea>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row" align='center'>
        @if($app_version_less_2)
        {!!$requests->appends(request()->all())->render()!!}
        @else
        {{$requests->appends(request()->all())->links()}}
        @endif
    </div>
</div>

<script>
    function confirmArchive() {
        var r = confirm("Are you sure, you want to archive unarchived requests???");
        if (r == true) {
            window.location = "{{url('admin-monitor/archive-requests')}}";
            return true;
        } else {
            return false;
        }

    }
</script>

@endsection

