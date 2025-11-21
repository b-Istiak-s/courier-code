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
            ->orderBy('id', 'desc')
            ->paginate(8);

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
            'products.product'   // nested eager loading
        ])
            ->findOrFail($request->booking_id);

        // Prepare bulk order data - create one order per product in the booking
        $bulkOrders = [];

        foreach ($booking->products as $index => $bookingProduct) {
            $product = $bookingProduct->product;

            // Build item description from product details
            $itemDescription = $product->name ?? 'Product';
            if ($product->category) {
                $itemDescription .= ' - ' . $product->category;
            }

            $bulkOrders[] = [
                // 'store_id' => $booking->store->pathao_store_id, // Pathao store ID from database
                'store_id' => 345173, // Pathao store ID from database
                'merchant_order_id' => $booking->order_id . '-' . ($index + 1), // Unique order ID per product
                'sender_name' => $booking->merchant->name ?? 'Merchant', // Store/Sender name
                'sender_phone' => $booking->merchant->phone ?? '01700000000', // Store/Sender phone

                'recipient_name' => $booking->recipient_name, // Customer name
                'recipient_phone' => $booking->recipient_phone, // Customer phone
                'recipient_address' => $booking->recipient_address, // Delivery address

                'recipient_city' => (int) $booking->city_id, // City ID // fetched from recipient_address if not provided
                'recipient_zone' => (int) $booking->zone_id, // Zone ID
                'recipient_area' => (int) $booking->area_id, // Area ID

                'delivery_type' => $booking->deliveryType->pathao_delivery_type_id ?? 48, // 48 = Normal, 12 = On Demand
                'item_type' => $booking->productType->pathao_item_type_id ?? 2, // 1 = Document, 2 = Parcel
                'special_instruction' => $booking->special_instruction ?? null, // Optional instructions

                'item_quantity' => $bookingProduct->quantity ?? 1, // Number of items
                'item_weight' => $product->weight ?? 0.5, // Weight in kg
                'amount_to_collect' => $booking->amount_to_collect ?? 0, // Cash to collect (COD amount)
                'item_description' => $itemDescription, // Item description
            ];
        }

        // If no products, create a single order with booking data
        if (empty($bulkOrders)) {
            $bulkOrders[] = [
                'store_id' => $booking->store->pathao_store_id, // Pathao store ID from database
                'merchant_order_id' => $booking->order_id,
                'sender_name' => $booking->merchant->name ?? 'Merchant',
                'sender_phone' => $booking->merchant->phone ?? '01700000000',
                'recipient_name' => $booking->recipient_name,
                'recipient_phone' => $booking->recipient_phone,
                'recipient_address' => $booking->recipient_address,
                'recipient_city' => (int) $booking->city_id,
                'recipient_zone' => (int) $booking->zone_id,
                'recipient_area' => (int) $booking->area_id,
                'delivery_type' => $booking->deliveryType->pathao_delivery_type_id ?? 48,
                'item_type' => $booking->productType->pathao_item_type_id ?? 2,
                'special_instruction' => $booking->special_instruction ?? null,
                'item_quantity' => 1,
                'item_weight' => 0.5,
                'amount_to_collect' => $booking->amount_to_collect ?? 0,
                'item_description' => 'Package',
            ];
        }

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
                'courier_status' => 'pending',
                'courier_service' => 'pathao',
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
