<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            foreach ($consignmentIds as $key => $consignmentId) {
                # code...
                $booking->update([
                    'pathao_consignment_ids' => $consignmentId, // Store as JSON array
                    'courier_status'         => 'pending',
                    'courier_service'        => 'pathao',
                ]);
            }

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

    public function invoice($pathao_consignment_id)
    {
        $data = PathaoCourier::VIEW_ORDER($pathao_consignment_id);
        $order = $data['data'];
        return view('admin.courier-services.invoice', compact('order'));
    }
}
