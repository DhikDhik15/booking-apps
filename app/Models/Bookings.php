<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    protected $fillable = [
        'order_id',
        'name',
        'email',
        'phone',
        'booking_date',
        'console_type',
        'total_price',
        'payment_status',
    ];
}
