@extends('admin.master-layout')
@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-lg-12">
                <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.booking.page') }}">
                    <i class="fa fa-arrow-left"></i> Back to Booking List
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

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white fw-semibold">
                        Add Booking Product
                    </div>

                    <div class="card-body">

                        {{-- <form id="bookingProductForm"> --}}
                        <form id="bookingProductForm">
                            @csrf

                            <input type="hidden" name="booking_id" value="{{ $orderId }}"> <!-- If needed -->

                            <div class="mb-3 row">

                                <div class="col-sm-12">
                                    <h4>Product Details</h4>
                                    <hr>
                                </div>

                                <div class="col-sm-12">
                                    <label for="product_id" class="col-form-label">Product Name</label>
                                    <select class="form-select" name="product_id" required>
                                        <option value="">Select Product</option>

                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-sm-6">
                                    <label for="weight" class="col-form-label">Total Weight</label>
                                    <input type="text" id="weight" name="weight" class="form-control"
                                        placeholder="Total Weight">
                                </div>

                                <div class="col-sm-6">
                                    <label for="quantity" class="col-form-label">Quantity</label>
                                    <input type="number" id="quantity" name="quantity" min="1" class="form-control"
                                        placeholder="Quantity">
                                </div>

                                <div class="col-sm-12">
                                    <label for="amount" class="col-form-label">Amount to Collect</label>
                                    <input type="text" id="amount" name="amount" class="form-control"
                                        placeholder="Amount to Collect">
                                </div>

                                <div class="col-sm-12">
                                    <label for="description_price" class="col-form-label">Item Description & Price</label>
                                    <input type="text" id="description_price" name="description_price"
                                        class="form-control" placeholder="Item Description & Price">
                                </div>

                                <div class="col-sm-12 mt-3">
                                    <button type="button" id="submitProductBtn" class="btn btn-sm btn-primary">Save
                                        Product</button>
                                </div>
                            </div>
                        </form>

                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>Product List</h4>
                            </div>
                        </div>
                        <hr>

                        <div class="m-1 row">
                            <!-- Product Table -->
                            <table class="table table-bordered mt-3">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Product Name</th>
                                        <th>Weight</th>
                                        <th>Quantity</th>
                                        <th>Amount</th>
                                        <th>Description & Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="productTableBody">
                                    <!-- New rows will be appended here -->
                                    @foreach ($bookinProducts as $key => $item)
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $item->product->name ?? null }}</td>
                                            <td>{{ $item->weight }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->amount }}</td>
                                            <td>{{ $item->description_price }}</td>
                                            <td>
                                                <a href="{{ route('admin.booking.product.delete', $item->id) }}"
                                                    class="btn btn-sm btn-danger">
                                                    <i class="bx bx-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        let rowCount = 0; // keeps track of row numbers

        document.getElementById('submitProductBtn').addEventListener('click', function() {
            const form = document.getElementById('bookingProductForm');
            const formData = new FormData(form);

            axios.post("{{ route('admin.booking.product.store') }}", formData)
                .then(response => {
                    const data = response.data.data; // the created product
                    rowCount++;

                    // Get selected product name
                    const productSelect = form.querySelector('select[name="product_id"]');
                    const productName = productSelect.options[productSelect.selectedIndex].text;

                    // Put this before your script (or pass it from Blade)
                    const deleteUrl = "{{ route('admin.booking.product.delete', ':id') }}";

                    // Create new table row dynamically
                    const newRow = `
                    <tr>
                        <th scope="row">${rowCount}</th>
                        <td>${productName}</td>
                        <td>${data.weight ?? '-'}</td>
                        <td>${data.quantity}</td>
                        <td>${data.amount}</td>
                        <td>${data.description_price ?? '-'}</td>
                        <td>
                            <a href="${deleteUrl.replace(':id', data.order_id)}" class="btn btn-sm btn-danger">
                                <i class="bx bx-trash"></i>
                            </a>
                        </td>
                        </tr>
                    `;

                    document.getElementById('productTableBody').insertAdjacentHTML('beforeend', newRow);
                    form.reset();

                    alert('✅ Product added successfully!');
                })
                .catch(error => {
                    console.error(error);
                    if (error.response && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        let message = 'Validation errors:\n';
                        for (const key in errors) {
                            message += `${errors[key][0]}\n`;
                        }
                        alert(message);
                    } else {
                        alert('Something went wrong. Please try again.');
                    }
                });
        });
    </script>


@endsection
