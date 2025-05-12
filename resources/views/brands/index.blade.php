<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brands</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #dc3545;
            --dark-red: #c82333;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #333;
        }

        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: white !important;
        }

        .bg-red {
            background-color: var(--primary-red);
        }

        .text-white {
            color: #ffffff !important;
        }

        .btn-red {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            color: #ffffff;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-red:hover,
        .btn-red:focus {
            background-color: var(--dark-red);
            border-color: var(--dark-red);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .btn-outline-red {
            border-color: var(--primary-red);
            color: var(--primary-red);
        }

        .btn-outline-red:hover {
            background-color: var(--primary-red);
            color: white;
        }

        .card {
            border-radius: 0.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-label {
            color: var(--dark-gray);
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border-color: #ced4da;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .table {
            background-color: white;
            border-radius: 0.375rem;
            overflow: hidden;
        }

        .table th {
            background-color: var(--light-gray);
            color: var(--dark-gray);
            font-weight: 600;
        }

        .table td {
            vertical-align: middle;
        }

        .nav-back-btn {
            color: var(--primary-red);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-back-btn:hover {
            color: var(--dark-red);
            transform: translateX(-3px);
        }

        /* Navbar active link styling */
        .nav-link.active {
            color: var(--primary-red) !important;
            font-weight: 600;
        }

        .nav-link:hover {
            color: var(--dark-red) !important;
        }

        /* Modal styling */
        .modal-content {
            border-radius: 0.5rem;
        }

        .modal-header {
            background-color: var(--primary-red);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0"><i class="bi bi-bookmark-star me-2"></i>Brands</h2>
                <button class="btn btn-outline-light" data-bs-toggle="modal" data-bs-target="#brandModal"
                    onclick="resetModal()">
                    <i class="bi bi-plus-circle me-1"></i>Create Brand
                </button>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Logo</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($brands as $brand)
                                <tr>
                                    <td>{{ $brand->name }}</td>
                                    <td>
                                        @if ($brand->getFirstMediaUrl('brands'))
                                            <img src="{{ $brand->getFirstMediaUrl('brands') }}"
                                                alt="{{ $brand->name }}" style="width: 50px; border-radius: 0.25rem;">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-red" data-bs-toggle="modal"
                                                data-bs-target="#brandModal"
                                                onclick="editBrand({{ $brand->id }}, '{{ $brand->name }}', '{{ $brand->getFirstMediaUrl('brands') }}')">
                                                <i class="bi bi-pencil me-1"></i>Edit
                                            </button>
                                            @if ($brand->id != 1)
                                                <form action="{{ route('brands.delete', $brand) }}" method="POST"
                                                    onsubmit="return confirm('Are you sure you want to delete this brand?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-red">
                                                        <i class="bi bi-trash me-1"></i>Delete
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No brands found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $brands->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Brand Modal -->
    <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="brandModalLabel">Create Brand</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="brandForm" action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="brandId">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Brand Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                id="name" name="name" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Logo</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                id="image" name="image" accept="image/*">
                            <small class="text-muted">Upload a PNG, JPG, or JPEG (max 2MB).</small>
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <div id="logoPreview" class="mt-2" style="display: none;">
                                <img id="logoImg" src="#" alt="Logo Preview"
                                    style="width: 100px; border-radius: 0.25rem;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-red" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-red">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript for Modal Handling -->
    <script>
        function resetModal() {
            document.getElementById('brandForm').action = "{{ route('brands.store') }}";
            document.getElementById('brandModalLabel').innerText = 'Create Brand';
            document.getElementById('brandId').value = '';
            document.getElementById('name').value = '';
            document.getElementById('image').value = '';
            document.getElementById('logoPreview').style.display = 'none';
            document.getElementById('brandForm').classList.remove('was-validated');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        function editBrand(id, name, logo) {
            document.getElementById('brandForm').action = "{{ url('brands/update') }}/" + id;
            document.getElementById('brandModalLabel').innerText = 'Edit Brand';
            document.getElementById('brandId').value = id;
            document.getElementById('name').value = name;
            if (logo) {
                document.getElementById('logoPreview').style.display = 'block';
                document.getElementById('logoImg').src = logo;
            } else {
                document.getElementById('logoPreview').style.display = 'none';
            }
        }

        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreview').style.display = 'block';
                    document.getElementById('logoImg').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>