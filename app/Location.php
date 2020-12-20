<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'latitude',
        'longitude',
        'order_id'
    ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }
}
