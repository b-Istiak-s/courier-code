<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignCourierController extends Controller
{
    public function index(Request $request)
    {
        $bookings = Booking::with([
            'store',
            'merchant',
            'bookingOperator',
            'productType',
            'deliveryType',
            'products.product'   // nested eager loading
        ])
            ->where('merchant_id', Auth::id())
            ->paginate(8);

        return view('admin.courier-services.index', compact('bookings'));
    }

    public function order(Request $request)
    {
        $bookings = Booking::with([
            'store',
            'merchant',
            'bookingOperator',
            'productType',
            'deliveryType',
            'products.product'   // nested eager loading
        ])
            ->findOrFail($request->booking_id);



        dd($bookings);

        // $data = [
        //     'store_id'             => $request->store_id,
        //     'merchant_order_id'    => $request->merchant_order_id,
        //     'sender_name'          => $request->sender_name,
        //     'sender_phone'         => $request->sender_phone,
        //     'recipient_name'       => $bookings["recipient_name"],
        //     'recipient_phone'      => $bookings["recipient_phone"],
        //     'recipient_address'    => $bookings["recipient_address"],
        //     'recipient_city'       => $bookings["recipient_city"],
        //     'recipient_zone'       => $request->recipient_zone,
        //     'recipient_area'       => $request->recipient_area,
        //     'delivery_type'        => $request->delivery_type,   // 48 = Normal, 12 = On Demand
        //     'item_type'            => $request->item_type,       // 1 = Document, 2 = Parcel
        //     'special_instruction'  => $request->special_instruction,
        //     'item_quantity'        => $request->item_quantity,
        //     'item_weight'          => $request->item_weight,
        //     'amount_to_collect'    => $request->amount_to_collect,
        //     'item_description'     => $request->item_description,
        // ];
    }
}
