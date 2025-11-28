<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingProduct;
use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Zone;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BulkUploadController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::query()
            ->when($request->filled('search'), fn($q) =>
            $q->where('name', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.bulk-upload.index', compact('categories'));
    }


    // public function store(Request $request)
    // {
    //     // Validate file
    //     $request->validate([
    //         'csv_file' => 'required|mimes:csv,txt|max:2048',
    //     ]);

    //     $file = $request->file('csv_file');
    //     $path = $file->getRealPath();

    //     // Read all lines
    //     $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    //     if (!$lines) {
    //         return back()->with('error', 'File is empty or unreadable.');
    //     }

    //     // Header row
    //     $headers = explode(',', $lines[0]);

    //     // Convert to associative rows
    //     $rows = [];
    //     foreach (array_slice($lines, 1) as $line) {
    //         $values = explode(',', $line);
    //         $rows[] = array_combine($headers, $values);
    //     }

    //     // ----------------------------------------------------
    //     // GROUP BY order_id
    //     // ----------------------------------------------------

    //     $grouped = [];

    //     foreach ($rows as $row) {
    //         $orderId = $row["Order_id"];

    //         // Extract product fields from CSV (exact names)
    //         $product = [
    //             "Product Name"     => $row["Product Name"],
    //             "Product Quantity" => $row["Product Quantity"],
    //         ];

    //         // Remove product columns so remaining fields are order-level
    //         unset($row["Product Name"], $row["Product Quantity"]);

    //         // If order not created yet, initialize
    //         if (!isset($grouped[$orderId])) {

    //             // Create order-level fields dynamically
    //             $grouped[$orderId] = [
    //                 "Order_id" => $orderId,
    //             ];

    //             foreach ($row as $key => $value) {
    //                 if ($key !== "Order_id") {
    //                     $grouped[$orderId][$key] = $value;
    //                 }
    //             }

    //             // Initialize products array
    //             $grouped[$orderId]["products"] = [];
    //         }

    //         // Add product into products array
    //         $grouped[$orderId]["products"][] = $product;
    //     }

    //     // Prepare bulk order data - create one order per product in the booking
    //     $bulkOrders = [];
    //     $unique_order_id_arr = [];

    //     foreach ($grouped as $index => $item) {

    //         // 1️⃣ Fetch all products for this merchant once
    //         $products = Product::where('user_id', $item["Merchant ID"])
    //             ->get()
    //             ->keyBy('name'); // index by product name for fast matching

    //         // 2️⃣ Loop through uploaded products
    //         foreach ($item["products"] as $value) {

    //             $productName = $value["Product Name"];

    //             // 3️⃣ Check if product exists for this merchant
    //             if (isset($products[$productName])) {

    //                 $product = $products[$productName];

    //                 // 4️⃣ Reduce stock in memory (no DB call here)
    //                 $product->stock -= (int)$value["Product Quantity"];

    //                 // Save updated product
    //                 $product->save();
    //             }
    //         }
    //     }

    //     foreach ($grouped as $index => $item) {

    //         // Safely extract values with trimming
    //         $senderName     = trim($item["Merchant Name"] ?? 'Merchant');
    //         $senderPhone    = isset($item["Merchant Phone"]) ? "0" . trim($item["Merchant Phone"]) : '01700000000';
    //         $recipientName  = trim($item["Customer Name"] ?? '');
    //         $recipientPhone = "0" . trim($item["Phone Number"] ?? '');
    //         $recipientAddr  = trim($item["Customer Address"] ?? '');
    //         $zoneName       = trim($item["Zones"] ?? '');
    //         $cityName       = trim($item["City Name"] ?? '');
    //         $itemQty        = intval($item["Item Quantity"] ?? 1);
    //         $itemWeight     = floatval($item["Item Weight"] ?? 0);
    //         $collectAmount  = floatval($item["COD Amount"] ?? 0);
    //         $description    = trim($item["Special Note"] ?? null);

    //         // Get city
    //         $city = City::where('city_name', $cityName)->first();

    //         if (!$city) {
    //             // Prevent error on missing city
    //             throw new Exception("City not found: $cityName (Row: $index)");
    //         }

    //         // Get zone
    //         $zone = Zone::where('city_id', $city->city_id)
    //             ->where('zone_name', $zoneName)
    //             ->first();

    //         if (!$zone) {
    //             throw new Exception("Zone '$zoneName' not found in city '$cityName' (Row: $index)");
    //         }

    //         $unique_order_id = uniqid("ORD_");
    //         array_push($unique_order_id_arr, $unique_order_id);

    //         // Build bulk order array
    //         $bulkOrders[] = [
    //             'store_id'            => 345173, // Pathao store ID from database
    //             'merchant_order_id'   => $unique_order_id, // Unique ID
    //             'sender_name'         => $senderName,
    //             'sender_phone'        => $senderPhone,
    //             'recipient_name'      => $recipientName,
    //             'recipient_phone'     => $recipientPhone,
    //             'recipient_address'   => $recipientAddr,
    //             'recipient_city'      => $city->city_id,
    //             'recipient_zone'      => $zone->zone_id,
    //             'recipient_area'      => null,
    //             'delivery_type'       => 48,
    //             'item_type'           => 2,
    //             'special_instruction' => $description,
    //             'item_quantity'       => $itemQty,
    //             'item_weight'         => $itemWeight,
    //             'amount_to_collect'   => $collectAmount,
    //             'item_description'    => $description,
    //         ];

    //         $booking = Booking::create([
    //             'order_id'                  => $unique_order_id,
    //             'merchant_id'               => Auth::user()->user_id ?? Auth::user()->id,
    //             'booking_operator_id'       => (Auth::user()->role == "booking-operator") ? Auth::user()->user_id : Auth::user()->id,
    //             'store_id'                  => 1,
    //             'product_type_id'           => 2,
    //             'delivery_type_id'          => 48,
    //             'recipient_name'            => trim($item["Customer Name"] ?? ''),
    //             'recipient_phone'           => "0" . trim($item["Phone Number"] ?? ''),
    //             'recipient_secondary_phone' => "0" . trim($item["Phone Number"] ?? '') ?? null,
    //             'recipient_address'         => trim($item["Customer Address"] ?? ''),
    //             'courier_status'            => "pending",
    //             'courier_service'           => "pathao",
    //             'amount_to_collect'         => floatval($item["COD Amount"] ?? 0),
    //             'item_description'          => trim($item["Special Note"] ?? null),
    //             'city_id'                   => $city->city_id,
    //             'zone_id'                   => $zone->zone_id,
    //             'area_id'                   => null,
    //             'status'                    => '1', // default
    //         ]);

    //         // 1️⃣ Fetch all products for this merchant once
    //         $products = Product::where('user_id', $item["Merchant ID"])
    //             ->get()
    //             ->keyBy('name'); // index by product name for fast matching

    //         // 2️⃣ Loop through uploaded products
    //         foreach ($item["products"] as $value) {

    //             $productName = $value["Product Name"];

    //             // 3️⃣ Check if product exists for this merchant
    //             if (isset($products[$productName])) {

    //                 $product = $products[$productName];

    //                 BookingProduct::create([
    //                     'booking_id' => $booking->id,
    //                     'product_id' => $product->id,
    //                     'weight'     => $product->weight,
    //                     'quantity'   => $value["Product Quantity"],
    //                 ]);
    //             }
    //         }
    //     }


    //     // dd($bulkOrders);
    //     // dd($unique_order_id_arr);

    //     try {
    //         $consignmentIds = [];
    //         $failedOrders = [];

    //         // Create individual orders for each product
    //         foreach ($bulkOrders as $index => $orderData) {
    //             try {
    //                 // Create PathaoOrderRequest for each order
    //                 $pathaoOrderRequest = new \Enan\PathaoCourier\Requests\PathaoOrderRequest();
    //                 $pathaoOrderRequest->merge($orderData);

    //                 // Create order in Pathao
    //                 $pathaoResponse = PathaoCourier::CREATE_ORDER($pathaoOrderRequest);

    //                 Log::info("Pathao Order {$index} Creation Response: ", $pathaoResponse);

    //                 // Check if order creation was successful
    //                 if (isset($pathaoResponse['data']['data']['consignment_id'])) {
    //                     $consignmentIds[] = $pathaoResponse['data']['data']['consignment_id'];
    //                 } else {
    //                     $failedOrders[] = $orderData['merchant_order_id'];
    //                     Log::error("Failed to create order {$orderData['merchant_order_id']}: ", $pathaoResponse);
    //                 }
    //             } catch (\Exception $e) {
    //                 $failedOrders[] = $orderData['merchant_order_id'];
    //                 Log::error("Exception creating order {$orderData['merchant_order_id']}: " . $e->getMessage());
    //             }
    //         }

    //         // If all orders failed
    //         if (empty($consignmentIds)) {
    //             return back()->with('error', 'Failed to create any orders in Pathao. Please check logs.');
    //         }

    //         // Update booking record with Pathao consignment details
    //         foreach ($unique_order_id_arr as $key => $uid) {

    //             $booking = Booking::where('order_id', $uid)->first();

    //             foreach ($consignmentIds as $key => $consignmentId) {
    //                 # code...
    //                 // if ($booking) {
    //                 $booking->pathao_consignment_ids = $consignmentId;
    //                 $booking->save();
    //                 // }
    //             }
    //         }

    //         $successMessage = count($consignmentIds) . ' order(s) created successfully! Consignments: ' . implode(', ', $consignmentIds);

    //         if (!empty($failedOrders)) {
    //             $successMessage .= ' | Failed: ' . implode(', ', $failedOrders);
    //         }

    //         return back()->with('success', $successMessage);
    //     } catch (\Exception $e) {
    //         Log::error('Pathao Order Creation Error: ' . $e->getMessage());
    //         return back()->with('error', 'Pathao API error: ' . $e->getMessage());
    //     }
    // }



    public function store(Request $request)
    {
        //--------------------------------
        // 1️⃣ VALIDATE FILE
        //--------------------------------
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if (!$lines) return back()->with('error', 'File is empty');

        //--------------------------------
        // 2️⃣ PARSE CSV
        //--------------------------------
        $headers = str_getcsv($lines[0]); // better than explode()
        $rows = array_map(fn($line) => array_combine($headers, str_getcsv($line)), array_slice($lines, 1));

        //--------------------------------
        // 3️⃣ GROUP BY ORDER_ID
        //--------------------------------
        $grouped = [];
        foreach ($rows as $row) {

            $orderId = $row["Order_id"];

            $product = [
                "name"     => $row["Product Name"],
                "qty"      => (int)$row["Product Quantity"],
            ];

            unset($row["Product Name"], $row["Product Quantity"]);

            if (!isset($grouped[$orderId])) {
                $grouped[$orderId] = $row;
                $grouped[$orderId]["products"] = [];
            }

            $grouped[$orderId]["products"][] = $product;
        }

        //--------------------------------
        // 4️⃣ PRELOAD CITY + ZONE CACHE
        //--------------------------------
        $cityCache = City::pluck('city_id', 'city_name');
        $zoneCache = Zone::select('city_id', 'zone_name', 'zone_id')->get()->groupBy('city_id');

        //--------------------------------
        // 5️⃣ BULK PRELOAD_PRODUCTS BY MERCHANT
        //--------------------------------
        $merchantProductCache = [];
        foreach ($grouped as $item) {
            $merchantId = $item["Merchant ID"];
            if (!isset($merchantProductCache[$merchantId])) {
                $merchantProductCache[$merchantId] = Product::where('user_id', $merchantId)
                    ->get()
                    ->keyBy('name');
            }
        }

        //--------------------------------
        // 6️⃣ INIT BULK USAGE ARRAYS
        //--------------------------------
        $bulkOrders = [];
        $createdOrders = [];  // order_id => booking_id
        $updateStock = [];    // merchantId => [productId => qty]

        //--------------------------------
        // 7️⃣ PROCESS EACH ORDER
        //--------------------------------
        foreach ($grouped as $group) {

            $merchantId = $group["Merchant ID"];
            $merchantProducts = $merchantProductCache[$merchantId];

            // Validate City
            $cityName = trim($group["City Name"]);
            if (!isset($cityCache[$cityName])) {
                throw new Exception("City not found: $cityName");
            }
            $cityId = $cityCache[$cityName];

            // Validate Zone
            $zoneName = trim($group["Zones"]);
            $zone = $zoneCache[$cityId]->firstWhere('zone_name', $zoneName);
            if (!$zone) throw new Exception("Zone '{$zoneName}' not found in '$cityName'");
            $zoneId = $zone->zone_id;

            // Generate Unique Order ID
            $uniqueOrderId = uniqid("ORD_");

            //--------------------------------
            // A) BUILD PATHAO BULK ORDER
            //--------------------------------
            $bulkOrders[] = [
                'store_id'            => 345173,
                'merchant_order_id'   => $uniqueOrderId,
                'sender_name'         => trim($group["Merchant Name"]),
                'sender_phone'        => "0" . trim($group["Merchant Phone"]),
                'recipient_name'      => trim($group["Customer Name"]),
                'recipient_phone'     => "0" . trim($group["Phone Number"]),
                'recipient_address'   => trim($group["Customer Address"]),
                'recipient_city'      => $cityId,
                'recipient_zone'      => $zoneId,
                'delivery_type'       => 48,
                'item_type'           => 2,
                'item_quantity'       => (int)$group["Item Quantity"],
                'item_weight'         => (float)$group["Item Weight"],
                'amount_to_collect'   => (float)$group["COD Amount"],
                'item_description'    => trim($group["Special Note"]),
            ];

            //--------------------------------
            // B) CREATE BOOKING
            //--------------------------------
            $booking = Booking::create([
                'order_id'            => $uniqueOrderId,
                'merchant_id'         => Auth::user()->user_id ?? Auth::id(),
                'booking_operator_id' => Auth::id(),
                'store_id'            => 1,
                'product_type_id'     => 2,
                'delivery_type_id'    => 48,
                'recipient_name'      => trim($group["Customer Name"]),
                'recipient_phone'     => "0" . trim($group["Phone Number"]),
                'recipient_address'   => trim($group["Customer Address"]),
                'courier_status'      => "pending",
                'courier_service'     => "pathao",
                'amount_to_collect'   => (float)$group["COD Amount"],
                'item_description'    => trim($group["Special Note"]),
                'city_id'             => $cityId,
                'zone_id'             => $zoneId,
                'status'              => 1,
            ]);

            $createdOrders[$uniqueOrderId] = $booking->id;

            //--------------------------------
            // C) PROCESS BOOKING PRODUCTS
            //--------------------------------
            foreach ($group["products"] as $p) {

                if (!isset($merchantProducts[$p["name"]])) continue;

                $prod = $merchantProducts[$p["name"]];

                BookingProduct::create([
                    'booking_id' => $booking->id,
                    'product_id' => $prod->id,
                    'weight'     => $prod->weight,
                    'quantity'   => $p["qty"],
                ]);

                // Queue stock update
                $updateStock[$prod->id] = ($updateStock[$prod->id] ?? 0) + $p["qty"];
            }
        }

        //--------------------------------
        // 8️⃣ BULK UPDATE STOCK
        //--------------------------------
        foreach ($updateStock as $productId => $qty) {
            Product::where('id', $productId)->decrement('stock', $qty);
        }

        //--------------------------------
        // 9️⃣ CREATE PATHAO ORDERS
        //--------------------------------
        $consignmentMap = [];  // order_id => consign_id

        foreach ($bulkOrders as $order) {

            try {
                $req = new \Enan\PathaoCourier\Requests\PathaoOrderRequest();
                $req->merge($order);

                $res = PathaoCourier::CREATE_ORDER($req);

                if (isset($res['data']['data']['consignment_id'])) {
                    $consignmentMap[$order['merchant_order_id']] =
                        $res['data']['data']['consignment_id'];
                }
            } catch (\Exception $e) {
                Log::error("Pathao order failed: " . $e->getMessage());
            }
        }

        //--------------------------------
        // 🔟 UPDATE BOOKINGS WITH CONSIGNMENTS
        //--------------------------------
        foreach ($consignmentMap as $orderId => $cId) {
            Booking::where('order_id', $orderId)->update([
                'pathao_consignment_ids' => $cId
            ]);
        }

        //--------------------------------
        // DONE
        //--------------------------------
        return back()->with(
            'success',
            count($consignmentMap) . ' orders created successfully!'
        );
    }
}
