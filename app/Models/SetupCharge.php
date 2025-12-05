<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetupCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'fulfilment_fee',
        'product_charges',
        'delivery_charges',
        'cod_fee',
    ];
}
