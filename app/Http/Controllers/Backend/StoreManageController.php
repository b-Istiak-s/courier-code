<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreManageController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query (so pagination always works)
        $query = Store::query();

        // Apply role-based store filtering
        if ($user->role === 'merchant' || $user->role === 'admin') {
            $query->where('merchant_id', $user->id)->where('status', '=', 1);
        } elseif ($user->role === 'store-admin') {
            $query->where('store_admin_id', $user->id)->where('status', '=', 1);
        } else {
            // If other roles have no access → return empty paginator
            return view('admin.store-manage.index', []);
        }

        // Always paginate at the end
        $stores = $query->paginate(8);

        return view('admin.store-manage.index', compact('stores'));
    }
}
