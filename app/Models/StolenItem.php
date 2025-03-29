<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StolenItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'serial_code',
        'stolen_date_time',
        'phone',
        'country',
        'city',
        'street_address',
        'id_card_image',
        'purchase_location',
        'files',
        'stolen_item_type',
    ];

    protected $casts = [
        'files' => 'array',
        'stolen_date_time' => 'datetime',
    ];
}
