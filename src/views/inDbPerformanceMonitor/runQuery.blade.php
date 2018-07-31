@extends('inDbPerformanceMonitor::layout')

@section('title', 'Run Query')

@push('styles')
<style>
    .panel-custom>.panel-heading {
        color: #fff;
        background-color: #cb79e6 !important;
        border-color: #cb79e6 !important;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="panel panel-custom"> 
        <div class="panel-heading text-center"> 
            <h2 style="padding: 0px; margin: 0px">Run Query => ID = {{$logQuery->id}}</h2> 
        </div> 
        <div class="panel-body">
            <a href="{{url('admin-monitor/request/'.$logQuery->request_id)}}" style="left: 0px; display: inline">Return to Request</a>
            <table class="table table-hover table-striped table-bordered">
                <thead>
                    <tr>
                        <th style="text-align: center">ID</th>
                        <th style="text-align: center">Creation Date</th>
                        <th>Query</th>
                        <th>Bindings</th>
                        <th style="text-align: center">Time</th>
                        <th style="text-align: center">Connection Name</th>
                        <th style="text-align: center">Is Elequent</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center">{{$logQuery->id}}</td>
                        <td style="text-align: center">{{$logQuery->created_at}}</td>
                        <td><textarea readonly="" class="form-control">{{$logQuery->query}}</textarea></td>
                        <td><textarea readonly="" class="form-control">{{$logQuery->bindings}}</textarea></td>
                        <td style="text-align: center">
                            {{$logQuery->time}} ms<br/>
                            {{($logQuery->time/1000)}} s
                        </td>
                        <td style="text-align: center">{{$logQuery->connection_name}}</td>
                        <td style="text-align: center">{{$logQuery->is_elequent}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel panel-success"> 
        <div class="panel-heading text-center"> 
            <h2 class="panel-title">Query Result - Script executed in {{$exec_time}} s, Returned {{count($results)}} records</h2> 
        </div> 
        <div class="panel-body">
            @if(is_array($results))
            @foreach ($results as $i=>$res)
            <p>
                <button class="btn btn-success" type="button" data-toggle="collapse" data-target="#res-{{($i+1)}}" aria-expanded="@if($i==0){{'true'}}@else{{'false'}}@endif" aria-controls="res-{{($i+1)}}" style="width: 100%">
                    Toggle row #{{($i+1)}} / {{sizeof($results)}}
                </button>
            </p>
            <div class="@if($i==0){{''}}@else{{'collapse'}}@endif" id="res-{{($i+1)}}">
                <div class="card card-body">
                    <pre>{{print_r($res)}}</pre>
                </div>
            </div>
            @endforeach
            @else
            <div class="card card-body">
                <pre>{{var_dump($results)}}</pre>
            </div>
            @endif
        </div>
    </div>

    <div class="panel panel-danger"> 
        <div class="panel-heading text-center"> 
            <h2 class="panel-title">Query Exception</h2> 
        </div> 
        <div class="panel-body">
            <table class="table table-hover table-striped table-bordered">
                @if($exception)
                <tr>
                    <th>Message</th><td>{{$exception->getMessage()}}</td>
                </tr>
                @else
                <tr><td>The query has no exception</td></tr>
                @endif
            </table>
        </div>
    </div>

</div>

@endsection

