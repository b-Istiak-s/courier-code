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
            <div class="col-lg-8">

                {{-- Success Flash Message --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>✅ Success!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>⚠️ Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                    <div class="card-header bg-primary text-white fw-semibold">
                        Create Booking
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.booking.store') }}">
                            @csrf

                            <div class="mb-3 row">
                                {{-- Store Name --}}
                                <div class="col-sm-12">
                                    <label for="store_id" class="col-form-label">Store Name</label>
                                    <select class="form-select form-select-md" id="store_id" name="store_id"
                                        aria-label="Small select example" required>
                                        <option value="" selected>Select Store</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-12">
                                    <label for="product_type_id" class="col-sm-3 col-form-label">Product Type</label>
                                    <select class="form-select form-select-md" id="product_type_id" name="product_type_id"
                                        aria-label="Small select example" required>
                                        <option value="">Product Type</option>
                                        @foreach ($productTypes as $productType)
                                            <option value="{{ $productType->id }}">{{ $productType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-12">
                                    <label for="delivery_type_id" class="col-sm-3 col-form-label">Delivery Type</label>
                                    <select class="form-select form-select-md" id="delivery_type_id" name="delivery_type_id"
                                        aria-label="Small select example" required>
                                        <option value="">Delivery Type</option>
                                        @foreach ($deliveryTypes as $deliveryType)
                                            <option value="{{ $deliveryType->id }}">{{ $deliveryType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Recipient Details --}}
                            <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <h4>Recipient Details</h4>
                                    <hr>
                                </div>

                                {{-- Recipient's name --}}
                                <div class="col-sm-12">
                                    <label for="recipient_name" class="col-form-label">Recipient's name</label>
                                    <input type="recipient_name" id="recipient_name" name="recipient_name"
                                        class="form-control @error('recipient_name') is-invalid @enderror"
                                        placeholder="Recipient's name" value="{{ old('recipient_name') }}">
                                </div>

                                {{-- Recipient's Phone --}}
                                <div class="col-sm-12">
                                    <label for="recipient_phone" class="col-form-label">Recipient's Phone</label>
                                    <input type="recipient_phone" id="recipient_phone" name="recipient_phone"
                                        class="form-control @error('recipient_phone') is-invalid @enderror"
                                        placeholder="Recipient's Phone" value="{{ old('recipient_phone') }}">
                                </div>

                                {{-- Recipient's Secondary Phone --}}
                                <div class="col-sm-12">
                                    <label for="recipient_secondary_phone" class="col-form-label">Recipient's Secondary
                                        Phone</label>
                                    <input type="recipient_secondary_phone" id="recipient_secondary_phone"
                                        name="recipient_secondary_phone"
                                        class="form-control @error('recipient_secondary_phone') is-invalid @enderror"
                                        placeholder="Recipient's Secondary Phone"
                                        value="{{ old('recipient_secondary_phone') }}">
                                </div>

                                <div class="col-sm-12">
                                    <label for="recipient_address" class="col-form-label">Recipient Address</label>
                                    <textarea id="recipient_address" name="recipient_address" rows="3" class="form-control"
                                        placeholder="Recipient Address">{{ old('recipient_address') }}</textarea>
                                </div>
                            </div>

                            {{-- Location selectors: City / Zone / Area (City -> Zone -> Area) --}}
                            <div class="mb-3 row">
                                <div class="col-sm-4">
                                    <label for="city_id" class="col-form-label">City</label>
                                    <select id="city_id" name="city_id"
                                        class="form-select @error('city_id') is-invalid @enderror">
                                        <option value="">Select City</option>
                                        @foreach ($cities ?? [] as $c)
                                            <option value="{{ $c['city_id'] ?? $c->city_id }}"
                                                {{ old('city_id') == ($c['city_id'] ?? $c->city_id) ? 'selected' : '' }}>
                                                {{ $c['name'] ?? ($c['city_name'] ?? ($c->title ?? '')) }}</option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-4">
                                    <label for="zone_id" class="col-form-label">Zone</label>
                                    <select id="zone_id" name="zone_id"
                                        class="form-select @error('zone_id') is-invalid @enderror" disabled>
                                        <option value="">Select Zone</option>
                                    </select>
                                    @error('zone_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-sm-4">
                                    <label for="area_id" class="col-form-label">Area</label>
                                    <select id="area_id" name="area_id"
                                        class="form-select @error('area_id') is-invalid @enderror" disabled>
                                        <option value="">Select Area</option>
                                    </select>
                                    @error('area_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-sm btn-success px-4">
                                        <i class="bx bx-save"></i> Save
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
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
    </script>
@endsection
