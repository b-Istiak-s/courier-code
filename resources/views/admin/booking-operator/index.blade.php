@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Booking Operator</h4>
        <a href="{{ route('admin.booking.operator.create') }}" class="btn btn-sm btn-dark">
            <i class="fas fa-plus-circle me-1"></i> Manage Booking Operator</a>
    </div>
    <hr>

    {{-- Admin List Table --}}
    <div class="row justify-content-start">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">
                    All Booking Operator Details
                </div>
                <div class="card-body table-responsive">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('admin.booking.operator.page') }}" class="mb-4">
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
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Registered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookingOperators as $index => $bookingOperator)
                                <tr>
                                    <td>{{ $bookingOperators->firstItem() + $index }}</td>
                                    <td>{{ $bookingOperator->name }}</td>
                                    <td>{{ $bookingOperator->email }}</td>
                                    <td>{{ $bookingOperator->phone ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.toggle.status', $bookingOperator->id) }}"
                                            class="btn btn-sm d-inline-flex align-items-center {{ $bookingOperator->status ? 'btn-success' : 'btn-danger' }}"
                                            onclick="return confirm('Are you sure you want to toggle status?')">
                                            <i
                                                class="bx {{ $bookingOperator->status ? 'bx-toggle-right' : 'bx-toggle-left' }} me-1"></i>
                                            {{ $bookingOperator->status ? 'Active' : 'Inactive' }}
                                        </a>
                                    </td>
                                    <td>{{ $bookingOperator->created_at->format('d M, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="col-lg-12">
                        <div class="mt-3">
                            {{ $bookingOperators->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
