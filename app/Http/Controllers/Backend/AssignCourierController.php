<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
            })
            ->orderBy('id', 'desc')
            ->paginate(8)
            ->withQueryString();



        // $bookings = DB::table('bookings')
        //     ->leftJoin('stores', 'bookings.store_id', '=', 'stores.id')
        //     ->leftJoin('merchants', 'bookings.merchant_id', '=', 'merchants.id')
        //     ->leftJoin('users as booking_operators', 'bookings.booking_operator_id', '=', 'booking_operators.id')
        //     ->leftJoin('product_types', 'bookings.product_type_id', '=', 'product_types.id')
        //     ->leftJoin('delivery_types', 'bookings.delivery_type_id', '=', 'delivery_types.id')
        //     ->leftJoin('booking_products', 'booking_products.booking_id', '=', 'bookings.id')
        //     ->leftJoin('products', 'booking_products.product_id', '=', 'products.id')
        //     ->select(
        //         'bookings.*',
        //         'stores.name as store_name',
        //         'merchants.name as merchant_name',
        //         'booking_operators.name as booking_operator_name',
        //         'product_types.name as product_type_name',
        //         'delivery_types.name as delivery_type_name',
        //         'booking_products.id as booking_product_id',
        //         'booking_products.quantity as booking_product_quantity',
        //         'products.name as product_name',
        //         'products.weight as product_weight'
        //     )
        //     ->when($request->filled('search'), function ($query) use ($request) {
        //         $query->where('bookings.order_id', 'like', '%' . $request->search . '%');
        //     })
        //     ->where('bookings.merchant_id', Auth::id())
        //     ->orderByDesc('bookings.id')
        //     ->paginate(8)
        //     ->withQueryString();

        return view('admin.courier-services.index', compact('bookings'));
    }

    public function order(Request $request)
    {
        $booking = Booking::with([
            'store',
            'merchant',
            'bookingOperator',
            'productType',
            'deliveryType',
            'products.product' // nested eager loading
        ])->findOrFail($request->booking_id);

        // Prepare bulk order data - create one order per product in the booking
        $bulkOrders = [];

        $weight = 0;
        $item_quantity = 0;

        foreach ($booking->products as $index => $bookingProduct) {
            $weight        += $bookingProduct->weight;
            $item_quantity += $bookingProduct->quantity;
        }

        //// Delete this
        $bulkOrders[] = [
            'store_id'            => $booking->store->pathao_store_id ?? 345173, // Pathao store ID from database
            'merchant_order_id'   => $booking->order_id,
            'sender_name'         => $booking->merchant->name ?? 'Merchant',
            'sender_phone'        => $booking->merchant->phone ?? '01700000000',
            'recipient_name'      => $booking->recipient_name,
            'recipient_phone'     => $booking->recipient_phone,
            'recipient_address'   => $booking->recipient_address,
            'recipient_city'      => (int) $booking->city_id,
            'recipient_zone'      => (int) $booking->zone_id,
            'recipient_area'      => (int) $booking->area_id,
            'delivery_type'       => $booking->delivery_type_id ?? 48,
            'item_type'           => $booking->product_type_id ?? 2,
            'special_instruction' => $booking->special_instruction ?? null,
            'item_quantity'       => (int) $item_quantity,
            'item_weight'         => $weight,
            'amount_to_collect'   => $booking->amount_to_collect ?? 0,
            'item_description'    => $booking->item_description,
        ];

        try {
            $consignmentIds = [];
            $failedOrders = [];

            // Create individual orders for each product
            foreach ($bulkOrders as $index => $orderData) {
                try {
                    // Create PathaoOrderRequest for each order
                    $pathaoOrderRequest = new \Enan\PathaoCourier\Requests\PathaoOrderRequest();
                    $pathaoOrderRequest->merge($orderData);

                    // Create order in Pathao
                    $pathaoResponse = PathaoCourier::CREATE_ORDER($pathaoOrderRequest);

                    Log::info("Pathao Order {$index} Creation Response: ", $pathaoResponse);

                    // Check if order creation was successful
                    if (isset($pathaoResponse['data']['data']['consignment_id'])) {
                        $consignmentIds[] = $pathaoResponse['data']['data']['consignment_id'];
                    } else {
                        $failedOrders[] = $orderData['merchant_order_id'];
                        Log::error("Failed to create order {$orderData['merchant_order_id']}: ", $pathaoResponse);
                    }
                } catch (\Exception $e) {
                    $failedOrders[] = $orderData['merchant_order_id'];
                    Log::error("Exception creating order {$orderData['merchant_order_id']}: " . $e->getMessage());
                }
            }

            // If all orders failed
            if (empty($consignmentIds)) {
                return back()->with('error', 'Failed to create any orders in Pathao. Please check logs.');
            }

            // Update booking record with Pathao consignment details
            $booking->update([
                'pathao_consignment_ids' => json_encode($consignmentIds), // Store as JSON array
                'courier_status'         => 'pending',
                'courier_service'        => 'pathao',
            ]);

            $successMessage = count($consignmentIds) . ' order(s) created successfully! Consignments: ' . implode(', ', $consignmentIds);

            if (!empty($failedOrders)) {
                $successMessage .= ' | Failed: ' . implode(', ', $failedOrders);
            }

            return back()->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Pathao Order Creation Error: ' . $e->getMessage());
            return back()->with('error', 'Pathao API error: ' . $e->getMessage());
        }
    }
}
