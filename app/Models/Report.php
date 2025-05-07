<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'customer_name',
        'serial_code',
        'date_time',
        'country',
        'city',
        'street_address',
        'item_type',
        'status',
        'company_id',
        'lost_ownership_document',
    ];
    protected $casts = [
        'date_time' => 'datetime',
        'lost_ownership_document' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(PartnerCompany::class, 'company_id');
    }
    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }
}
