@extends('inDbPerformanceMonitor::layout')

@section('content')
<div class="container">
    <div class="panel panel-warning"> 
        <div class="panel-heading text-center">
            <h2 style="padding: 0px; margin: 0px">Change Password</h2> 
        </div>
        <div class="panel-body">
            <form class="" method="post" style="text-align: center">
                <div class="form-group row">
                    {{ csrf_field() }}
                    <div class="col-sm-2"></div>
                    <label for="password" class="col-sm-2 col-form-label">Password</label>
                    <div class="col-sm-4">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Old Password" required="">
                        <br/>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="New Password" required="">
                        <br/>
                        <input type="password" class="form-control" id="new_password_confirmed" name="new_password_confirmed" placeholder="Confirm Password" required="">
                    </div>
                    <div class="col-sm-2">
                        <br/><br/><br/><br/><br/><br/>
                        <input type="submit" class="btn btn-primary" value="Change Password"/>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

