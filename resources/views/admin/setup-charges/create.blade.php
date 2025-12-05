@extends('admin.master-layout')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-start">
            <div class="col-lg-6">
                @if (session('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white fw-semibold">
                        Setup Charges
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.setup.charges.update') }}">
                            @csrf

                            {{-- fulfilment_fee --}}
                            <div class="mb-3 row">
                                <label for="fulfilment_fee" class="col-sm-3 col-form-label">Fulfilment Fee</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('fulfilment_fee') is-invalid @enderror"
                                        name="fulfilment_fee" id="fulfilment_fee" placeholder="Fulfilment Fee"
                                        value="{{ old('fulfilment_fee', $setupchargers->fulfilment_fee ?? null) }}">
                                    @error('fulfilment_fee')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- delivery_charges --}}
                            <div class="mb-3 row">
                                <label for="delivery_charges" class="col-sm-3 col-form-label">Delivery Charges</label>
                                <div class="col-sm-9">
                                    <input type="text"
                                        class="form-control @error('delivery_charges') is-invalid @enderror"
                                        name="delivery_charges" id="delivery_charges" placeholder="Delivery Charges"
                                        value="{{ old('delivery_charges', $setupchargers->delivery_charges ?? null) }}">
                                    @error('delivery_charges')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- cod_fee --}}
                            <div class="mb-3 row">
                                <label for="cod_fee" class="col-sm-3 col-form-label">COD Fee</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="cod_fee" id="cod_fee"
                                        placeholder="COD Fee" value="{{ old('cod_fee', $setupchargers->cod_fee ?? null) }}">
                                </div>
                            </div>

                            {{-- product_charges --}}
                            <div class="mb-3 row">
                                <label for="product_charges" class="col-sm-3 col-form-label">Product Charges (Kg)</label>
                                <div class="col-sm-9">
                                    <input type="text"
                                        class="form-control @error('product_charges') is-invalid @enderror"
                                        name="product_charges" id="product_charges" placeholder="Product Charges per kg"
                                        value="{{ old('product_charges', $setupchargers->product_charges ?? null) }}">
                                    @error('product_charges')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <hr>

                            {{-- Submit --}}
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-sm btn-primary px-4">
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
