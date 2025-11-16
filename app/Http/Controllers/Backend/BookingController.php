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

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $store = Store::where('merchant_id', '=', Auth::user()->id)->where('status', '=', 1)->first();
        $products = Product::where('user_id', '=', Auth::user()->id)->get();

        $get_cities = PathaoCourier::GET_CITIES();
        $cities = $get_cities['data']['data'] ?? [];

        $bookingOrders = Booking::where('merchant_id', '=', Auth::user()->id)->paginate(8);
        return view('admin.booking.index', compact('store', 'products', 'bookingOrders', 'cities'));
    }

    /**
     * Show the booking creation form.
     */
    public function create()
    {
        $user_id = Auth::user()->id;

        if (Auth::user()->role == "booking-operator") {
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
        $products = Product::where('user_id', '=', Auth::user()->id)->get();

        // ✅ Step 1: Validate the incoming request
        $validatedData = $request->validate([
            'store_id'                  => 'required|integer',
            'product_type_id'           => 'required|string',
            'delivery_type_id'          => 'required|string',
            'recipient_name'            => 'required|string|max:100',
            'recipient_phone'           => 'required|string|max:20',
            'recipient_secondary_phone' => 'nullable|string|max:20',
            'recipient_address'         => 'required|string|max:255',
            'city_id'                   => 'required|integer',
            'zone_id'                   => 'required|integer',
            'area_id'                   => 'required|integer',
        ]);

        // Current date and time: YYYYMMDDHHIISS
        $datetime = date('YmdHis');
        // Random BASE62 segment
        $random = $this->base62(6);

        // ✅ Step 2: Create the booking record
        $booking = new Booking();
        $booking->merchant_id               = Auth::user()->user_id ?? Auth::user()->id;
        $booking->booking_operator_id       = (Auth::user()->role == "booking-operator") ? Auth::user()->user_id : Auth::user()->id;
        $booking->order_id                  = $datetime . $random; // Combine
        $booking->store_id                  = $validatedData['store_id'];
        $booking->product_type_id           = $validatedData['product_type_id'];
        $booking->delivery_type_id          = $validatedData['delivery_type_id'];
        $booking->recipient_name            = $validatedData['recipient_name'];
        $booking->recipient_phone           = $validatedData['recipient_phone'];
        $booking->recipient_secondary_phone = $validatedData['recipient_secondary_phone'] ?? null;
        $booking->recipient_address         = $validatedData['recipient_address'];
        $booking->city_id                   = $validatedData['city_id'];
        $booking->zone_id                   = $validatedData['zone_id'];
        $booking->area_id                   = $validatedData['area_id'];
        $booking->save();

        // ✅ Step 3: Redirect with a success message
        // return view('admin.booking.add_product', compact('booking', 'products'))->with('success', 'Booking created successfully!');
        return redirect()->back()->with('success', 'Booking created successfully!');
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
