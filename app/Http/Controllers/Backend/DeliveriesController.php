<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CourierStore;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveriesController extends Controller
{
    public function all(Request $request)
    {
        $user_id = Auth::user()->id;
        $counts = 0;

        if (Auth::user()->role == "Admin") {
            $counts = Booking::where('pathao_consignment_ids', '!=', null)->count();
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.all', compact('bookings', 'courierStores', 'counts'));
        } else {
            $counts = Booking::where('pathao_consignment_ids', '!=', null)->where('merchant_id', '=', $user_id)->count();
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->where('merchant_id', $user_id)
                ->where('pathao_consignment_ids', '!=', null)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.all', compact('bookings', 'courierStores', 'counts'));
        }
    }

    public function active(Request $request)
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "Booking Operator") {
            $user_id = Auth::user()->user_id;
        }

        if (Auth::user()->role == "Admin") {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '!=', "Pickup Cancel")
                ->where('courier_status', '!=', "Delivered")
                ->where('courier_status', '!=', "Returned")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.active', compact('bookings', 'courierStores'));
        } else {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->where('merchant_id', $user_id)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '!=', "Pickup Cancel")
                ->where('courier_status', '!=', "Delivered")
                ->where('courier_status', '!=', "Returned")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.active', compact('bookings', 'courierStores'));
        }
    }

    public function delivered(Request $request)
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "Booking Operator") {
            $user_id = Auth::user()->user_id;
        }

        if (Auth::user()->role == "Admin") {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '=', "Delivered")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.delivered', compact('bookings', 'courierStores'));
        } else {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->where('merchant_id', $user_id)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '=', "Delivered")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.delivered', compact('bookings', 'courierStores'));
        }
    }

    public function returned(Request $request)
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "Booking Operator") {
            $user_id = Auth::user()->user_id;
        }

        if (Auth::user()->role == "Admin") {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '=', "Returned")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.returned', compact('bookings', 'courierStores'));
        } else {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->where('merchant_id', $user_id)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '=', "Returned")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.returned', compact('bookings', 'courierStores'));
        }
    }

    public function cancelled(Request $request)
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "Booking Operator") {
            $user_id = Auth::user()->user_id;
        }

        if (Auth::user()->role == "Admin") {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '=', "Pickup Cancel")
                ->orWhere('courier_status', '=', "Cancelled")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.cancelled', compact('bookings', 'courierStores'));
        } else {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->where('merchant_id', $user_id)
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('courier_status', '=', "Pickup Cancel")
                ->orWhere('courier_status', '=', "Cancelled")
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.cancelled', compact('bookings', 'courierStores'));
        }
    }



    public function invoice(Request $request)
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "Booking Operator") {
            $user_id = Auth::user()->user_id;
        }

        if (Auth::user()->role == "Admin") {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.invoices', compact('bookings', 'courierStores'));
        } else {
            $bookings = Booking::with([
                'store',
                'Merchant',
                'bookingOperator',
                'productType',
                'deliveryType',
                'products.product'   // nested eager loading
            ])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
                })
                ->where('pathao_consignment_ids', '!=', null)
                ->where('merchant_id', $user_id)
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            $courierStores = CourierStore::get();

            return view('admin.deliveries.invoices', compact('bookings', 'courierStores'));
        }
    }


    public function invoicePdf($orderId)
    {
        $booking = Booking::with([
            'store',
            'Merchant',
            'bookingOperator',
            'productType',
            'deliveryType',
            'products.product'
        ])->where('order_id', $orderId)->first();

        // dd($booking);
        $pdf = Pdf::loadView('admin.deliveries.invoice-pdf', compact('booking'));
        return $pdf->stream('Invoice_' . $booking->order_id . '.pdf');
    }
}
