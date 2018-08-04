<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'customers';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'mobile', 'address', 'birth_date', 'kids_no'];

    public function Orders() {
        return $this->hasMany('App\Order', 'customer_id');
    }

    public function products() {
        return $this->belongsToMany('App\Product', 'orders', 'customer_id', 'product_id');
    }

}
