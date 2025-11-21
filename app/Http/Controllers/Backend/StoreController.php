<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Enan\PathaoCourier\Facades\PathaoCourier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index(Request $request, $id)
    {
        $stores = Store::query()
            ->when($request->filled('search'), fn($q) =>
            $q->where('name', 'like', '%' . $request->search . '%'))
            ->where('merchant_id', '=', $id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $storeAdmins = User::where('role', '=', 'store-admin')->where('status', '=', 1)->get(['id', 'name']);
        return view('admin.store.index', compact('stores', 'id', 'storeAdmins'));
    }


    public function add(Request $request, $id)
    {
        $get_cities = PathaoCourier::GET_CITIES();
        $cities = $get_cities['data']['data'] ?? [];
        return view('admin.store.create', compact('id', 'cities'));
    }

    /**
     * Show the form for creating.
     */
    public function create()
    {

        $get_cities = PathaoCourier::GET_CITIES();
        $cities = $get_cities["data"]["data"];

        return view('admin.store.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'merchant_id' => 'required|string',
            'owner_name' => 'required|string|max:255',
            'name' => 'required|string|max:255|unique:stores,name',
            'phone' => 'required|string|size:11',
            'email' => 'nullable|email',
            'address' => 'required|string|min:15|max:120',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg,webp|max:2048',
            'city_id' => 'required',
            'zone_id' => 'required',
            'area_id' => 'required',
        ]);

        $data = [
            'merchant_id' => $validated['merchant_id'],
            'name' => $validated['name'],
            'owner_name' => $validated['owner_name'], // Store owner name in owner_phone field
            'primary_phone' => $validated['phone'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            // API returns structured objects; store string/IDs as appropriate
            'city' => $request->input('city_id'),
            'zone' => $request->input('zone_id'),
            'area' => $request->input('area_id'),
            'slug' => Str::slug($validated['name']),
            'logo' => $request->hasFile('image') ? $this->uploadImage($request->file('image')) : null,
        ];

        // Create Pathao store using the request object directly
        $pathaoStoreRequest = new \Enan\PathaoCourier\Requests\PathaoStoreRequest();
        $pathaoStoreRequest->merge([
            'name' => $validated['name'], // store name
            'contact_name' => $validated['owner_name'],
            'contact_number' => $validated['phone'],
            'address' => $validated['address'],
            'city_id' => (int) $request->input('city_id'),
            'zone_id' => (int) $request->input('zone_id'),
            'area_id' => (int) $request->input('area_id'),
        ]);

        try {
            $pathaoResponse = PathaoCourier::CREATE_STORE($pathaoStoreRequest);

            Log::info('Pathao Store Creation Response: ', $pathaoResponse);

            // Check if Pathao store creation was successful
            if (isset($pathaoResponse['status']) && $pathaoResponse['status'] != 200) {
                return back()
                    ->withInput()
                    ->with('error', 'Failed to create store in Pathao: ' . ($pathaoResponse['message'] ?? 'Unknown error'));
            }

            // Extract and store the Pathao store_id from response

            // When a new store is created, Pathao keeps the status as Approval Pending, but when stores are retrieved
            // from the API later, they appear there. So we fetch the store ID by listing stores; however, for that the store must be
            // Activated, which is not the case immediately after creation.
            // Therefore, this method shall be moved elsewhere after some time or upon status change.
            // Given the API guide, Pathao doesn't provide any webhooks for store approval status changes.
            $pathaoStoreId = $this->retrievePathaoStoreId($validated['name']);
            if ($pathaoStoreId) {
                $data['pathao_store_id'] = $pathaoStoreId;
                Log::info('Pathao Store ID retrieved: ' . $pathaoStoreId);
            }

        } catch (\Exception $e) {
            Log::error('Pathao Store Creation Error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Pathao API error: ' . $e->getMessage());
        }

        Store::create($data);

        return back()->with('success', 'Store added successfully and registered with Pathao!');
    }

    /**
     * Retrieve Pathao store ID by store name
     * @param string $storeName
     * @return int|null
     */
    private function retrievePathaoStoreId(string $storeName)
    {
        try {
            $page = 1;
            $maxPages = 10; // Prevent infinite loop

            while ($page <= $maxPages) {
                $storesResponse = PathaoCourier::GET_STORES($page);

                if (isset($storesResponse['data']) && is_array($storesResponse['data'])) {
                    foreach ($storesResponse['data'] as $store) {
                        // Match store by name (case-insensitive)
                        if (isset($store['name']) && strcasecmp($store['name'], $storeName) === 0) {
                            return $store['store_id'] ?? null;
                        }
                    }

                    // If no more pages, break
                    if (empty($storesResponse['data']) || count($storesResponse['data']) < 10) {
                        break;
                    }

                    $page++;
                } else {
                    break;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error retrieving Pathao store ID: ' . $e->getMessage());
        }

        return null;
    }

    public function assignStoreAdmin(Request $request, $id)
    {
        // $request->validate([
        //     'admin_id' => 'required|exists:users,id',
        // ]);

        // Update only the column
        Store::where('id', $id)
            ->update(['store_admin_id' => $request->admin_id]);

        return back()->with('success', 'Store admin assigned successfully!');
    }

    private function uploadImage($image)
    {
        $path = 'uploads/store';
        $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path($path), $filename);

        return $path . '/' . $filename;
    }

    public function show(Request $request)
    {
        $store = Store::where('merchant_id', '=', Auth::user()->id)->first();

        $stores = [];
        return view('admin.store.show', compact('store', 'stores'));
    }

    public function toggleStatus($id)
    {
        $store = Store::findOrFail($id);
        $store->status = $store->status == 1 ? 0 : 1;
        $store->save();

        return redirect()->back()->with('success', 'Status updated successfully.');
    } ## End Mehtod
}
