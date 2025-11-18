<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Item</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-header {
            background-color: var(--primary-red);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-label {
            color: var(--dark-gray);
            font-weight: 500;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .form-control,
        .form-select {
            border-radius: 0.375rem;
            border: 1px solid #ced4da;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            height: 38px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        .input-group-text {
            background-color: var(--primary-red);
            color: white;
            border-color: var(--primary-red);
            font-size: 0.9rem;
        }

        .btn-red {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            color: white;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .btn-red:hover {
            background-color: var(--dark-red);
            border-color: var(--dark-red);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }

        .nav-back-btn {
            color: var(--primary-red);
            font-weight: 500;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .nav-back-btn:hover {
            color: var(--dark-red);
        }

        .branch-price-section {
            background-color: var(--light-gray);
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .sticky-footer {
            position: sticky;
            bottom: 0;
            background-color: white;
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--medium-gray);
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.05);
            z-index: 10;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-list-check me-2"></i>Create New Item</h4>
                <a href="{{ route('admin.item.index') }}" class="nav-back-btn">
                    <i class="bi bi-arrow-left me-1"></i>Back to Items
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

                <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <!-- Item Name (English) -->
                        <div>
                            <label for="name_en" class="form-label"><i class="bi bi-card-heading me-1"></i>Name (English)</label>
                            <input type="text" class="form-control @error('name.en') is-invalid @enderror" name="name[en]" id="name_en" value="{{ old('name.en') }}" placeholder="Item name (English)">
                            @error('name.en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Item Name (Arabic) -->
                        <div>
                            <label for="name_ar" class="form-label"><i class="bi bi-card-heading me-1"></i>Name (Arabic)</label>
                            <input type="text" class="form-control @error('name.ar') is-invalid @enderror" name="name[ar]" id="name_ar" value="{{ old('name.ar') }}" placeholder="اسم العنصر (عربي)" dir="rtl">
                            @error('name.ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Item Description (English) -->
                        <div>
                            <label for="description_en" class="form-label"><i class="bi bi-text-paragraph me-1"></i>Description (English)</label>
                            <textarea class="form-control @error('description.en') is-invalid @enderror" name="description[en]" id="description_en" rows="3" placeholder="Enter description (English)">{{ old('description.en') }}</textarea>
                            @error('description.en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Item Description (Arabic) -->
                        <div>
                            <label for="description_ar" class="form-label"><i class="bi bi-text-paragraph me-1"></i>Description (Arabic)</label>
                            <textarea class="form-control @error('description.ar') is-invalid @enderror" name="description[ar]" id="description_ar" rows="3" placeholder="أدخل الوصف (عربي)" dir="rtl">{{ old('description.ar') }}</textarea>
                            @error('description.ar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Upload -->
                        <div>
                            <label for="image" class="form-label"><i class="bi bi-image me-1"></i>Item Image</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" id="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Default Price -->
                        <div>
                            <label for="price" class="form-label"><i class="bi bi-currency-dollar me-1"></i>Default Price</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" name="price" id="price" value="{{ old('price') }}" placeholder="e.g., 9.99">
                                <span class="input-group-text">$</span>
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category Selection -->
                        <div>
                            <label for="item_category_id" class="form-label"><i class="bi bi-tag me-1"></i>Category</label>
                            <select name="item_category_id" id="item_category_id" class="form-select @error('item_category_id') is-invalid @enderror">
                                <option value="" disabled {{ old('item_category_id') ? '' : 'selected' }}>Select Category</option>
                                @foreach ($itemCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('item_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('item_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Item Type -->
                        <div>
                            <label for="item_type" class="form-label"><i class="bi bi-egg me-1"></i>Item Type</label>
                            <select name="item_type" id="item_type" class="form-select @error('item_type') is-invalid @enderror">
                                <option value="1" {{ old('item_type') == '1' ? 'selected' : '' }}>Veg</option>
                                <option value="2" {{ old('item_type') == '2' ? 'selected' : '' }}>Non-Veg</option>
                            </select>
                            @error('item_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Is Featured -->
                        <div>
                            <label for="is_featured" class="form-label"><i class="bi bi-star me-1"></i>Is Featured</label>
                            <select name="is_featured" id="is_featured" class="form-select @error('is_featured') is-invalid @enderror">
                                <option value="1" {{ old('is_featured') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="2" {{ old('is_featured') == '2' ? 'selected' : '' }}>No</option>
                            </select>
                            @error('is_featured')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="form-label"><i class="bi bi-toggle-on me-1"></i>Status</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="5" {{ old('status') == '5' ? 'selected' : '' }}>Active</option>
                                <option value="10" {{ old('status') == '10' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Brand Selection -->
                        {{-- <div>
                            <label for="brand_id" class="form-label"><i class="bi bi-building me-1"></i>Brand</label>
                            <select name="brand_id" id="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                <option value="" disabled {{ old('brand_id') ? '' : 'selected' }}>Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            @error('brand_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div> --}}

                        <!-- Caution -->
                        <div class="full-width">
                            <label for="caution" class="form-label"><i class="bi bi-exclamation-triangle me-1"></i>Caution</label>
                            <textarea class="form-control @error('caution') is-invalid @enderror" name="caution" id="caution" rows="2" placeholder="Enter caution (e.g., contains allergens)">{{ old('caution') }}</textarea>
                            @error('caution')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Branch-Specific Prices -->
                        <div class="full-width branch-price-section">
                            <h5 class="mb-3"><i class="bi bi-shop me-1"></i>Branch-Specific Prices (Optional)</h5>
                            <div class="row g-3">
                                @foreach ($branches as $branch)
                                    <div class="col-md-6">
                                        <label for="branch_price_{{ $branch->id }}" class="form-label">{{ $branch->name }}</label>
                                        <div class="input-group">
                                            <input type="number" step="0.01" class="form-control @error('branch_prices.' . $branch->id) is-invalid @enderror" name="branch_prices[{{ $branch->id }}]" id="branch_price_{{ $branch->id }}" value="{{ old('branch_prices.' . $branch->id) }}" placeholder="Use default price">
                                            <span class="input-group-text">$</span>
                                        </div>
                                        @error('branch_prices.' . $branch->id)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sticky Submit Button -->
                    <div class="sticky-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-red">
                            <i class="bi bi-plus-circle me-1"></i>Create Item
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Clear invalid feedback on input change
        document.querySelectorAll('.form-control, .form-select').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
            });
        });
    </script>
</body>
</html>