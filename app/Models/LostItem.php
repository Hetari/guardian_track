<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LostItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'serial_code',
        'lost_date_time',
        'phone',
        'country',
        'city',
        'street_address',
        'id_card_image',
        'purchase_location',
        'files',
        'lost_item_type',
    ];

    protected $casts = [
        'files' => 'array',
        'lost_date_time' => 'datetime',
    ];
}
