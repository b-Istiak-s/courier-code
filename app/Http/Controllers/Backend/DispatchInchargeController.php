<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Hub;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DispatchInchargeController extends Controller
{
    public function index(Request $request)
    {
        $dispatchIncharges = [];

        if (Auth::user()->role == "Admin") {
            $dispatchIncharges = User::where('user_id', '=', Auth::user()->id)->where('role', '=', 'Dispatch Incharge')->paginate(10);
        }

        if (Auth::user()->role == "Hub Incharge") {
            $dispatchIncharges = User::where('user_id', '=', Auth::user()->user_id)->where('role', '=', 'Dispatch Incharge')->paginate(10);
        }

        if (Auth::user()->role == "Dispatch Incharge") {
            $dispatchIncharges = User::where('user_id', '=', Auth::user()->user_id)->where('role', '=', 'Dispatch Incharge')->paginate(10);
        }

        return view('admin.dispatch-incharge.index', compact('dispatchIncharges'));
    } ## End Mehtod

    /**
     * Show the form for creating.
     */
    public function create()
    {
        $hubLists = Hub::get();
        return view('admin.dispatch-incharge.create', compact('hubLists'));
    }


    public function store(Request $request)
    {
        // ✅ Step 1: Validate incoming request
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string',
            'hub_id'   => 'required|string',
        ]);

        // ✅ Step 3: Create the Hub Incharge
        $operator = new User();
        $operator->user_id  = Auth::user()->id;
        $operator->name     = $validatedData['name'];
        $operator->email    = $validatedData['email'];
        $operator->password = bcrypt($validatedData['password']);
        $operator->phone    = $validatedData['phone'] ?? null;
        $operator->address  = $validatedData['address'] ?? null;
        $operator->role     = 'Dispatch Incharge';
        $operator->hub_id   = $validatedData['hub_id'];
        $operator->save();

        // Add role to model_has_roles table automatically
        $operator->assignRole('Dispatch Incharge');

        // ✅ Step 4: Return response
        return redirect()->back()->with('success', 'Dispatch Incharge created successfully!');
    }

    /**
     * Show the form for editing.
     */
    public function edit($id)
    {
        $hubIncharge = User::findOrFail($id);
        $hubLists = Hub::where('merchant_id', '=', Auth::user()->id)->get();

        return view('admin.dispatch-incharge.edit', compact('hubIncharge', 'hubLists'));
    }

    /**
     * Update an existing.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'phone'   => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'hub_id'  => 'required|string|max:255',
        ]);

        $hubIncharge          = User::findOrFail($id);
        $hubIncharge->name    = $validated['name'];
        $hubIncharge->phone   = $validated['phone'];
        $hubIncharge->hub_id  = $validated['hub_id'];
        $hubIncharge->address = $validated['address'];

        $hubIncharge->save();

        return back()->with('success', 'Dispatch Incharge updated successfully.');
    }
}
