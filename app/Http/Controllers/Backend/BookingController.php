<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingProduct;
use App\Models\DeliveryType;
use App\Models\Product;
use App\Models\ProductType;
use App\Models\Store;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 300); // 300 seconds = 5 minutes


class BookingController extends Controller
{
    public function index(Request $request)
    {
        $store      = Store::where('merchant_id', '=', Auth::user()->id)->where('status', '=', 1)->first();
        $products   = Product::where('user_id', '=', Auth::user()->id)->get();
        $get_cities = PathaoCourier::GET_CITIES();
        $cities     = $get_cities['data']['data'] ?? [];

        $bookingOrders = Booking::query()
            ->when($request->filled('search'), fn($q) =>
            $q->where('order_id', 'like', '%' . $request->search . '%'))
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.booking.index', compact('store', 'products', 'bookingOrders', 'cities'));
    }

    /**
     * Show the booking creation form.
     */
    public function create()
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "booking operator") {
            $user_id = Auth::user()->user_id;
        }

        // ✅ Load data for dropdowns
        $stores   = Store::select('id', 'name')->where('merchant_id', '=', $user_id)->orderBy('name')->get();
        $products = Product::select('id', 'name')->where('user_id', '=', $user_id)->orderBy('name')->get();


        $productTypes = ProductType::where('status', '=', 1)->orderBy('name')->get();
        $deliveryTypes = DeliveryType::where('status', '=', 1)->orderBy('name')->get();

        $get_cities = PathaoCourier::GET_CITIES();
        $cities = $get_cities['data']['data'] ?? [];

        // ✅ Pass the data to the view
        return view('admin.booking.create', compact(
            'stores',
            'products',
            'cities',
            'productTypes',
            'deliveryTypes'
        ));
    }

    public function store(Request $request)
    {
        // ------------------------------
        // Step 1: Validate Request
        // ------------------------------
        $validatedData = $request->validate([
            'store_id'                  => 'required|integer',
            'product_type_id'           => 'required|string',
            'delivery_type_id'          => 'required|string',
            'recipient_name'            => 'required|string|max:100',
            'recipient_phone'           => 'required|string|max:20',
            'recipient_secondary_phone' => 'nullable|string|max:20',
            'recipient_address'         => 'required|string|min:10|max:255',
            'amount_to_collect'         => 'required|string',
            'item_description'          => 'nullable',
            'city_id'                   => 'required|integer',
            'zone_id'                   => 'required|integer',
            'area_id'                   => 'required|integer',
            'products'                  => 'required',  // product list JSON
        ]);

        // Convert product JSON to PHP array
        $products = json_decode($request->products, true);

        if (!is_array($products) || count($products) === 0) {
            return back()->with('error', 'Please add at least one product.');
        }

        // Create order ID
        $datetime = date('YmdHis');
        $random = $this->base62(6);

        // -------------------------------------
        // Step 2: Use DB Transaction
        // -------------------------------------
        DB::beginTransaction();

        try {

            // ------------------------------
            // Save Booking
            // ------------------------------
            $booking = Booking::create([
                'merchant_id'               => Auth::user()->user_id ?? Auth::user()->id,
                'booking_operator_id'       => (Auth::user()->role == "booking operator") ? Auth::user()->user_id : Auth::user()->id,
                'order_id'                  => $datetime . strtoupper($random),
                'store_id'                  => $validatedData['store_id'],
                'product_type_id'           => $validatedData['product_type_id'],
                'delivery_type_id'          => $validatedData['delivery_type_id'],
                'recipient_name'            => $validatedData['recipient_name'],
                'recipient_phone'           => $validatedData['recipient_phone'],
                'recipient_secondary_phone' => $validatedData['recipient_secondary_phone'] ?? null,
                'recipient_address'         => $validatedData['recipient_address'],
                'city_id'                   => $validatedData['city_id'],
                'zone_id'                   => $validatedData['zone_id'],
                'area_id'                   => $validatedData['area_id'],
                'amount_to_collect'         => $validatedData["amount_to_collect"],
                'item_description'          => $validatedData["item_description"],
            ]);

            // Get ID
            $booking_id = $booking->id;

            // Step 1: Get all product IDs
            $productIds = collect($products)->pluck('product_id')->toArray();

            // Step 2: Get all product info (weight + stock) in ONE QUERY
            $productData = Product::whereIn('id', $productIds)
                ->get(['id', 'weight', 'stock'])
                ->keyBy('id');

            // Step 3: Prepare bulk insert array
            $bookingProductInsert = [];

            foreach ($products as $item) {
                $pid = $item['product_id'];

                // Get product object
                $product = $productData[$pid];

                // Reduce stock in memory
                $product->stock -= $item['quantity'];

                // Prepare booking product insert
                $bookingProductInsert[] = [
                    'booking_id' => $booking_id,
                    'product_id' => $pid,
                    'quantity'   => $item['quantity'],
                    'weight'     => $product->weight,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Step 4: BULK update stock (only one query)
            foreach ($productData as $product) {
                Product::where('id', $product->id)->update([
                    'stock' => $product->stock
                ]);
            }

            // Step 5: BULK insert booking products (one query)
            BookingProduct::insert($bookingProductInsert);

            DB::commit();
        } catch (\Exception $e) {

            DB::rollBack();
            return back()->with('error', 'Something went wrong! ');
        }

        // ------------------------------
        // Step 3: Return Success
        // ------------------------------
        return redirect()->back()->with('success', 'Booking created successfully!');
    }

    public function updateBooking(Request $request, $id)
    {
        // Validate form input
        $validated = $request->validate([
            'store_id'                  => 'required|exists:stores,id',
            'product_type_id'           => 'required|exists:product_types,id',
            'delivery_type_id'          => 'required|exists:delivery_types,id',
            'recipient_name'            => 'required|string|max:255',
            'recipient_phone'           => 'required|string|max:20',
            'recipient_secondary_phone' => 'nullable|string|max:20',
            'amount_to_collect'         => 'nullable|numeric',
            'item_description'          => 'nullable|string|max:500',
            'recipient_address'         => 'required|string|max:500',
            'city_id'                   => 'required|integer',
            'zone_id'                   => 'required|integer',
            'area_id'                   => 'required|integer',
        ]);

        try {
            // Start transaction
            DB::transaction(function () use ($validated, $id) {

                // Fetch the booking
                $booking = Booking::findOrFail($id);

                // Update booking fields
                $booking->update([
                    'store_id'                  => $validated['store_id'],
                    'product_type_id'           => $validated['product_type_id'],
                    'delivery_type_id'          => $validated['delivery_type_id'],
                    'recipient_name'            => $validated['recipient_name'],
                    'recipient_phone'           => $validated['recipient_phone'],
                    'recipient_secondary_phone' => $validated['recipient_secondary_phone'] ?? null,
                    'amount_to_collect'         => $validated['amount_to_collect'] ?? null,
                    'item_description'          => $validated['item_description'] ?? null,
                    'recipient_address'         => $validated['recipient_address'],
                    'city_id'                   => $validated['city_id'],
                    'zone_id'                   => $validated['zone_id'] ?? null,
                    'area_id'                   => $validated['area_id'] ?? null,
                ]);
            });

            return redirect()->back()->with('success', 'Booking updated successfully!');
        } catch (\Exception $e) {
            Log::error("Booking update failed: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to update booking.');
        }
    }

    public function edit($id)
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "booking operator") {
            $user_id = Auth::user()->user_id;
        }

        $stores          = Store::select('id', 'name')->where('merchant_id', '=', $user_id)->orderBy('name')->get();
        $products        = Product::select('id', 'name')->where('user_id', '=', $user_id)->orderBy('name')->get();
        $productTypes    = ProductType::where('status', '=', 1)->orderBy('name')->get();
        $deliveryTypes   = DeliveryType::where('status', '=', 1)->orderBy('name')->get();
        $get_cities      = PathaoCourier::GET_CITIES();
        $cities          = $get_cities['data']['data'] ?? [];

        $bookingInfo     = Booking::findOrFail($id);
        $bookingProducts = BookingProduct::with('product')
            ->where('booking_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'id'                => $item->id,
                    'product_id'        => $item->product_id,
                    'product_name'      => $item->product->name ?? null,
                    'weight'            => $item->weight,
                    'quantity'          => $item->quantity,
                    'amount'            => $item->amount,
                    'description_price' => $item->description_price,
                ];
            });

        return view('admin.booking.edit', compact('id', 'stores', 'products', 'bookingInfo', 'bookingProducts', 'productTypes', 'deliveryTypes', 'cities'));
    }

    public function editBookingProduct(Request $request, $bookingId)
    {
        // Validate input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        // Wrap in transaction to ensure atomicity
        DB::transaction(function () use ($validated, $bookingId) {

            // Fetch product
            $product = Product::findOrFail($validated['product_id']);

            // Check if enough stock exists
            if ($product->stock < $validated['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$product->stock} units available in stock."
                ]);
            }

            // Deduct stock
            $product->decrement('stock', $validated['quantity']);

            // Prepare booking product data
            $bookingProductData = [
                'booking_id' => $bookingId,
                'product_id' => $validated['product_id'],
                'weight'     => $product->weight,
                'quantity'   => $validated['quantity'],
            ];

            // Create booking product
            BookingProduct::create($bookingProductData);
        });

        return redirect()->back()->with('success', 'Product added to booking successfully!');
    }

    public function deleteBookingProduct($id)
    {
        try {
            DB::transaction(function () use ($id) {

                // Fetch booking product with related product
                $bookingProduct = BookingProduct::with('product')->findOrFail($id);

                $product = $bookingProduct->product;

                // Restore stock
                if ($product) {
                    $product->increment('stock', $bookingProduct->quantity);
                }

                // Delete booking product
                $bookingProduct->delete();
            });

            return redirect()->back()->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            // Optional: Log the exception for debugging
            Log::error("Failed to delete booking product: {$e->getMessage()}");

            return redirect()->back()->with('error', 'Failed to delete product.');
        }
    }

    public function bookingIndex(Request $request, $orderId)
    {
        // $store = Store::where('merchant_id', '=', Auth::user()->id)->where('status', '=', 1)->first();
        $products       = Product::where('user_id', '=', Auth::user()->id)->get();
        $bookingOrders  = Booking::where('id', '=', $orderId)->paginate(8);
        $bookinProducts = BookingProduct::where('booking_id', '=', $orderId)->get();

        return view('admin.booking.add_product', compact('orderId', 'products', 'bookingOrders', 'bookinProducts'));
    }

    public function addProduct(Request $request)
    {
        $validated = $request->validate([
            'booking_id'        => 'required|exists:bookings,id',
            'product_id'        => 'required|exists:products,id',
            'weight'            => 'nullable|numeric|min:0',
            'quantity'          => 'required|integer|min:1',
            'amount'            => 'required|numeric|min:0',
            'description_price' => 'nullable|string|max:255',
        ]);

        $bookingProduct = BookingProduct::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully!',
            'data'    => $bookingProduct
        ]);
    }

    public function destroy($id)
    {
        try {
            $product = BookingProduct::findOrFail($id);
            $product->delete();

            return redirect()->back()->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete product.');
        }
    }

    function base62($length = 6)
    {
        $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $id = '';
        for ($i = 0; $i < $length; $i++) {
            $id .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $id;
    }
}
