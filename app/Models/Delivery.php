<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_group',
        'driver_id',
        'status',
    ];

    /**
     * Get the driver (user) assigned to the delivery.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Get the orders associated with this delivery.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'order_group', 'order_group');
    }
}
