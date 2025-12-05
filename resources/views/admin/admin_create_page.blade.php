@extends('admin.master-layout')

@section('title', 'Admin Dashboard')

@section('content')
    <!--breadcrumb-->
    <!-- ✅ Visible on medium & large screens (hidden on extra-small screens) -->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Create Admin</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="javascript:;">
                            <i class="bx bx-home-alt"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Create Admin</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto"></div>
    </div>

    <!-- ✅ Visible only on small & medium screens (hidden on large and up) -->
    <div class="page-breadcrumb d-flex align-items-center mb-3 d-lg-none">
        <div class="breadcrumb-title pe-3">Create Admin</div>
        <div class="ms-auto"></div>
    </div>
    <!--end breadcrumb-->
    <hr>

    <div class="row g-4">
        {{-- Profile Info Card --}}
        {{-- <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <img src="{{ !empty($data->image) ? url($data->image) : url('no_image.jpg') }}" alt="Admin"
                        class="rounded-circle img-thumbnail mb-3" width="110">
                    <h4>{{ $data->name }}</h4>
                    <p class="text-muted mb-1">{{ $data->email }}</p>
                    <p class="text-muted mb-1">Role: {{ $data->role }}</p>
                </div>
            </div>
        </div> --}}

        {{-- Profile Edit Form --}}
        <div class="col-lg-8">
            @if (session('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-semibold">
                    Create Admin
                </div>
                <div class="card-body">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.create.store') }}">
                            @csrf

                            {{-- Full Name --}}
                            <div class="mb-3 row">
                                <label for="name" class="col-sm-3 col-form-label">Full Name</label>
                                <div class="col-sm-9">
                                    <input type="text" id="name" name="name"
                                        class="form-control @error('name') is-invalid @enderror" placeholder="Full Name"
                                        value="{{ old('name') }}">
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Phone --}}
                            <div class="mb-3 row">
                                <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                                <div class="col-sm-9">
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        placeholder="Phone" value="{{ old('phone') }}">
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="mb-3 row">
                                <label for="address" class="col-sm-3 col-form-label">Address</label>
                                <div class="col-sm-9">
                                    <textarea id="address" name="address" rows="3" class="form-control" placeholder="Address">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <hr>

                            {{-- Email --}}
                            <div class="mb-3 row">
                                <label for="email" class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" id="email" name="email"
                                        class="form-control @error('email') is-invalid @enderror" placeholder="Email"
                                        value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password --}}
                            <div class="mb-4 row">
                                <label for="password" class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror" placeholder="Password">
                                    @error('password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="row">
                                <div class="col-sm-3"></div>
                                <div class="col-sm-9">
                                    <button type="submit" class="btn btn-primary px-4">
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
        function showPreview(event) {
            const input = event.target;
            const file = input.files[0];

            // Check file size (limit: 50MB)
            if (file && file.size > 50 * 1024 * 1024) {
                alert("File size exceeds 50MB limit.");
                input.value = ""; // Clear file input
                return; // Stop preview generation
            }

            // Show preview if valid
            if (file) {
                const src = URL.createObjectURL(file);
                const preview = document.getElementById("file-ip-1-preview");
                preview.src = src;
                preview.style.display = "block";
            }
        }

        function showPreview1(event) {
            const input = event.target;
            const file = input.files[0];

            // Check file size (limit: 50MB)
            if (file && file.size > 50 * 1024 * 1024) {
                alert("File size exceeds 50MB limit.");
                input.value = ""; // Clear file input
                return; // Stop preview generation
            }

            // Show preview if valid
            if (file) {
                const src = URL.createObjectURL(file);
                const preview = document.getElementById("file-ip-2-preview");
                preview.src = src;
                preview.style.display = "block";
            }
        }
    </script>
@endsection
