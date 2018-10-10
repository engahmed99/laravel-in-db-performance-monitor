@extends('inDbPerformanceMonitor::layout')

@section('title', 'Dashboard')

@push('styles')
<style> 
    .panel-custom>.panel-heading {
        color: #fff;
        background-color: #d08166 !important;
        border-color: #d08166 !important;
    }

    /*    .btn.btn-lblue {
            color: #ffffff;
            background: #32c8de;
            border: 1px solid #1faabe;
        }
        .btn.btn-round {
            width: 80px;
            height: 80px;
            border-radius: 100%;
            font-size: 40px;
            font-weight: bold;
            text-align: center;
        }*/
</style>
@endpush

@section('content')
<form method="get" action="" id="dashboard-form">    
    <div class="container">
        <div class="panel panel-custom"> 
            <div class="panel-heading text-center">
                <h2 style="padding: 0px; margin: 0px">Dashboard</h2> 
            </div>
            <div class="panel-body">
                <div style="text-align: center">
                    <h3>Total Requests Count by Type
                        <select id="archive-select" name="archive" class="form-control"  value="{{request('archive')}}" style="width: auto; display: inline-block">
                            <option value="">All Archives</option>
                            @foreach($archives as $arc)
                            @if($arc->archive_tag == request('archive'))
                            <option value="{{$arc->archive_tag}}" selected>{{$arc->archive_tag}}</option>                    
                            @else
                            <option value="{{$arc->archive_tag}}">{{$arc->archive_tag}}</option>                    
                            @endif
                            @endforeach
                        </select>
                    </h3>
                    <hr/>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="" class="alert-warning">Label</th>
                                @foreach ($requests_types as $type)
                                <th style="text-align: center" class="alert-warning">{{$type->type}}</th>
                                @endforeach
                                <th style="text-align: center" class="alert-warning">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_with_errors = 0; ?>
                            <?php $total_without_errors = 0; ?>
                            <?php $total_sum = 0; ?>
                            <tr>
                                <th class="alert-danger">With Errors:</th>
                                @foreach ($requests_types as $type)
                                <?php $total_with_errors += $type->with_errors_count; ?>
                                <td style="text-align: center; color: red">{{$type->with_errors_count}}</td>
                                @endforeach
                                <th style="text-align: center" class="alert-danger">{{$total_with_errors}}</th>
                            </tr>
                            <tr>
                                <th class="alert-success">Without Errors:</th>
                                @foreach ($requests_types as $type)
                                <?php $total_without_errors += $type->with_no_errors_count; ?>
                                <td style="text-align: center; color: green">{{$type->with_no_errors_count}}</td>
                                @endforeach
                                <th style="text-align: center" class="alert-success">{{$total_without_errors}}</th>
                            </tr>
                            <tr>
                                <th class="alert-info">Total:</th>
                                @foreach ($requests_types as $type)
                                <?php $total_sum += $type->requests_count; ?>
                                <th style="text-align: center" class="alert-info">{{$type->requests_count}}</tH>
                                @endforeach
                                <th style="text-align: center" class="alert-warning">{{$total_sum}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <hr/>
                <div style="text-align: center">
                    <h3>Total Requests Count by Countries</h3>
                    <hr/>
                    <h5>We have <span style="color:red">{{$not_finished_c}}</span> IPs without country information</h5>
                    <br/>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="" class="alert-warning">Country</th>
                                <th style="text-align: center" class="alert-warning">Total Users</th>
                                <th style="text-align: center" class="alert-success">Without Error</th>
                                <th style="text-align: center" class="alert-danger">With Error</th>
                                <th style="text-align: center" class="alert-warning">Total Requests</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $total_c = 0; ?>
                            <?php $total_sum = 0; ?>
                            <?php $total_sum_error = 0; ?>
                            @foreach ($req_countries as $country)
                            <?php $total_c += $country->req_total_c; ?>
                            <?php $total_sum += $country->req_total_sum; ?>
                            <?php $total_sum_error += $country->req_total_sum_error; ?>
                            <tr>
                                <th style="" class="alert-info">{{$country->country_name}} [{{$country->country}}]</th>
                                <td style="text-align: center" class="">{{$country->req_total_c}}</td>
                                <td style="text-align: center" class="alert-success">{{$country->req_total_sum - $country->req_total_sum_error}}</td>
                                <td style="text-align: center" class="alert-danger">{{$country->req_total_sum_error}}</td>
                                <td style="text-align: center" class="alert-warning">{{$country->req_total_sum}}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <th style="" class="alert-warning">Total</th>
                                <th style="text-align: center" class="alert-info">{{$total_c}}</th>
                                <th style="text-align: center" class="alert-success">{{$total_sum-$total_sum_error}}</th>
                                <th style="text-align: center" class="alert-danger">{{$total_sum_error}}</th>
                                <th style="text-align: center" class="alert-info">{{$total_sum}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(function () {
        $("#archive-select").change(function () {
            $("#dashboard-form").submit();
        });
    });
</script>
@endsection

