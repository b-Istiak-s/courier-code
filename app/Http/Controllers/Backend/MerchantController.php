<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Kyc;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;

class MerchantController extends Controller
{
    public function manageMerchant(Request $request)
    {

        $merchants = User::query()
            ->leftJoin('kycs', 'users.id', '=', 'kycs.user_id')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('users.phone', 'like', "%{$search}%");
                });
            })
            ->where('users.role', '=', 'Merchant')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.status',
                'users.role',
                'users.image',
                'users.nid',
                'users.created_at',
                'kycs.status as kyc_status'
            )
            ->latest('users.id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.admin_register_merchant', compact('merchants'));
    } ## End Mehtod


    public function manageMerchantFullfillment(Request $request)
    {
        $merchant_fullfillments = User::query()
            ->leftJoin('kycs', 'users.id', '=', 'kycs.user_id')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('users.phone', 'like', "%{$search}%");
                });
            })
            ->where('users.role', '=', 'Merchant Fullfillment')
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.status',
                'users.role',
                'users.image',
                'users.nid',
                'users.created_at',
                'kycs.status as kyc_status'
            )
            ->latest('users.id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.admin_register_merchant_fullfillments', compact('merchant_fullfillments'));
    } ## End Mehtod
}
