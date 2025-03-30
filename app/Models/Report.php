<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'product_name',
        'email',
        'serial_code',
        'date_time',
        'country',
        'city',
        'street_address',
        'purchase_location',
        'item_type',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
}
