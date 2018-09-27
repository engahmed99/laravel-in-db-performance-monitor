<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Admin Monitor | @yield('title')</title>

        <!-- Scripts -->
        <script src="{{ asset('js/inDbPerformanceMonitor.js') }}"></script>
        <script>
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    // Animate textareas
    $("textarea").not('.query-textarea, .bind-textarea').focus(function () {
        $(this).attr("style", "");
        $(this).attr('data-cols', $(this)[0].cols);
        $(this).attr('data-rows', $(this)[0].rows);
        var cols = $(this)[0].cols;
        var lines_c = 0;
        $.each($(this).val().split("\n"), function (i, l) {
            if (l.length <= cols)
                lines_c++;
            else
                lines_c += Math.ceil(l.length / cols); // Take into account long lines
        });
        $(this).animate({
            'rows': lines_c
        }, 500);
    });
    //--------
    $("textarea").not('.query-textarea, .bind-textarea').blur(function () {
        $(this).animate({
            'cols': $(this).attr('data-cols'),
            'rows': $(this).attr('data-rows')
        }, 500);
    });

});
        </script>
        @stack('scripts')

        <!-- Styles -->
        <link href="{{ asset('css/inDbPerformanceMonitor.css') }}" rel="stylesheet">
        @stack('styles')

    </head>
    <body>
        <div id="app">
            <br/>
            <div class="container">

                @if(session('__asamir_token'))
                <nav class="navbar navbar-default" style=""> 
                    <div class="container-fluid"> 
                        <div class="navbar-header"> 
                            <button type="button" class="collapsed navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-9" aria-expanded="false"> 
                                <span class="sr-only">Toggle navigation</span> 
                                <span class="icon-bar"></span> 
                                <span class="icon-bar"></span> 
                                <span class="icon-bar"></span> 
                            </button> 
                            <a href="{{url('')}}" class="navbar-brand">Admin Monitor</a> 
                        </div> 
                        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-9"> 
                            <ul class="nav navbar-nav"> 
                                <li @if(request()->getPathInfo() == '/admin-monitor/dashboard') class="active" @endif><a href="{{url('admin-monitor/dashboard')}}" class="">Dashboard</a></li>
                                <li @if(request()->getPathInfo() == '/admin-monitor/requests') class="active" @endif><a href="{{url('admin-monitor/requests')}}" class="">Requests List</a></li>
                                <li @if(request()->getPathInfo() == '/admin-monitor/statistics-report') class="active" @endif><a href="{{url('admin-monitor/statistics-report')}}" class="">Statistics Report</a></li>
                                <li @if(request()->getPathInfo() == '/admin-monitor/errors-report') class="active" @endif><a href="{{url('admin-monitor/errors-report')}}" class="">Errors Report</a></li>
                                <li @if(substr(request()->getPathInfo(), 0, 23) == '/admin-monitor/request/') class="active" @endif role="presentation" class="dropdown">
                                     <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                        Latest By <span class="caret"></span>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{url('admin-monitor/request/-1')}}" class="">Session ID</a></li>
                                        <li><a href="{{url('admin-monitor/request/-2')}}" class="">IP</a></li>
                                    </ul>
                                </li>
                                <li @if(request()->getPathInfo() == '/admin-monitor/change-password') class="active" @endif><a href="{{url('admin-monitor/change-password')}}" class="">Change Password</a></li>
                                <li><a href="{{url('admin-monitor/logout')}}" class="">Exit</a></li>
                            </ul> 
                        </div> 
                    </div> 
                </nav>
                @endif
                <div class="flash-message">
                    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
                    @if(session('alert-' . $msg))
                    <p class="alert alert-{{ $msg }}">{{ session('alert-' . $msg) }}</p>
                    @endif
                    @endforeach
                </div>
                @if(session('__asamir_token'))
                <div style="padding: 0px; margin: 0px">
                    <h5 style="text-align: center;"><span class="label label-warning">Your Session ID = {{session()->getId()}}</span> - <span class="label label-warning">Your IP = {{request()->ip()}} </span></h5>
                </div>
                @endif
            </div>
            @yield('content')
            <div class="container">
                <div style="text-align: center; padding: 5px; bottom: 0px; width: 100%" class="alert-success">
                    <h5>© {{date('Y')}} Copyrights: <a href="#">Ahmed Samir</a> | <a href="https://github.com/engahmed99/laravel-in-db-performance-monitor">GitHub</a></h5>
                </div>
            </div>
            <br/>
            <br/>
        </div>
    </body>
</html>
