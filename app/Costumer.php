<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Costumer extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'chat_id',
    ];

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
