<div class="form-group {{ $errors->has('customer_id') ? 'has-error' : ''}}">
    <label for="customer_id" class="col-md-4 control-label">{{ 'Customer Id' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="customer_id" type="number" id="customer_id" value="{{ $order->customer_id or ''}}" >
        {!! $errors->first('customer_id', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('product_id') ? 'has-error' : ''}}">
    <label for="product_id" class="col-md-4 control-label">{{ 'Product Id' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="product_id" type="number" id="product_id" value="{{ $order->product_id or ''}}" >
        {!! $errors->first('product_id', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('is_sale') ? 'has-error' : ''}}">
    <label for="is_sale" class="col-md-4 control-label">{{ 'Is Sale' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="is_sale" type="number" id="is_sale" value="{{ $order->is_sale or ''}}" >
        {!! $errors->first('is_sale', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('amount') ? 'has-error' : ''}}">
    <label for="amount" class="col-md-4 control-label">{{ 'Amount' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="amount" type="text" id="amount" value="{{ $order->amount or ''}}" >
        {!! $errors->first('amount', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-4 col-md-4">
        <input class="btn btn-primary" type="submit" value="{{ $submitButtonText or 'Create' }}">
    </div>
</div>
