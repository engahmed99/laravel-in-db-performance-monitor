<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ads extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ads';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $connection = 'ads_connection';

}
