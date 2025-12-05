@extends('admin.master-layout')
@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-lg-12">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.store.index', $id) }}">
                    <i class="fa fa-arrow-left"></i> Back to Store List
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

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-semibold">
                        Create Store
                    </div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.store.store') }}" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3 row">
                                {{-- Store Name --}}
                                <div class="col-sm-6">
                                    <label for="name" class="col-form-label">Store Name</label>
                                    <input type="hidden" value="{{ $id }}" name="merchant_id">
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" placeholder="Shop Name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Owner Name --}}
                                <div class="col-sm-6">
                                    <label for="owner_name" class="col-form-label">Owner Name</label>
                                    <input type="text" id="owner_name" name="owner_name" class="form-control"
                                        placeholder="Owner Name" value="{{ old('owner_name') }}">
                                </div>

                            </div>

                            <div class="mb-3 row">
                                {{-- Phone --}}
                                <div class="col-sm-6">
                                    <label for="phone" class="col-form-label">Phone</label>
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        placeholder="Phone" value="{{ old('phone') }}">
                                </div>

                                {{-- Email --}}
                                <div class="col-sm-6">
                                    <label for="email" class="col-form-label">Email</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Address --}}
                            <div class="mb-3 row">
                                <div class="col-sm-12">
                                    <label for="address" class="col-form-label">Address</label>
                                    <textarea id="address" name="address" rows="3" class="form-control" placeholder="Address">{{ old('address') }}</textarea>
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

                            <!-- Shop Logo -->
                            <div class="mb-3 row">
                                <label for="image" class="form-label">Shop Logo</label>
                                <div class="col-sm-12">
                                    <input type="file" name="image" id="image"
                                        class="form-control form-control-sm"
                                        accept="image/png, image/jpg, image/jpeg, image/svg+xml, image/webp"
                                        onchange="showPreview(event)">
                                    <small id="fileError" style="color: red; display: none;"></small>
                                    <div class="mt-3">
                                        <img id="file-ip-1-preview" src="{{ asset('no_image.jpg') }}"
                                            class="img-thumbnail" style="width: 100px; height: 80px;"
                                            alt="Image Preview">
                                    </div>
                                </div>
                            </div>

                            <hr>

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
        function showPreview(event) {
            const input = event.target;
            const file = input.files[0];

            // Check file size (limit: 50MB)
            if (file && file.size > 50 * 1024 * 1024) {
                alert("File size exceeds 50MB limit.");
                input.value = ""; // Clear file input
                return; // Stop preview generation
            }

            const preview = document.getElementById('file-ip-1-preview');
            preview.src = URL.createObjectURL(event.target.files[0]);
            preview.onload = () => URL.revokeObjectURL(preview.src); // Free memory
        }
    </script>
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
