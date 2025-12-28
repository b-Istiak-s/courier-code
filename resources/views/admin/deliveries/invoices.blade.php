@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">All Invoices</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Invoice List</li>
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
            <div class="row">

                <div class="col">

                    <div class="card radius-10" style="background: #F5F8FA;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-black">৳ {{ $merchants ?? 0 }}</h5>
                                <div class="ms-auto">
                                    <i class='bx bx-dollar fs-3 text-black'></i>
                                </div>
                            </div>
                            <div class="progress my-3 bg-light-transparent" style="height:3px;">
                                <div class="progress-bar bg-black" role="progressbar" style="width: 55%" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center text-black">
                                <p class="mb-0">Total Collected</p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col">
                    <div class="card radius-10" style="background: #F5F8FA;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-black">৳ {{ $operators ?? 0 }}</h5>
                                <div class="ms-auto">
                                    <i class='bx bx-dollar fs-3 text-black'></i>
                                </div>
                            </div>
                            <div class="progress my-3 bg-light-transparent" style="height:3px;">
                                <div class="progress-bar bg-black" role="progressbar" style="width: 55%" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center text-black">
                                <p class="mb-0">Total Receivable</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10" style="background: #F5F8FA;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-black">৳ {{ $stores ?? 0 }}</h5>
                                <div class="ms-auto">
                                    <i class='bx bx-dollar fs-3 text-black'></i>
                                </div>
                            </div>
                            <div class="progress my-3 bg-light-transparent" style="height:3px;">
                                <div class="progress-bar bg-black" role="progressbar" style="width: 55%" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center text-black">
                                <p class="mb-0">Total Received</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card radius-10" style="background: #F5F8FA;">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0 text-black">৳ {{ $courierPlatforms ?? 0 }}</h5>
                                <div class="ms-auto">
                                    <i class='bx bx-dollar fs-3 text-black'></i>
                                </div>
                            </div>
                            <div class="progress my-3 bg-light-transparent" style="height:3px;">
                                <div class="progress-bar bg-black" role="progressbar" style="width: 55%" aria-valuenow="25"
                                    aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex align-items-center text-black">
                                <p class="mb-0">Total Fee</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">
                    All Invoice List
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
                                    <th>Consignment ID</th>
                                    <th>Store Name</th>
                                    <th>Courier</th>
                                    <th>Status</th>
                                    <th>Invoice</th>
                                    <th>POD</th>
                                    <th>PDF</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $key => $booking)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $booking->order_id }}</td>
                                        <td>
                                            {{ $booking->pathao_consignment_ids }}
                                        </td>
                                        <td>{{ strtoupper($booking->store->name) }}</td>
                                        <td>
                                            {{ strtoupper($booking->courier_service) }}
                                        </td>
                                        <td>
                                            @if (!empty($booking->pathao_consignment_ids))
                                                <div class="bg-danger p-2 rounded text-white" role="alert">
                                                    {{ $booking->courier_status ?? null }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>

                                            @if (!empty($booking->pathao_consignment_ids))
                                                <a class="btn btn-sm btn-warning d-flex align-item-center"
                                                    href="{{ route('admin.assign.courier.services.invoice.page', [$booking->pathao_consignment_ids, $booking->merchant_id, $booking->merchant->role]) }}">Invoice</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($booking->pathao_consignment_ids))
                                                <a class="btn btn-sm btn-success d-flex align-item-center" target="_blank"
                                                    href="{{ route('admin.assign.courier.services.pod.page', $booking->pathao_consignment_ids) }}">POD</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if (!empty($booking->pathao_consignment_ids))
                                                <a class="btn btn-sm btn-success d-flex align-item-center" target="_blank"
                                                    href="{{ route('admin.invoice.pdf', $booking->order_id) }}">PDF</a>
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
