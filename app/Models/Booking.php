<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'merchant_id',
        'booking_operator_id',
        'store_id',
        'product_type',
        'delivery_type',
        'recipient_name',
        'recipient_phone',
        'recipient_secondary_phone',
        'recipient_address',
        'city_id',
        'zone_id',
        'area_id',
        'status',
    ];

    // Merchant who created the booking
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    // Booking operator assigned
    public function bookingOperator()
    {
        return $this->belongsTo(User::class, 'booking_operator_id');
    }

    // The store associated with the booking
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    // Products associated with this booking
    public function products()
    {
        return $this->hasMany(BookingProduct::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function deliveryType()
    {
        return $this->belongsTo(DeliveryType::class);
    }
}
