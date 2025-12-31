@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')

    <!--breadcrumb-->
    <!-- ✅ Visible on medium & large screens (hidden on extra-small screens) -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Manage Merchant</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Merchant</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto"></div>
    </div>

    <!-- ✅ Visible only on small & medium screens (hidden on large and up) -->
    <div class="page-breadcrumb d-flex align-items-center mb-3 d-lg-none">
        <div class="breadcrumb-title pe-3">Manage Merchant</div>
        <div class="ms-auto"></div>
    </div>
    <!--end breadcrumb-->
    <hr>

    {{-- Admin List Table --}}

    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link" href="{{ route('admin.register.merchant.page') }}">Merchant</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link active" href="{{ route('admin.register.merchant.fullfillment.page') }}">Fullfillment</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="row justify-content-start mt-2">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-dark text-white fw-semibold">
                        All Merchant Details
                    </div>
                    <div class="card-body table-responsive">
                        {{-- Search --}}
                        <form method="GET" action="{{ route('admin.register.merchant.fullfillment.page') }}" class="mb-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by name, email or phone" value="{{ request('search') }}">
                                <button class="btn btn-outline-secondary" type="submit">Search</button>
                            </div>
                        </form>
                        <br>

                        {{-- Table --}}
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Verify</th>
                                    <th>Full Name</th>
                                    <th>Role</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Status</th>
                                    <th>Fullfillment</th>
                                    <th>Registered At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($merchant_fullfillments as $index => $merchant)
                                    <tr>
                                        <td>{{ $merchant_fullfillments->firstItem() + $index }}</td>
                                        <td>
                                            @if ($merchant->kyc_status)
                                                <i class="text-primary fa-solid fa-circle-check"></i>
                                            @else
                                                <i class="text-danger fa-solid fa-circle-xmark"></i>
                                            @endif
                                        </td>
                                        <td>{{ $merchant->name }}</td>
                                        <td>{{ $merchant->role }}</td>
                                        <td>{{ $merchant->email }}</td>
                                        <td>{{ $merchant->phone ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.toggle.status', $merchant->id) }}"
                                                class="btn btn-sm d-inline-flex align-items-center {{ $merchant->status ? 'btn-success' : 'btn-danger' }}"
                                                onclick="return confirm('Are you sure you want to toggle status?')">
                                                <i
                                                    class="bx {{ $merchant->status ? 'bx-toggle-right' : 'bx-toggle-left' }} me-1"></i>
                                                {{ $merchant->status ? 'Active' : 'Inactive' }}
                                            </a>

                                            <button class="btn btn-sm btn-dark text-white" data-bs-toggle="modal"
                                                data-bs-target="#merchantModal" data-name="{{ $merchant->name }}"
                                                data-email="{{ $merchant->email }}" data-phone="{{ $merchant->phone }}"
                                                data-address="{{ $merchant->address }}"
                                                data-status="{{ $merchant->status == 1 ? 'Active' : 'Inactive' }}"
                                                data-image="{{ $merchant->image ? asset($merchant->image) : asset('no_image.jpg') }}"
                                                data-nid="{{ $merchant->nid ? asset($merchant->nid) : asset('no_image.jpg') }}">
                                                <i class="fa fa-eye"></i> View
                                            </button>
                                        </td>
                                        <td>
                                            <select class="form-select" name="fullfillment" id="fullfillment"
                                                data-user-id="{{ $merchant->id }}">

                                                <option value="yes"
                                                    {{ $merchant->role == 'Merchant Fullfillment' ? 'selected' : '' }}>
                                                    Yes
                                                </option>

                                                <option value="no"
                                                    {{ $merchant->role != 'Merchant Fullfillment' ? 'selected' : '' }}>
                                                    No
                                                </option>

                                            </select>
                                        </td>
                                        <td>{{ $merchant->created_at->format('d M, Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Pagination --}}
                        <div class="col-lg-12">
                            <div class="mt-3">
                                {{ $merchant_fullfillments->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🧩 Bootstrap Modal -->
        <div class="modal fade" id="merchantModal" tabindex="-1" aria-labelledby="merchantModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title text-white" id="merchantModalLabel">Merchant Details</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3 text-center">
                                <img id="merchantImage" src="" alt="Image"
                                    class="img-fluid rounded-circle border" width="120" height="120">
                            </div>
                            <div class="col-md-9">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Status:</th>
                                        <td id="merchantStatus"></td>
                                    </tr>

                                    <tr>
                                        <th>Name:</th>
                                        <td id="merchantName"></td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td id="merchantEmail"></td>
                                    </tr>
                                    <tr>
                                        <th>Phone:</th>
                                        <td id="merchantPhone"></td>
                                    </tr>
                                    <tr>
                                        <th>Address:</th>
                                        <td id="merchantAddress"></td>
                                    </tr>
                                    <tr>
                                        <th>NID:</th>
                                        <td>
                                            <a id="merchantNidLink" href="#" target="_blank">
                                                <img id="merchantNid" src="" alt="NID Image"
                                                    class="img-fluid border" width="150" height="100">
                                            </a>
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <!-- ⚙️ JS to populate modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var merchantModal = document.getElementById('merchantModal');
            merchantModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;

                // Get data attributes
                var name = button.getAttribute('data-name');
                var email = button.getAttribute('data-email');
                var phone = button.getAttribute('data-phone');
                var address = button.getAttribute('data-address');
                var nid = button.getAttribute('data-nid');
                var status = button.getAttribute('data-status');
                var image = button.getAttribute('data-image');

                // Set modal content
                document.getElementById('merchantName').textContent = name;
                document.getElementById('merchantEmail').textContent = email;
                document.getElementById('merchantPhone').textContent = phone || '-';
                document.getElementById('merchantAddress').textContent = address || '-';
                document.getElementById('merchantStatus').innerHTML =
                    status === 'Active' ?
                    '<span class="badge bg-success">Active</span>' :
                    '<span class="badge bg-secondary">Inactive</span>';
                document.getElementById('merchantImage').src = image;

                // 🔗 NID image click opens in new tab
                if (nid) {
                    document.getElementById('merchantNid').src = nid;
                    document.getElementById('merchantNidLink').href = nid;
                } else {
                    document.getElementById('merchantNid').src =
                        'https://via.placeholder.com/150x100?text=No+NID';
                    document.getElementById('merchantNidLink').removeAttribute('href');
                }
            });
        });
    </script>

    <script>
        document.getElementById('fullfillment').addEventListener('change', function() {
            let value = this.value;
            let userId = this.getAttribute('data-user-id');

            fetch("/admin/update-fullfillment-role", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        fullfillment: value,
                        user_id: userId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success == 1) {
                        alert("Fullfillment Role Updated");
                        window.location.reload();
                    }
                })
                .catch(err => console.error(err));
        });
    </script>
@endsection
