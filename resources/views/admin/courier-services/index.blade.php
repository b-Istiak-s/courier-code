@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">My Store Lists</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Store List</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto"></div>
    </div>
    <!--end breadcrumb-->
    <hr>

    {{-- Admin List Table --}}
    <div class="row justify-content-start">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">
                    My Store List
                </div>
                <div class="card-body">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('admin.assign.courier.services.page') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by Order ID"
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form>
                    <br>

                    {{-- Table --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Order ID</th>
                                    <th>Merchant Name</th>
                                    <th>Store Name</th>
                                    <th>Booking Operator</th>
                                    <th class="text-center">Booking</th>
                                    <th>Courier</th>
                                    <th>Consignment ID</th>
                                    <th>Status</th>
                                    <th>Invoice</th>
                                    <th>POD</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $key => $booking)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $booking->order_id }}</td>
                                        <td>{{ $booking->merchant->name }}</td>
                                        <td>{{ strtoupper($booking->store->name) }}</td>
                                        <td>{{ $booking->bookingOperator->name ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @if (count($booking->products) > 0)
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#bookingModal{{ $booking->id }}">
                                                    View
                                                </button>
                                            @else
                                                0 Item
                                            @endif

                                            <div class="modal fade" id="bookingModal{{ $booking->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5">Booking Order:
                                                                {{ $booking->order_id }}
                                                            </h1>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="table-responsive">
                                                                <table class="table">
                                                                    <thead class="bg-dark text-white">
                                                                        <tr>
                                                                            <th>#</th>
                                                                            {{-- <th>Order ID</th> --}}
                                                                            <th>Product Name</th>
                                                                            <th>QTY</th>
                                                                            {{-- <th>Price</th> --}}
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        @foreach ($booking->products as $i => $bp)
                                                                            <tr>
                                                                                <td>{{ $i + 1 }}</td>
                                                                                {{-- <td>{{ $booking->order_id }}</td> --}}
                                                                                <td>{{ $bp->product->name }}</td>
                                                                                <td>{{ $bp->quantity }}</td>
                                                                                {{-- <td>{{ $bp->amount }}</td> --}}
                                                                            </tr>
                                                                        @endforeach

                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Close</button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>

                                            @if (count($booking->products) > 0)
                                                @if (empty($booking->courier_service))
                                                    @if (Auth::user()->can('courier.assign'))
                                                        <form action="{{ route('admin.assign.courier.services') }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-lg-10">
                                                                    <select class="form-select form-select-md"
                                                                        name="courier" required>
                                                                        <option value="">Select Courier</option>
                                                                        @foreach ($courierStores as $courierStore)
                                                                            <option value="{{ $courierStore->store_id }}">
                                                                                {{ $courierStore->store_name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="col-lg-2 d-flex p-0">
                                                                    <input type="hidden" value="{{ $booking->id }}"
                                                                        name="booking_id">
                                                                    <button type="submit" class="btn btn-sm btn-warning">
                                                                        <i class="bx bx-check"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    @endif
                                                @else
                                                    {{ strtoupper($booking->courier_service) }}
                                                @endif

                                            @endif
                                        </td>

                                        <td>
                                            {{ $booking->pathao_consignment_ids }}
                                            @php
                                                if (!empty($booking->pathao_consignment_ids)) {
                                                    # code...
                                                    $value = Enan\PathaoCourier\Facades\PathaoCourier::VIEW_ORDER(
                                                        $booking->pathao_consignment_ids,
                                                    );
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            @if (!empty($booking->pathao_consignment_ids))
                                                <div class="bg-danger p-2 rounded text-white" role="alert">
                                                    {{ $value['data']['order_status'] ?? null }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($booking->pathao_consignment_ids))
                                                <a class="btn btn-sm btn-warning d-flex align-item-center"
                                                    href="{{ route('admin.assign.courier.services.invoice.page', $booking->pathao_consignment_ids) }}">Invoice</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($booking->pathao_consignment_ids))
                                                <a class="btn btn-sm btn-success d-flex align-item-center"
                                                    href="{{ route('admin.assign.courier.services.pod.page', $booking->pathao_consignment_ids) }}">POD</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    <div class="col-lg-12">
                        <div class="mt-3">
                            {{ $bookings->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
