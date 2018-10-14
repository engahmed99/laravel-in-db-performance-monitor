@extends('inDbPerformanceMonitor::layout')

@section('title', 'IPs Report')

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
            <h2 class="" style="padding: 0px; margin: 0px">Requests IPs Report</h2> 
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
                            <input type="text" class="form-control" id="search" name="search" placeholder="e.x. IP, Country, City, Or Region" value="{{request('search')}}" data-toggle="tooltip" data-placement="right" title="Remember: you can use sql like wildcards like % or _ e.x. %/customers%, /sales%">
                        </div>
                        <div class="col-md-1" style="text-align: center">
                            <label for="from_date">From</label>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" id="from_date" name="from_date" placeholder="From Date" value="{{request('from_date')}}">
                        </div>
                        <div class="col-md-2" style="text-align: center">
                            <div class="checkbox-inline">
                                <label>
                                    <input type="checkbox" name="not_finished" id="not_finished" value="1" @if(request('not_finished') == '1'){{'checked'}}@endif > Not Completed
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
                                <option value="created_at" @if(request('order_by') == 'created_at'){{'selected'}}@endif >Creation Date</option>
                                <option value="updated_at" @if(request('order_by') == 'updated_at'){{'selected'}}@endif >Last Update Date</option>
                                <option value="country_name" @if(request('order_by') == 'country_name'){{'selected'}}@endif >Country</option>
                                <option value="total_c" @if(request('order_by') == 'total_c'){{'selected'}}@endif >Total Requests</option>
                                <option value="total_c_error" @if(request('order_by') == 'total_c_error'){{'selected'}}@endif >Total Requests With Errors</option>
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
                        </div>
                    </div>
                </div>
            </form>								
            <!-- Advance search ends -->
        </div>
    </div>

    <div class="alert-info" style="text-align: center"><h4 class="">Page {{$ips->currentPage()}} of {{$ips->lastPage()}} - Display {{$ips->count()}} of {{$ips->total()}} Records</h4></div>   

    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th style="text-align: center">#</th>
                    <th style="text-align: center">IP</th>
                    <th style="text-align: center">Country</th>
                    <th style="text-align: center">City</th>
                    <th style="text-align: center">Region</th>
                    <th style="text-align: center">Requests Count</th>
                    <th style="text-align: center">Completed</th>                
                    <th style="text-align: center">Start Date</th>                
                    <th style="text-align: center">End Date</th>                
                </tr>
            </thead>
            <tbody>
                @foreach ($ips as $i => $stat)
                <tr>
                    <th style="text-align: center">{{($i+1+(($ips->currentPage()-1)*$ips->perPage()))}}</th>
                    <th style="text-align: center"><a href="https://ipinfo.io/{{$stat->ip}}/json" target="blanck">{{$stat->ip}}</a></th>
                    <td style="text-align: center"><a href="https://restcountries.eu/rest/v2/alpha/{{$stat->country}}" target="blanck">{{$stat->country_name}} [{{$stat->country}}]</a></td>
                    <td style="text-align: center">{{$stat->city}}</td>
                    <td style="text-align: center">{{$stat->region}}</td>
                    <td>
            <li style="color: red"><b>With Errors:</b> {{$stat->total_c_error}}</li>
            <li style="color: green"><b>Without Errors:</b> {{($stat->total_c-$stat->total_c_error)}}</li>
            <li><b>Total:</b> {{$stat->total_c}}</li>
            </td>
            <td style="text-align: center">
                @if($stat->is_finished == '1')
                Yes
                @else
                No
                @endif
            </td>
            <td style="text-align: center">{{$stat->created_at}}</td>
            <td style="text-align: center">{{$stat->updated_at}}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row" align='center'>
        @if($app_version_less_2)
        {!!$ips->appends(request()->all())->render()!!}
        @else
        {{$ips->appends(request()->all())->links()}}
        @endif
    </div>
</div>

@endsection

