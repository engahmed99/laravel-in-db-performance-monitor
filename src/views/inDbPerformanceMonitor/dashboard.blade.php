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
<div class="container">
    <div class="panel panel-custom"> 
        <div class="panel-heading text-center">
            <h2 style="padding: 0px; margin: 0px">Dashboard</h2> 
        </div>
        <div class="panel-body">
            <div style="text-align: center">
                <h3>Total Requests Count by Type</h3>
                <hr/>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center" class="alert-warning">Label</th>
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
                <h3>Total Requests Count by Archive Tag and Type<hr/><span class="label label-danger">With Errors</span> <span class="label label-success">Without Errors</span> <span class="label label-warning">Total</span></h3>
                <br/>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align: center" class="alert-warning">Archive Tag</th>
                            @foreach ($requests_types as $type)
                            <th style="text-align: center" class="alert-warning">{{$type->type}}</th>
                            @endforeach
                            <th style="text-align: center" class="alert-warning">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $total_sum = 0; ?>
                        @foreach ($archive_tags as $archive_tag => $tag_reqs)
                        <tr>
                            <th style="text-align: center;" class="alert-info">{{$archive_tag}}</th>
                            <?php $sum = 0; ?>
                            @foreach ($requests_types as $type)
                            <?php $temp = $tag_reqs->where('type', $type->type); ?>
                            @if(count($temp)>0)
                            <?php $temp = $temp->first(); ?>
                            <?php $sum += $temp->requests_count; ?>
                            <td style="text-align: center;">
                                <span class="" style="color: red">{{$temp->with_errors_count}}</span> +
                                <span class="" style="color: green">{{$temp->with_no_errors_count}}</span> =
                                <span class="" style="font-weight: bold">{{$temp->requests_count}}</span>
                            </td>
                            @else
                            <td style="text-align: center;">
                                <span class="" style="color: red">0</span> +
                                <span class="" style="color: green">0</span> =
                                <span class="" style="font-weight: bold">0</span>
                            </td>
                            @endif
                            @endforeach
                            <?php $total_sum += $sum; ?>
                            <th style="text-align: center" class="alert-info">{{$sum}}</th>
                        </tr>
                        @endforeach
                        <tr>
                            <th style="text-align: center" class="alert-info">Total</th>
                            @foreach ($requests_types as $type)
                            <th style="text-align: center" class="alert-info">{{$type->requests_count}}</th>
                            @endforeach
                            <th style="text-align: center" class="alert-warning">{{$total_sum}}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

