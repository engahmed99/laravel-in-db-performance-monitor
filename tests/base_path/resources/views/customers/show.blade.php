@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-9">
            <div class="card">
                <div class="card-header">Customer {{ $customer->id }}</div>
                <div class="card-body">

                    <a href="{{ url('/customers') }}" title="Back"><button class="btn btn-warning btn-sm"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                    <a href="{{ url('/customers/' . $customer->id . '/edit') }}" title="Edit Customer"><button class="btn btn-primary btn-sm"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</button></a>

                    <form method="POST" action="{{ url('customers' . '/' . $customer->id) }}" accept-charset="UTF-8" style="display:inline">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-danger btn-sm" title="Delete Customer" onclick="return confirm( & quot; Confirm delete? & quot; )"><i class="fa fa-trash-o" aria-hidden="true"></i> Delete</button>
                    </form>
                    <br/>
                    <br/>

                    <div class="table-responsive">
                        <table class="table table">
                            <tbody>
                                <tr>
                                    <th>ID</th><td>{{ $customer->id }}</td>
                                </tr>
                                <tr><th> Name </th><td> {{ $customer->name }} </td></tr><tr><th> Email </th><td> {{ $customer->email }} </td></tr><tr><th> Mobile </th><td> {{ $customer->mobile }} </td></tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            <br/><br/>
            <div>
                <form method="POST" action="http://asamir.local/asamir/public/customers/1" accept-charset="UTF-8" enctype="multipart/form-data" class="form-horizontal">
                    <input type="hidden" name="_method" value="PATCH"> 
                    <input type="hidden" name="_token" value="VN2wyOnMo7WXL0QujadzaaxMjcpfBCZ4ggeD4uSG"> 
                    <div class="form-group ">
                        <label for="name" class="col-md-4 control-label">Product</label> 
                        <div class="col-md-6">
                            <input name="name" type="text" id="name" value="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group ">
                        <label for="name" class="col-md-4 control-label">Company</label> 
                        <div class="col-md-6">
                            <input name="name" type="text" id="name" value="" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-4">
                            <input type="submit" value="Add" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
            <table class="table table-bordered">
                <tr>
                    <th>#</th>
                    <th>Product name</th>
                    <th>Product company</th>
                    <th>Price</th>
                    <th>Transaction->date</th>
                    <th><a href="#" class="btn btn-success btn-sm" id="add-product-btn">Add</a></th>
                </tr>
                @foreach ($customer->orders as $i=>$order)
                <tr>
                    <td>{{($i+1)}}</td>
                    <td>{{$order->product->name}}</td>
                    <td>{{$order->product->company}}</td>
                    <td>{{$order->product->price}}</td>
                    <td>{{$order->created_at}}</td>
                    <td><a href="#" class="btn btn-warning btn-sm" id="add-product-btn">Edit</a> <a href="#" class="btn btn-danger btn-sm" id="add-product-btn">Delete</a></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>

</div>
@endsection
