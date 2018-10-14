@extends('inDbPerformanceMonitor::layout')

@section('title', 'Archives Report')

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
            <h2 class="" style="padding: 0px; margin: 0px">Requests Archives Report</h2> 
        </div> 
    </div>

    <div class="alert-info" style="text-align: center"><h4 class=""> {{count($archives)}} Records</h4></div>   

    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th style="text-align: center">#</th>
                    <th style="text-align: center">Archive</th>
                    <th style="text-align: center">Queries Count</th>
                    <th style="text-align: center">Queries Time</th>
                    <th style="text-align: center">Exec Time</th>
                    <th style="text-align: center">Requests Count</th>
                    <th style="text-align: center"></th>                
                </tr>
            </thead>
            <tbody>
                @foreach ($archives as $i => $stat)
                <tr>
                    <th style="text-align: center">{{($i+1)}}</th>
                    <th style="text-align: center">{{$stat->archive_tag}}</th>
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
            <td style="text-align: center">
                @if($stat->archive_tag != '0')
                <form action="{{url('admin-monitor/delete-archive')}}" method="post" id="delete-{{$stat->archive_tag}}-form">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="DELETE">
                    <input type="hidden" name="archive" value="{{$stat->archive_tag}}">
                    <button class="btn btn-danger delete-archive-btn" data-archive="{{$stat->archive_tag}}">Delete Archive</button>
                </form>
                @endif
                <a href="{{url('admin-monitor/request/'.$stat->last_id)}}" class="btn btn-success">Show Last Request</a>
            </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="row" align='center'>

    </div>
</div>

<script>

    $(".delete-archive-btn").click(function (e) {
        e.preventDefault();
        var archive = $(this).attr('data-archive');
        var r = confirm("Are you sure, you want to delete all archive [ " + archive + " ] data???");
        if (r == true) {
            $("#delete-" + archive + "-form").submit();
            return true;
        } else {
            return false;
        }
    });
</script>

@endsection

