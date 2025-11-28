<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovementController extends Controller
{

    public function index(Request $request, $id)
    {
        ## ✅ Find the store or show 404 if not found
        $store = Store::findOrFail($id);

        ## ✅ Check if this store belongs to the logged-in user
        if ((int) $store->store_admin_id !== Auth::id() && (int) $store->merchant_id !== Auth::id()) {
            abort(404); ## Unauthorized access → show 404
        }

        ## ✅ Get paginated stock movements (you can later filter by store_id if needed)
        $stockMovements = StockMovement::where('store_id', '=', $id)->paginate(8);
        $user           = Auth::user();

        // Apply role-based store filtering
        if ($user->role === 'admin' || $user->role === 'merchant') {
            $user_id = $user->id;
        } else {
            $user_id = $user->user_id;
        }

        ## ✅ Get all products that belong to this user
        $products = Product::where('user_id', $user_id ?? null)->get();

        ## ✅ Return the view with data
        return view('admin.stock-movement.index', compact('stockMovements', 'products', 'id'));
    } ## End Mehtod


    public function store(Request $request)
    {
        // ✅ Validate the request
        $validatedData = $request->validate([
            'store_id'   => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|string|in:in,out',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string',
        ]);

        try {
            // ✅ Start transaction
            DB::beginTransaction();

            // ✅ Find the product
            $product = Product::findOrFail($validatedData['product_id']);

            // ✅ Adjust stock based on movement type
            if ($validatedData['type'] === 'in') {
                $product->stock += $validatedData['quantity'];
            } elseif ($validatedData['type'] === 'out') {
                if ($product->stock < $validatedData['quantity']) {
                    // prevent negative stock
                    return redirect()->back()->with('error', 'Not enough stock to process this movement.');
                }
                $product->stock -= $validatedData['quantity'];
            }

            // ✅ Save updated product stock
            $product->save();

            // ✅ Create stock movement record
            StockMovement::create([
                'store_id'   => $validatedData['store_id'],
                'product_id' => $validatedData['product_id'],
                'type'       => $validatedData['type'],
                'qty'        => $validatedData['quantity'],
                'notes'      => $validatedData['notes'] ?? null,
            ]);

            // ✅ Commit the transaction
            DB::commit();

            return redirect()->back()->with('success', 'Stock movement recorded and product stock updated successfully!');
        } catch (\Exception $e) {
            // ❌ Rollback on error
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record stock movement: ' . $e->getMessage());
        }
    }
}
