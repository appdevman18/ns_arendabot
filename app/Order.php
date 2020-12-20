<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'price',
        'distance',
        'game',
        'duration',
        'type',
        'status',
        'costumer_id'
    ];

    public function costumer()
    {
        return $this->belongsTo('App\Costumer');
    }

    public function location()
    {
        return $this->hasOne('App\Location');
    }
}
