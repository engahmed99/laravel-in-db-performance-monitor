<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'orders';

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
    protected $fillable = ['customer_id', 'product_id', 'is_sale', 'amount'];

    public function customer() {
        return $this->hasOne('App\Customer', 'customer_id');
    }

    public function product() {
        return $this->hasOne('App\Product', 'product_id');
    }

}
