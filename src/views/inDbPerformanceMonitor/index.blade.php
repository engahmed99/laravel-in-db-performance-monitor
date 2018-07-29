@extends('inDbPerformanceMonitor::layout')

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="panel panel-warning"> 
        <div class="panel-heading text-center">
            <h2 style="">Login to Admin Monitor</h2> 
        </div>
        <div class="panel-body">
            <form class="" method="post" style="text-align: center">
                <div class="form-group row">
                    {{ csrf_field() }}
                    <div class="col-sm-2"></div>
                    <label for="inputPassword" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required="">
                    </div>
                    <div class="col-sm-2">
                        <input type="submit" class="btn btn-primary" value="Login"/>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

