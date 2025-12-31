<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Service\PathaoService;
use App\Models\Booking;
use App\Models\CourierStore;
use App\Models\Invoice;
use App\Models\SetupCharge;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AssignCourierController extends Controller
{
    public function index(Request $request)
    {

        $user_id = Auth::user()->id;

        if (Auth::user()->role == "Booking Operator") {
            $user_id = Auth::user()->user_id;
        }

        $courierStores = CourierStore::get();

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
                ->where('pathao_consignment_ids', '=', null)
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            return view('admin.courier-services.index', compact('bookings', 'courierStores'));
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
                ->where('pathao_consignment_ids', '=', null)
                ->orderBy('id', 'desc')
                ->paginate(8)
                ->withQueryString();

            return view('admin.courier-services.index', compact('bookings', 'courierStores'));
        }
    }

    public function order(Request $request)
    {
        $courierStore = CourierStore::findOrFail($request->courier);
        $booking = Booking::with([
            'store',
            'Merchant',
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
            $weight += $bookingProduct->weight;
            $item_quantity += $bookingProduct->quantity;
        }

        //// Delete this
        $bulkOrders[] = [
            'store_id' => $courierStore->store_id ?? 345173, // Pathao store ID from database
            'merchant_order_id' => $booking->order_id,
            'sender_name' => $booking->merchant->name ?? 'Merchant',
            'sender_phone' => $booking->merchant->phone ?? '01700000000',
            'recipient_name' => $booking->recipient_name,
            'recipient_phone' => $booking->recipient_phone,
            'recipient_address' => $booking->recipient_address,
            // 'recipient_city' => (int) $booking->city_id,
            // 'recipient_zone' => (int) $booking->zone_id,
            // 'recipient_area' => (int) $booking->area_id,
            'delivery_type' => $booking->delivery_type_id ?? 48,
            'item_type' => $booking->product_type_id ?? 2,
            'special_instruction' => $booking->special_instruction ?? null,
            'item_quantity' => (int) $item_quantity,
            'item_weight' => $weight,
            'amount_to_collect' => $booking->amount_to_collect ?? 0,
            'item_description' => $booking->item_description,
        ];

        try {
            $consignmentIds = [];
            $failedOrders = [];

            // Create individual orders for each product
            foreach ($bulkOrders as $index => $orderData) {
                try {
                    // Create order in Pathao using the array data directly
                    $pathaoResponse = (new PathaoService(store: $courierStore))->createOrder($orderData);

                    Log::info("Pathao Order {$index} Creation Response: ", (array) $pathaoResponse);

                    // Check if order creation was successful
                    if ($pathaoResponse->code == 200 && isset($pathaoResponse->data['data']['consignment_id'])) {
                        $consignmentIds[] = $pathaoResponse->data['data']['consignment_id'];
                    } else {
                        $failedOrders[] = $orderData['merchant_order_id'];
                        Log::error("Failed to create order {$orderData['merchant_order_id']}: ", (array) $pathaoResponse);
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
                    'courier_status' => 'pending',
                    'courier_service' => 'pathao',
                ]);


                $data = PathaoCourier::VIEW_ORDER($consignmentId);
                $order = $data['data'];
                
                Invoice::updateOrCreate(
                    [
                        'order_id' => $order['order_id'], // unique key
                        'merchant_id' => 1,
                    ],
                    [

                        'order_consignment_id' => $order['order_consignment_id'] ?? null,
                        'merchant_order_id' => $order['merchant_order_id'] ?? null,

                        'order_created_at' => $order['order_created_at'] ?? null,
                        'order_description' => $order['order_description'] ?? null,
                        'order_status' => $order['order_status'] ?? null,
                        'order_status_updated_at' => $order['order_status_updated_at'] ?? null,

                        'recipient_name' => $order['recipient_name'],
                        'recipient_address' => $order['recipient_address'],
                        'recipient_phone' => $order['recipient_phone'],
                        'recipient_secondary_phone' => $order['recipient_secondary_phone'] ?? null,

                        'customer_city_id' => $order['customer_city_id'] ?? null,
                        'customer_zone_id' => $order['customer_zone_id'] ?? null,
                        'customer_area_id' => $order['customer_area_id'] ?? null,
                        'city_name' => $order['city_name'] ?? null,
                        'zone_name' => $order['zone_name'] ?? null,
                        'area_name' => $order['area_name'] ?? null,

                        'order_amount' => $order['order_amount'] ?? 0,
                        'total_fee' => $order['total_fee'] ?? 0,
                        'promo_discount' => $order['promo_discount'] ?? 0,
                        'discount' => $order['discount'] ?? 0,
                        'cod_fee' => $order['cod_fee'] ?? 0,
                        'additional_charge' => $order['additional_charge'] ?? 0,
                        'compensation_cost' => $order['compensation_cost'] ?? 0,
                        'delivery_fee' => $order['delivery_fee'] ?? 0,

                        'delivery_type' => $order['delivery_type'] ?? null,
                        'total_weight' => $order['total_weight'] ?? 0,
                        'cash_on_delivery' => $order['cash_on_delivery'] ?? null,
                        'order_delivery_hub_id' => $order['order_delivery_hub_id'] ?? null,
                        'delivery_method' => $order['delivery_method'] ?? 0,
                        'delivery_string' => $order['delivery_string'] ?? null,
                        'pickup_method' => $order['pickup_method'] ?? 0,
                        'pickup_string' => $order['pickup_string'] ?? null,

                        'store_name' => $order['store_name'] ?? null,
                        'store_id' => $order['store_id'] ?? null,

                        'order_type' => $order['order_type'] ?? null,
                        'item_type' => $order['item_type'] ?? null,
                        'order_type_id' => $order['order_type_id'] ?? null,
                        'item_type_id' => $order['item_type_id'] ?? null,
                        'item_quantity' => $order['item_quantity'] ?? 1,
                        'item_description' => $order['item_description'] ?? null,
                        'color' => $order['color'] ?? null,

                        'billing_status' => $order['billing_status'] ?? 'Unpaid',
                        'modification_notes' => $order['modification_notes'] ?? null,
                        'failed_reason' => $order['failed_reason'] ?? null,
                        'delivery_instruction' => $order['delivery_instruction'] ?? null,
                        'is_incomplete' => $order['is_incomplete'] ?? false,

                        'is_recipient_flagged' => $order['is_recipient_flagged'] ?? false,
                        'is_point_delivery' => $order['is_point_delivery'] ?? false,
                        'can_place_execution_request' => $order['can_place_execution_request'] ?? false,

                        'short_link' => $order['short_link'] ?? null,
                        'ticket_id' => $order['ticket_id'] ?? null,
                        'invoice_id' => $order['invoice_id'] ?? null,
                        'delivery_slip' => $order['delivery_slip'] ?? null,
                        'execution_request_type' => $order['execution_request_type'] ?? null,
                        'sorted_at' => $order['sorted_at'] ?? null,

                        'contact_collectable_amount_update_status' => $order['contact_collectable_amount_update_status'] ?? null,
                        'c2c_info' => $order['c2c_info'] ?? null,
                    ]
                );
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

    public function invoice($pathao_consignment_id, $merchant_id, $role)
    {
        $data = PathaoCourier::VIEW_ORDER($pathao_consignment_id);
        $order = $data['data'];
        $role = $role ?? null;
        $setup_change = SetupCharge::first();

        // Store invoice
        $data = $invoice = Invoice::updateOrCreate(
            [
                'order_id' => $order['order_id'], // unique key
                'merchant_id' => $merchant_id,
            ],
            [

                'order_consignment_id' => $order['order_consignment_id'] ?? null,
                'merchant_order_id' => $order['merchant_order_id'] ?? null,

                'order_created_at' => $order['order_created_at'] ?? null,
                'order_description' => $order['order_description'] ?? null,
                'order_status' => $order['order_status'] ?? null,
                'order_status_updated_at' => $order['order_status_updated_at'] ?? null,

                'recipient_name' => $order['recipient_name'],
                'recipient_address' => $order['recipient_address'],
                'recipient_phone' => $order['recipient_phone'],
                'recipient_secondary_phone' => $order['recipient_secondary_phone'] ?? null,

                'customer_city_id' => $order['customer_city_id'] ?? null,
                'customer_zone_id' => $order['customer_zone_id'] ?? null,
                'customer_area_id' => $order['customer_area_id'] ?? null,
                'city_name' => $order['city_name'] ?? null,
                'zone_name' => $order['zone_name'] ?? null,
                'area_name' => $order['area_name'] ?? null,

                'order_amount' => $order['order_amount'] ?? 0,
                'total_fee' => $order['total_fee'] ?? 0,
                'promo_discount' => $order['promo_discount'] ?? 0,
                'discount' => $order['discount'] ?? 0,
                'cod_fee' => $order['cod_fee'] ?? 0,
                'additional_charge' => $order['additional_charge'] ?? 0,
                'compensation_cost' => $order['compensation_cost'] ?? 0,
                'delivery_fee' => $order['delivery_fee'] ?? 0,

                'delivery_type' => $order['delivery_type'] ?? null,
                'total_weight' => $order['total_weight'] ?? 0,
                'cash_on_delivery' => $order['cash_on_delivery'] ?? null,
                'order_delivery_hub_id' => $order['order_delivery_hub_id'] ?? null,
                'delivery_method' => $order['delivery_method'] ?? 0,
                'delivery_string' => $order['delivery_string'] ?? null,
                'pickup_method' => $order['pickup_method'] ?? 0,
                'pickup_string' => $order['pickup_string'] ?? null,

                'store_name' => $order['store_name'] ?? null,
                'store_id' => $order['store_id'] ?? null,

                'order_type' => $order['order_type'] ?? null,
                'item_type' => $order['item_type'] ?? null,
                'order_type_id' => $order['order_type_id'] ?? null,
                'item_type_id' => $order['item_type_id'] ?? null,
                'item_quantity' => $order['item_quantity'] ?? 1,
                'item_description' => $order['item_description'] ?? null,
                'color' => $order['color'] ?? null,

                'billing_status' => $order['billing_status'] ?? 'Unpaid',
                'modification_notes' => $order['modification_notes'] ?? null,
                'failed_reason' => $order['failed_reason'] ?? null,
                'delivery_instruction' => $order['delivery_instruction'] ?? null,
                'is_incomplete' => $order['is_incomplete'] ?? false,

                'is_recipient_flagged' => $order['is_recipient_flagged'] ?? false,
                'is_point_delivery' => $order['is_point_delivery'] ?? false,
                'can_place_execution_request' => $order['can_place_execution_request'] ?? false,

                'short_link' => $order['short_link'] ?? null,
                'ticket_id' => $order['ticket_id'] ?? null,
                'invoice_id' => $order['invoice_id'] ?? null,
                'delivery_slip' => $order['delivery_slip'] ?? null,
                'execution_request_type' => $order['execution_request_type'] ?? null,
                'sorted_at' => $order['sorted_at'] ?? null,

                'contact_collectable_amount_update_status' => $order['contact_collectable_amount_update_status'] ?? null,
                'c2c_info' => $order['c2c_info'] ?? null,
            ]
        );

        return view('admin.courier-services.invoice', compact('order', 'data', 'setup_change', 'role'));
    }

    public function pod($consignmentId)
    {
        $booking = Booking::where('pathao_consignment_ids', $consignmentId)->first();
        if (!$booking) {
            return back()->with('error', 'Booking not found for Consignment ID: ' . $consignmentId);
        }
        // have to update the user_id based on your application logic
        $pathaoStore = CourierStore::where('user_id', '1')->first();
        if (!$pathaoStore) {
            return back()->with('error', 'Pathao Store not found for User ID: ' . $booking->booking_operator_id);
        }
        $token = $pathaoStore->token;

        $url = "https://merchant.pathao.com/api/v1/orders/{$consignmentId}/pod";

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/pdf',
            ])->timeout(30)->get($url);

            if ($response->successful()) {
                $contentType = $response->header('Content-Type', 'application/pdf') ? 'application/pdf' : $response->header('Content-Type', 'application/octet-stream');
                $filename = "pod_{$consignmentId}.pdf";

                return response($response->body(), 200)
                    ->header('Content-Type', $contentType)
                    ->header('Content-Disposition', "inline; filename=\"{$filename}\"");
            }

            Log::error('POD download failed', [
                'consignment' => $consignmentId,
                'status' => $response->status(),
                'body' => substr($response->body(), 0, 1000),
            ]);

            return back()->with('error', 'Failed to download POD. See logs.');
        } catch (\Exception $e) {
            Log::error('POD Download Error: ' . $e->getMessage(), ['consignment' => $consignmentId]);
            return back()->with('error', 'Error downloading POD: ' . $e->getMessage());
        }
    }
}
