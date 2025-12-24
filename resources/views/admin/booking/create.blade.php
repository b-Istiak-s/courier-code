@extends('admin.master-layout')
@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-lg-12">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.booking.page') }}">
                    <i class="fa fa-arrow-left"></i> Back to Booking List
                </a>
            </div>
        </div>
        <hr>
        <div class="row justify-content-start">
            <div class="col-lg-10">

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-semibold py-3">
                        Create Booking
                    </div>

                    <div class="card-body">
                        <form id="mainBookingForm" method="POST" action="{{ route('admin.booking.store') }}">
                            @csrf

                            <input type="hidden" name="products" id="productsInput">


                            {{-- STORE / PRODUCT / DELIVERY --}}

                            <h5 class="mb-3 fw-bold text-secondary">Booking Information</h5>
                            <div class="row g-3">

                                {{-- Store Name --}}
                                <div class="col-lg-4">
                                    <label for="store_id" class="form-label fw-semibold">Store Name</label>
                                    <select class="form-select" id="store_id" name="store_id" required>
                                        <option value="">Select Store</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Product Type --}}
                                <div class="col-lg-4">
                                    <label for="product_type_id" class="form-label fw-semibold">Product Type</label>
                                    <select class="form-select" id="product_type_id" name="product_type_id" required>
                                        <option value="">Product Type</option>
                                        @foreach ($productTypes as $productType)
                                            <option value="{{ $productType->id }}">{{ $productType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Delivery Type --}}
                                <div class="col-lg-4">
                                    <label for="delivery_type_id" class="form-label fw-semibold">Delivery Type</label>
                                    <select class="form-select" id="delivery_type_id" name="delivery_type_id" required>
                                        <option value="">Delivery Type</option>
                                        @foreach ($deliveryTypes as $deliveryType)
                                            <option value="{{ $deliveryType->id }}">{{ $deliveryType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            {{-- PRODUCT ADD SECTION --}}
                            <div class="mt-4">
                                <h5 class="fw-bold text-secondary mb-3">Add Products</h5>

                                <div class="card border shadow-sm">
                                    <div class="card-body">
                                        <div id="bookingProductForm">
                                            <input type="hidden" name="booking_id" value="{{ $orderId ?? 0 }}">

                                            <div class="row g-3">
                                                <div class="col-md-7">
                                                    <label class="form-label fw-semibold">Product Name</label>
                                                    <select name="product_id" class="form-select" id="product_id">
                                                        <option value="">Select Product</option>
                                                        @foreach ($products as $product)
                                                            <option value="{{ $product->id }}">{{ $product->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="form-label fw-semibold">Quantity</label>
                                                    <input type="number" name="quantity" id="quantity" min="1"
                                                        value="1" class="form-control" placeholder="Qty">
                                                </div>

                                                <div class="col-md-2 d-flex align-items-end">
                                                    <button type="button" id="submitProductBtn"
                                                        class="btn btn-primary w-100">
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Product Table --}}
                                <div class="mt-3 table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Product</th>
                                                <th>Qty</th>
                                                <th width="70">Action</th>
                                            </tr>
                                        </thead>

                                        <tbody id="productTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- RECIPIENT DETAILS --}}
                            <div class="mt-4">
                                <h5 class="fw-bold text-secondary mb-3">Recipient Details</h5>

                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Recipient Name</label>
                                        <input type="text" class="form-control" name="recipient_name"
                                            value="{{ old('recipient_name') }}" required>
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Phone</label>
                                        <input type="text" class="form-control" name="recipient_phone"
                                            value="{{ old('recipient_phone') }}" required>
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Secondary Phone</label>
                                        <input type="text" class="form-control" name="recipient_secondary_phone"
                                            value="{{ old('recipient_secondary_phone') }}">
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Amount to Collect</label>
                                        <input type="text" class="form-control" name="amount_to_collect">
                                    </div>

                                    <div class="col-lg-8">
                                        <label class="form-label fw-semibold">Item Description & Price</label>
                                        <input type="text" class="form-control" name="item_description">
                                    </div>

                                    <div class="col-lg-12">
                                        <label class="form-label fw-semibold">Recipient Address</label>
                                        <textarea class="form-control" rows="3" name="recipient_address" placeholder="Address, area, city" required>{{ old('recipient_address') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- LOCATION SELECTION --}}
                            {{-- <div class="mt-4">
                                <h5 class="fw-bold text-secondary mb-3">Location</h5>

                                <div class="row g-3">
                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">City</label>
                                        <select id="city_id" name="city_id" class="form-select">
                                            <option value="">Select City</option>
                                            @foreach ($cities ?? [] as $c)
                                                <option value="{{ $c['city_id'] ?? $c->city_id }}">
                                                    {{ $c['name'] ?? ($c['city_name'] ?? ($c->title ?? '')) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Zone</label>
                                        <select id="zone_id" name="zone_id" class="form-select" disabled>
                                            <option value="">Select Zone</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-4">
                                        <label class="form-label fw-semibold">Area</label>
                                        <select id="area_id" name="area_id" class="form-select" disabled>
                                            <option value="">Select Area</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="bx bx-save"></i> Save Booking
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOMContentLoaded fired - initializing location selects');

            const citySelect = document.getElementById('city_id');
            const zoneSelect = document.getElementById('zone_id');
            const areaSelect = document.getElementById('area_id');

            console.log('Elements found:', {
                citySelect: !!citySelect,
                zoneSelect: !!zoneSelect,
                areaSelect: !!areaSelect
            });

            if (!citySelect || !zoneSelect || !areaSelect) return;

            const routes = {
                zones: (cityId) => `{{ url('/pathao/zones') }}/${cityId}`,
                areas: (zoneId) => `{{ url('/pathao/areas') }}/${zoneId}`,
            };

            const oldCity = {!! json_encode(old('city_id')) !!};
            const oldZone = {!! json_encode(old('zone_id')) !!};
            const oldArea = {!! json_encode(old('area_id')) !!};

            function setOptions(selectEl, items, placeholder) {
                selectEl.innerHTML = '';
                const opt0 = document.createElement('option');
                opt0.value = '';
                opt0.textContent = placeholder;
                selectEl.appendChild(opt0);

                items.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.city_id ?? item.zone_id ?? item.area_id ?? item.id ?? item._id;
                    opt.textContent = item.city_name ?? item.zone_name ?? item.area_name ?? item.name ??
                        item.title ?? item.label;
                    selectEl.appendChild(opt);
                });
            }

            function fetchJson(url) {
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest'
                };
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.content;

                return fetch(url, {
                    headers
                }).then(r => {
                    if (!r.ok) throw new Error(r.status + ' ' + r.statusText);
                    return r.json();
                });
            }

            citySelect.addEventListener('change', function() {
                const cityId = this.value;
                console.debug('City changed to:', cityId);

                zoneSelect.innerHTML = '<option value="">Select Zone</option>';
                areaSelect.innerHTML = '<option value="">Select Area</option>';
                zoneSelect.disabled = true;
                areaSelect.disabled = true;

                if (!cityId) return;

                fetchJson(routes.zones(cityId))
                    .then(data => {
                        const zones = data.zones ?? data.data ?? [];
                        setOptions(zoneSelect, zones, 'Select Zone');
                        zoneSelect.disabled = false;

                        if (oldZone) {
                            zoneSelect.value = oldZone;
                            zoneSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(err => console.error('Failed to load zones for city', cityId, err));
            });

            zoneSelect.addEventListener('change', function() {
                const zoneId = this.value;
                console.debug('Zone changed to:', zoneId);

                areaSelect.innerHTML = '<option value="">Select Area</option>';
                areaSelect.disabled = true;

                if (!zoneId) return;

                fetchJson(routes.areas(zoneId))
                    .then(data => {
                        const areas = data.areas ?? data.data ?? [];
                        setOptions(areaSelect, areas, 'Select Area');
                        areaSelect.disabled = false;

                        if (oldArea) {
                            areaSelect.value = oldArea;
                        }
                    })
                    .catch(err => console.error('Failed to load areas for zone', zoneId, err));
            });

            // If old city exists, trigger loading
            if (oldCity) {
                citySelect.value = oldCity;
                citySelect.dispatchEvent(new Event('change'));
            }
        });
    </script> --}}

    <script>
        function checkCharacter(params) {
            const addressField = document.getElementById("recipient_address");

            addressField.addEventListener("input", function() {
                if (addressField.value.length < 10) {
                    addressField.style.border = "2px solid red";
                } else {
                    addressField.style.border = "2px solid green";
                }
            });

            // Attach to your form submit
            document.querySelector("form").addEventListener("submit", function(e) {
                if (!validateAddress()) {
                    e.preventDefault();
                }
            });
        }
    </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        let productList = [];

        $(document).on("click", "#submitProductBtn", function() {

            let product_id = $('#product_id').val();
            let product_name = $('#product_id option:selected').text();
            let quantity = $('#quantity').val();

            if (product_id === "" || quantity === "") {
                alert("Select product & enter quantity");
                return;
            }

            // Add product to JS array
            productList.push({
                product_id: product_id,
                product_name: product_name,
                quantity: quantity
            });

            // Render table
            renderProductTable();

            // Clear inputs
            $('#product_id').val('');
            $('#quantity').val('');
        });

        function renderProductTable() {
            let html = "";
            productList.forEach((item, index) => {
                html += `
            <tr>
                <td>${index + 1}</td>
                <td>${item.product_name}</td>
                <td>${item.quantity}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger removeProduct" data-index="${index}">
                        X
                    </button>
                </td>
            </tr>
        `;
            });

            $("#productTableBody").html(html);
        }

        $(document).on("click", ".removeProduct", function() {
            const index = $(this).data("index");
            productList.splice(index, 1);
            renderProductTable();
        });

        $(document).on("submit", "#mainBookingForm", function() {
            $("#productsInput").val(JSON.stringify(productList));
        });
    </script>


    {{-- <script>
        document.getElementById("recipient_address").addEventListener("input", function() {
            let addr = this.value.toLowerCase();
            console.log("this is working!!");


            // === City Auto Select ===
            if (addr.includes("chittagong") || addr.includes("muradpur")) {
                document.getElementById("city_id").value = "1"; // Chittagong
            }

            // === Zone Auto Select ===
            if (addr.includes("halishahar")) {
                document.getElementById("zone_id").value = "10"; // Halishahar
            } else if (addr.includes("muradpur")) {
                document.getElementById("zone_id").value = "11"; // Muradpur
            }

            // === Area Auto Select ===
            if (addr.includes("block i")) {
                document.getElementById("area_id").value = "101";
            } else if (addr.includes("block j")) {
                document.getElementById("area_id").value = "102";
            }
        });
    </script> --}}
@endsection
