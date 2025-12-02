<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Hub;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreInchargeController extends Controller
{
    public function index(Request $request)
    {
        $storeIncharges = User::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->where('user_id', '=', Auth::user()->id)
            ->where('role', '=', 'store inchage')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.store-incharge.index', compact('storeIncharges'));
    } ## End Mehtod

    /**
     * Show the form for creating.
     */
    public function create()
    {
        $hubLists = Hub::get();
        return view('admin.store-incharge.create', compact('hubLists'));
    }

    public function store(Request $request)
    {
        // ✅ Step 1: Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'hub_id' => 'required|string',
        ]);

        // ✅ Step 3: Create the Hub Incharge
        $operator = new User();
        $operator->user_id = Auth::user()->id;
        $operator->name = $validatedData['name'];
        $operator->email = $validatedData['email'];
        $operator->password = bcrypt($validatedData['password']);
        $operator->phone = $validatedData['phone'] ?? null;
        $operator->address = $validatedData['address'] ?? null;
        $operator->role = 'store inchage';
        $operator->hub_id = $validatedData['hub_id'];
        $operator->save();

        // Add role to model_has_roles table automatically
        $operator->assignRole('store inchage');

        // ✅ Step 4: Return response
        return redirect()->back()->with('success', 'Store Incharge created successfully!');
    }

    /**
     * Show the form for editing.
     */
    public function edit($id)
    {
        $storeIncharge = User::findOrFail($id);
        $hubLists = Hub::where('merchant_id', '=', Auth::user()->id)->get();

        return view('admin.store-incharge.edit', compact('storeIncharge', 'hubLists'));
    }

    /**
     * Update an existing.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'hub_id' => 'required|string|max:255',
        ]);

        $storeIncharge = User::findOrFail($id);
        $storeIncharge->name = $validated['name'];
        $storeIncharge->phone = $validated['phone'];
        $storeIncharge->hub_id = $validated['hub_id'];
        $storeIncharge->address = $validated['address'];

        $storeIncharge->save();

        return back()->with('success', 'Store Incharge updated successfully.');
    }
}
