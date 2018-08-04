@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Order {{ $order->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/orders') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/orders/' . $order->id . '/edit') }}" title="Edit Order"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                    <form method="POST" action="{{ url('orders' . '/' . $order->id) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Order" onclick="return confirm( & quot; Confirm delete? & quot; )"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                    </form>
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table table">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $order->id }}</td>
                                </tr>
                                <tr><th> Customer Id </th><td> {{ $order->customer_id }} </td></tr><tr><th> Order Id </th><td> {{ $order->order_id }} </td></tr><tr><th> Is Sale </th><td> {{ $order->is_sale }} </td></tr>
                            </tbody>
                        </table>
                    </div>

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
