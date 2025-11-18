<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Offer</title>
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

        .items-container {
            border: 1px solid var(--medium-gray);
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: var(--light-gray);
            scrollbar-width: thin;
            scrollbar-color: var(--primary-red) var(--light-gray);
        }

        .items-container::-webkit-scrollbar {
            width: 8px;
        }

        .items-container::-webkit-scrollbar-track {
            background: var(--light-gray);
        }

        .items-container::-webkit-scrollbar-thumb {
            background-color: var(--primary-red);
            border-radius: 4px;
        }

        .input-group-text {
            background-color: var(--primary-red);
            color: white;
            border-color: var(--primary-red);
        }

        .form-check-input:checked {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
        }

        .form-check-label {
            cursor: pointer;
        }

        .nav-back-btn {
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-back-btn:hover {
            color: var(--dark-red);
            transform: translateX(-3px);
        }

        .invalid-feedback {
            display: none;
        }

        .is-invalid ~ .invalid-feedback {
            display: block;
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
                <h2 class="mb-0"><i class="bi bi-tag me-2"></i>Create New Offer</h2>
                <a href="{{ route('offers.index') }}" class="btn btn-outline-light btn-sm nav-back-btn">
                    <i class="bi bi-arrow-left me-1"></i>Back to Offers
                </a>
            </div>
            <div class="card-body">
                <!-- Display General Errors -->
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('offers.store.brand') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Brand Selection -->
                    {{-- <div class="mb-4">
                        <label for="brand_id" class="form-label fw-semibold"><i class="bi bi-shop-window me-2"></i>Brand</label>
                        <select name="brand_id" id="brandFilter" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="" disabled {{ old('brand_id') ? '' : 'selected' }}>Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div> --}}

                    <!-- Offer Name -->
                    <div class="mb-4">
                        <label for="name" class="form-label fw-semibold"><i class="bi bi-card-heading me-2"></i>Offer Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}" placeholder="Enter offer name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div class="mb-4">
                        <label for="slug" class="form-label fw-semibold"><i class="bi bi-link-45deg me-2"></i>Slug</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" name="slug" id="slug" value="{{ old('slug') }}" placeholder="e.g., summer-sale-2025">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Discount Amount -->
                    <div class="mb-4">
                        <label for="amount" class="form-label fw-semibold"><i class="bi bi-percent me-2"></i>Discount Amount</label>
                        <div class="input-group">
                            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" name="amount" id="amount" value="{{ old('amount') }}" placeholder="e.g., 10.50">
                            <span class="input-group-text">%</span>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="form-label fw-semibold"><i class="bi bi-toggle-on me-2"></i>Status</label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="5" {{ old('status') == '5' ? 'selected' : '' }}>Active</option>
                            <option value="10" {{ old('status') == '10' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date Fields -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-semibold"><i class="bi bi-calendar-plus me-2"></i>Start Date</label>
                            <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" name="start_date" id="start_date" value="{{ old('start_date') }}">
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label fw-semibold"><i class="bi bi-calendar-minus me-2"></i>End Date</label>
                            <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" name="end_date" id="end_date" value="{{ old('end_date') }}">
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Image Upload -->
                    <div class="mb-4">
                        <label for="image" class="form-label fw-semibold"><i class="bi bi-image me-2"></i>Offer Image</label>
                        <div class="image-upload-container border rounded p-3 text-center">
                            <input type="file" name="image" id="image" class="d-none @error('image') is-invalid @enderror" accept=".jpg,.jpeg,.png">
                            <label for="image" class="cursor-pointer">
                                <div class="preview-area mb-3" style="height: 140px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border: 2px dashed #ced4da; border-radius: 0.375rem;">
                                    <div class="text-center">
                                        <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                                        <p class="mb-0 text-muted">Click to upload image</p>
                                    </div>
                                </div>
                            </label>
                            <small class="text-muted d-block">Recommended size: 548x140 pixels (JPG, PNG)</small>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Items Selection with Search and Brand Filter -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-list-check me-2"></i>Select Items for this Offer</label>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-red text-white border-0"><i class="bi bi-search"></i></span>
                                    <input type="text" class="form-control" id="itemSearch" placeholder="Search items..." aria-label="Search items">
                                </div>
                            </div>
                        </div>
                        <div class="row items-container" style="max-height: 300px; overflow-y: auto;">
                            @foreach ($items as $item)
                                <div class="col-md-4 item-option" data-brand="{{ $item->brand_id }}">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="items[]" value="{{ $item->id }}" id="item{{ $item->id }}" {{ in_array($item->id, old('items', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="item{{ $item->id }}">
                                            {{ $item->name['en'] ?? 'N/A' }} <span class="badge bg-secondary ms-2">{{ $item->brand->name ?? 'No Brand' }}</span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('items')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-red btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>Create Offer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for Search, Brand Filter, and Image Preview -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Search and filter functionality
            const itemSearch = document.getElementById('itemSearch');
            const brandFilter = document.getElementById('brandFilter');
            const items = document.querySelectorAll('.item-option');

            function filterItems() {
                const searchTerm = itemSearch.value.toLowerCase();
                const brandId = brandFilter.value;

                items.forEach(item => {
                    const itemName = item.querySelector('.form-check-label').textContent.toLowerCase();
                    const itemBrand = item.dataset.brand;

                    const matchesSearch = itemName.includes(searchTerm);
                    const matchesBrand = brandId === '' || itemBrand === brandId;

                    item.style.display = matchesSearch && matchesBrand ? 'block' : 'none';
                });
            }

            itemSearch.addEventListener('input', filterItems);
            brandFilter.addEventListener('change', filterItems);

            // Image upload preview
            const imageInput = document.getElementById('image');
            const previewArea = document.querySelector('.preview-area');

            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewArea.innerHTML = `<img src="${event.target.result}" class="img-fluid" style="max-height: 100%; object-fit: contain;">`;
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>