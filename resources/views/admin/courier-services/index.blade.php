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
                                <th>Merchant Name</th>
                                <th>Store Name</th>
                                <th>Booking Operator</th>
                                <th>Booking</th>
                                <th>Courier</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $key => $booking)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $booking->merchant->name }}</td>
                                    <td>{{ $booking->store->name }}</td>
                                    <td>{{ $booking->bookingOperator->name ?? 'N/A' }}</td>

                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#bookingModal{{ $booking->id }}">
                                            View
                                        </button>

                                        <div class="modal fade" id="bookingModal{{ $booking->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5">Booking Order: {{ $booking->order_id }}
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
                                                                        <th>Order ID</th>
                                                                        <th>Product Name</th>
                                                                        <th>QTY</th>
                                                                        <th>Price</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    @foreach ($booking->products as $i => $bp)
                                                                        <tr>
                                                                            <td>{{ $i + 1 }}</td>
                                                                            <td>{{ $booking->order_id }}</td>
                                                                            <td>{{ $bp->product->name }}</td>
                                                                            <td>{{ $bp->quantity }}</td>
                                                                            <td>{{ $bp->amount }}</td>
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
                                        <div class="row">
                                            <form action="{{ route('admin.assign.courier.services') }}" method="POST">
                                                @csrf
                                                <div class="col">
                                                    <select class="form-select form-select-md" name="courier">
                                                        <option>Select Courier</option>
                                                        <option value="1">Pathao</option>
                                                        <option value="2">Steadfast</option>
                                                    </select>
                                                </div>
                                                <div class="col">
                                                    <input type="hidden" value="{{ $booking->id }}" name="booking_id">
                                                    <button type="submit" class="btn btn-sm btn-warning">
                                                        <i class="bx bx-check"></i>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

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
