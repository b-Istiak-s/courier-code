<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\SetupCharge;
use Illuminate\Http\Request;

class SetupChargeController extends Controller
{
    public function index()
    {
        $setupchargers = SetupCharge::first();

        return view('admin.setup-charges.create', compact('setupchargers'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'fulfilment_fee'   => 'nullable|numeric',
            'delivery_charges' => 'nullable|numeric',
            'cod_fee'          => 'nullable|numeric',
            'product_charges'  => 'nullable|numeric',
        ]);

        SetupCharge::updateOrCreate(
            ['id' => 1], // fixed single row
            [
                'fulfilment_fee'   => $request->fulfilment_fee,
                'delivery_charges' => $request->delivery_charges,
                'cod_fee'          => $request->cod_fee,
                'product_charges'  => $request->product_charges,
            ]
        );

        return back()->with('success', 'Setup Charges Saved Successfully');
    }
}
