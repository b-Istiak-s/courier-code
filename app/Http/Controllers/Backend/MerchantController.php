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
        // $merchants = User::query()
        //     ->when($request->filled('search'), function ($q) use ($request) {
        //         $search = $request->search;
        //         $q->where(function ($query) use ($search) {
        //             $query->where('name', 'like', "%{$search}%")
        //                 ->orWhere('email', 'like', "%{$search}%")
        //                 ->orWhere('phone', 'like', "%{$search}%");
        //         });
        //     })
        //     ->where('role', '=', 'merchant')
        //     ->latest()
        //     ->paginate(10)
        //     ->withQueryString();

        // $verifies = Kyc::get(['user_id','status']);

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
            ->whereIn('users.role', ['merchant', 'Merchant Fullfillment'])
            ->select(
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.status',
                'users.role',
                'users.created_at',
                'kycs.status as kyc_status'
            )
            ->latest('users.id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.admin_register_merchant', compact('merchants'));
    } ## End Mehtod
}
