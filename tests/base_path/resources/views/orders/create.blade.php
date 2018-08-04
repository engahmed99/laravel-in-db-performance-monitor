@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Create New Order</div>
                <div class="card-body">
                    <a href="{{ url('/orders') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <br />
                    <br />

                    @if ($errors->any())
                    <ul class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @endif

                    <form method="POST" action="{{ url('/orders') }}" accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        @include ('orders.form')

                    </form>

                    <script>
                        $(function () {
                            // Submit ajax
                            $('form').submit(function (e) {
                                e.preventDefault();
                                var form = $(this).serializeArray();
                                var formObject = {};
                                $.each(form, function (i, v) {
                                    formObject[v.name] = v.value;
                                });

                                $.ajax({
                                    method: $(this).attr('method'),
                                    url: $(this).attr('action'),
                                    data: formObject
                                }).done(function (msg) {
                                    window.location = "{{url('orders')}}";
                                });
                                return false;
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
