@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')

    <!--breadcrumb-->
    <!-- ✅ Visible on medium & large screens (hidden on extra-small screens) -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Manage Booking</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create Booking</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.booking.create.page') }}" class="btn btn-sm btn-dark">
                <i class="fas fa-plus-circle me-1"></i>
                Create Booking Order
            </a>
        </div>
    </div>

    <!-- ✅ Visible only on small & medium screens (hidden on large and up) -->
    <div class="page-breadcrumb d-flex align-items-center mb-3 d-lg-none">
        <div class="breadcrumb-title pe-3">Manage Booking</div>
        <div class="ms-auto">
            <a href="{{ route('admin.booking.create.page') }}" class="btn btn-sm btn-dark">
                <i class="fas fa-plus-circle me-1"></i>
                Create Booking Order
            </a>
        </div>
    </div>
    <hr>

    {{-- Admin List Table --}}
    <div class="row justify-content-start mt-5">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">
                    My Booking list
                </div>
                <div class="card-body table-responsive">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('admin.register.page') }}" class="mb-4">
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
                                <th>Store Name</th>
                                <th>Order ID</th>
                                <th>Recipient Name</th>
                                <th>Recipient Phone</th>
                                <th>Details</th>
                                {{-- <th>Add Product</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookingOrders as $index => $bookingOrder)
                                <tr>
                                    <td>{{ $bookingOrders->firstItem() + $index }}</td>
                                    <td>{{ strtoupper($bookingOrder->store->name) ?? null }}</td>
                                    <td>{{ $bookingOrder->order_id }}</td>
                                    <td>{{ strtoupper($bookingOrder->recipient_name) }}</td>
                                    <td>{{ $bookingOrder->recipient_phone }}</td>
                                    <td>

                                        <button type="button" class="btn btn-sm btn-primary viewBookingBtn"
                                            data-bs-toggle="modal" data-bs-target="#bookingModal{{ $bookingOrder->id }}">
                                            Details
                                        </button>

                                        <!-- Modal for Each Booking -->
                                        <div class="modal fade" id="bookingModal{{ $bookingOrder->id }}" tabindex="-1"
                                            aria-labelledby="bookingModal{{ $bookingOrder->id }}Label" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h1 class="modal-title fs-5 text-white"
                                                            id="bookingModal{{ $bookingOrder->id }}Label">
                                                            Booking Details (ID: {{ $bookingOrder->order_id }})
                                                        </h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                    <tr>
                                                                        <th>Merchant ID</th>
                                                                        <td>{{ $bookingOrder->merchant->name ?? null }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Booking Operator</th>
                                                                        <td>{{ $bookingOrder->bookingOperator->name ?? null }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Product Type</th>
                                                                        <td>{{ $bookingOrder->productType->name ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Delivery Type</th>
                                                                        <td>{{ $bookingOrder->deliveryType->name ?? 'N/A' }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Recipient Name</th>
                                                                        <td>{{ $bookingOrder->recipient_name }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Recipient Phone</th>
                                                                        <td>{{ $bookingOrder->recipient_phone }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Recipient Secondary Phone</th>
                                                                        <td>{{ $bookingOrder->recipient_secondary_phone }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Recipient Address</th>
                                                                        <td>{{ $bookingOrder->recipient_address }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>City</th>
                                                                        <td>
                                                                            @foreach ($cities ?? [] as $city)
                                                                                @if ($city['city_id'] == $bookingOrder->city_id)
                                                                                    {{ $city['city_name'] }}
                                                                                @endif
                                                                            @endforeach
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Zone</th>
                                                                        <td>{{ $bookingOrder->zone_id }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Area</th>
                                                                        <td>{{ $bookingOrder->area_id }}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>Created At</th>
                                                                        <td>{{ $bookingOrder->created_at->format('d M, Y h:i A') }}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-warning">
                                                            Edit
                                                        </button>
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </td>
                                    {{-- <td>
                                        @if (empty($bookingOrder->courier_service))
                                            <a class="btn btn-sm btn-success"
                                                href="{{ route('admin.booking.product.page', $bookingOrder->id) }}">Add
                                                Product</a>
                                        @endif
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
