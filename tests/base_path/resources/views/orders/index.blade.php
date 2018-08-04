@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Orders</div>
                <div class="card-body">
                    <a href="{{ url('/orders/create') }}" class="btn btn-success btn-sm" title="Add New Order">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add New
                    </a>

                    <form method="GET" action="{{ url('/orders') }}" accept-charset="UTF-8" class="form-inline my-2 my-lg-0 float-right" role="search">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search..." value="{{ request('search') }}">
                            <span class="input-group-append">
                                <button class="btn btn-secondary" type="submit">
                                    <i class="fa fa-search"></i>
                                </button>
                            </span>
                        </div>
                    </form>

                    <br/>
                    <br/>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th><th>Customer Id</th><th>Product Id</th><th>Is Sale</th><th>Ammount</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $item)
                                <tr>
                                    <td>{{ $loop->iteration or $item->id }}</td>
                                    <td>{{ $item->customer_id }}</td><td>{{ $item->product_id }}</td><td>{{ $item->is_sale }}</td><td>{{ $item->amount }}</td>
                                    <td>
                                        <a href="{{ url('/orders/' . $item->id) }}" title="View Order"><button class="btn btn-info btn-xs"><i class="fa fa-eye" aria-hidden="true"></i> View</button></a>
                                        <a href="{{ url('/orders/' . $item->id . '/edit') }}" title="Edit Order"><button class="btn btn-primary btn-xs"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                                        <form method="POST" action="{{ url('/orders' . '/' . $item->id) }}" accept-charset="UTF-8" style="display:inline">
                                            {{ method_field('DELETE') }}
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-danger btn-xs" title="Delete Order" onclick="return confirm( & quot; Confirm delete? & quot; )"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="pagination-wrapper"> {!! $orders->appends(['search' => Request::get('search')])->render() !!} </div>
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
