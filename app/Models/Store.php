<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'merchant_id',
        'name',
        'email',
        'owner_phone',
        'primary_phone',
        'address',
        'logo',
        'city',
        'zone',
        'area',
        'status',
        'pathao_store_id',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Merchant owner
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    // Bookings of this store
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
