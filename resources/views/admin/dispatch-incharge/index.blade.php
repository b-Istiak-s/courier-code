@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Dispatch Incharge</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Manage Dispatch Incharge</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto">
            <a href="{{ route('admin.dispatch.incharge.create') }}" class="btn btn-sm btn-dark">
                <i class="fas fa-plus-circle me-1"></i> Add Dispatch Incharge
            </a>
        </div>
    </div>
    <!--end breadcrumb-->
    <hr>

    {{-- Admin List Table --}}
    <div class="row justify-content-start mt-5">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">
                    All Dispatch Incharge Details
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
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Hub</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Registered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dispatchIncharges as $index => $dispatchIncharge)
                                <tr>
                                    <td>{{ $dispatchIncharges->firstItem() + $index }}</td>
                                    <td>{{ $dispatchIncharge->name }}</td>
                                    <td>{{ $dispatchIncharge->email }}</td>
                                    <td>{{ $dispatchIncharge->phone ?? '-' }}</td>
                                    <td>hub</td>
                                    <td>{{ $dispatchIncharge->phone ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('admin.toggle.status', $dispatchIncharge->id) }}"
                                            class="btn btn-sm d-inline-flex align-items-center {{ $dispatchIncharge->status ? 'btn-success' : 'btn-danger' }}"
                                            onclick="return confirm('Are you sure you want to toggle status?')">
                                            <i
                                                class="bx {{ $dispatchIncharge->status ? 'bx-toggle-right' : 'bx-toggle-left' }} me-1"></i>
                                            {{ $dispatchIncharge->status ? 'Active' : 'Inactive' }}
                                        </a>
                                        <a href="{{ route('admin.dispatch.incharge.edit', $dispatchIncharge->id) }}"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>{{ $dispatchIncharge->created_at->format('d M, Y h:i A') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="col-lg-12">
                        <div class="mt-3">
                            @if (!empty($dispatchIncharges))
                                {{ $dispatchIncharges->links('pagination::bootstrap-5') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
