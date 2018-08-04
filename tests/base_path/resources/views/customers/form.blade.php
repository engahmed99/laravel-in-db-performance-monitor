<div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    <label for="name" class="col-md-4 control-label">{{ 'Name' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="name" type="text" id="name" value="{{ $customer->name or ''}}" >
        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
    <label for="email" class="col-md-4 control-label">{{ 'Email' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="email" type="email" id="email" value="{{ $customer->email or ''}}" >
        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('mobile') ? 'has-error' : ''}}">
    <label for="mobile" class="col-md-4 control-label">{{ 'Mobile' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="mobile" type="text" id="mobile" value="{{ $customer->mobile or ''}}" >
        {!! $errors->first('mobile', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
    <label for="address" class="col-md-4 control-label">{{ 'Address' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="address" type="text" id="address" value="{{ $customer->address or ''}}" >
        {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('birth_date') ? 'has-error' : ''}}">
    <label for="birth_date" class="col-md-4 control-label">{{ 'Birth Date' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="birth_date" type="date" id="birth_date" value="{{ $customer->birth_date or ''}}" >
        {!! $errors->first('birth_date', '<p class="help-block">:message</p>') !!}
    </div>
</div><div class="form-group {{ $errors->has('kids_no') ? 'has-error' : ''}}">
    <label for="kids_no" class="col-md-4 control-label">{{ 'Kids No' }}</label>
    <div class="col-md-6">
        <input class="form-control" name="kids_no" type="number" id="kids_no" value="{{ $customer->kids_no or ''}}" >
        {!! $errors->first('kids_no', '<p class="help-block">:message</p>') !!}
    </div>
</div>

<div class="form-group">
    <div class="col-md-offset-4 col-md-4">
        <input class="btn btn-primary" type="submit" value="{{ $submitButtonText or 'Create' }}">
    </div>
</div>
