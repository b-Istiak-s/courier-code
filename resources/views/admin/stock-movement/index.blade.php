@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')

    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Stock Management</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Stock IN/OUT</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto"></div>
    </div>
    <!--end breadcrumb-->
    <hr>


    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-semibold">
            Stock IN/OUT
        </div>

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


        <div class="card-body">
            <form method="POST" action="{{ route('admin.stock.movement.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-3 row">
                    <div class="col-sm-3">
                        <label for="product_id" class="col-form-label">Product</label>
                        <select class="form-select form-select-sm" id="product_id" name="product_id" required>
                            <option value="">Select Product</option>

                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach

                        </select>
                    </div>

                    {{-- Movement Type --}}
                    <div class="col-sm-3">
                        <label for="type" class="col-form-label">Movement Type</label>
                        <select class="form-select form-select-sm" id="type" name="type" required>
                            <option value="">Select Movement Type</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                        </select>
                    </div>

                    {{-- quantity --}}
                    <div class="col-sm-2">
                        <label for="quantity" class="col-form-label">Quantity</label>
                        <input type="hidden" name="store_id" value="{{ $id }}">
                        <input type="text" id="quantity" name="quantity" class="form-control form-control-sm"
                            value="{{ old('quantity') }}" required>
                        @error('quantity')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- notes --}}
                    <div class="col-sm-4">
                        <label for="notes" class="col-form-label">Notes (Optional)</label>
                        <input type="text" id="notes" name="notes" class="form-control form-control-sm"
                            value="{{ old('notes') }}">
                        @error('notes')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
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


    {{-- Admin List Table --}}
    <div class="row justify-content-start mt-5">
        <div class="col-lg-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white fw-semibold">
                    My Store List
                </div>
                <div class="card-body table-responsive">
                    {{-- Search --}}
                    {{-- <form method="GET" action="{{ route('admin.stock.movement.page', $id) }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by product"
                                value="{{ request('search') }}">
                            <button class="btn btn-outline-secondary" type="submit">Search</button>
                        </div>
                    </form> --}}
                    <br>

                    {{-- Table --}}
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Movement Type</th>
                                <th>Quantity</th>
                                {{-- <th>Stock</th> --}}
                                <th>Notes</th>
                                <th>Registered At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($stockMovements as $index => $stockMovement)
                                <tr>
                                    <td>{{ $stockMovements->firstItem() + $index }}</td>
                                    <td>{{ $stockMovement->product->name }}</td>
                                    <td>{{ $stockMovement->type }}</td>
                                    <td>{{ $stockMovement->qty }}</td>
                                    {{-- <td>{{ $stockMovement->product->stock }}</td> --}}
                                    <td>{{ $stockMovement->notes }}</td>
                                    <td>{{ $stockMovement->created_at->format('d M, Y h:i A') }}</td>
                                    {{-- <td>
                                        <a href="{{ route('admin.product.manage.index', $stockMovement->id) }}"
                                            class="btn btn-sm d-inline-flex align-items-center btn-warning">
                                            <i class="bx bx-edit"></i>
                                        </a>
                                    </td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Pagination --}}
                    <div class="col-lg-12">
                        <div class="mt-3">
                            {{ $stockMovements->links('pagination::bootstrap-5') }}
                        </div>
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
        const fileInput = document.getElementById('image');
        const fileError = document.getElementById('fileError');

        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
        const maxSizeInKB = 2048;

        fileInput.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                const fileType = file.type;
                const fileSizeInKB = file.size / 1024;

                // Validate MIME type
                if (!allowedTypes.includes(fileType)) {
                    fileError.textContent = 'Only JPEG, JPG, PNG, WEBP, or SVG files are allowed.';
                    fileError.style.display = 'inline';
                    this.value = '';
                    return;
                }

                // Validate file size
                if (fileSizeInKB > maxSizeInKB) {
                    fileError.textContent = 'File size must be less than 2MB.';
                    fileError.style.display = 'inline';
                    this.value = '';
                    return;
                }

                // If valid
                fileError.style.display = 'none';
            }
        });
    </script>
@endsection
