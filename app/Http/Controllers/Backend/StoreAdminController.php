<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreAdminController extends Controller
{
    public function index(Request $request)
    {
        $bookingOperators = User::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->where('user_id', '=', Auth::user()->id)
            ->where('role', '=', 'store admin')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.store-admin.index', compact('bookingOperators'));
    } ## End Mehtod


    public function store(Request $request)
    {
        // ✅ Step 1: Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        // ✅ Step 3: Create the booking operator
        $operator = new User();
        $operator->user_id = Auth::user()->id;
        $operator->name = $validatedData['name'];
        $operator->email = $validatedData['email'];
        $operator->password = bcrypt($validatedData['password']);
        $operator->phone = $validatedData['phone'] ?? null;
        $operator->address = $validatedData['address'] ?? null;
        $operator->role = 'store admin';
        $operator->save();

        // Add role to model_has_roles table automatically
        $operator->assignRole('store admin');

        // ✅ Step 4: Return response
        return redirect()->back()->with('success', 'Booking operator created successfully!');
    }
}
