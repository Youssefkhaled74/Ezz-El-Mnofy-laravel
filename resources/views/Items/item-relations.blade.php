<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items - Manage Relations</title>
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

        .btn-outline-red {
            border-color: var(--primary-red);
            color: var(--primary-red);
            padding: 0.5rem 1rem;
            font-weight: 500;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
        }

        .btn-outline-red:hover {
            background-color: var(--primary-red);
            color: white;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
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
                <h4 class="mb-0"><i class="bi bi-box-seam me-2"></i>Items - Manage Relations</h4>
                <a href="{{ route('admin.item.index') }}" class="nav-back-btn">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

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

                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('items.branch-index') }}" class="mb-4">
                    <div class="form-grid">
                        <div>
                            <label for="search" class="form-label"><i class="bi bi-search me-1"></i>Search Items</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search items by name..." value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div>
                            <label for="category_id" class="form-label"><i class="bi bi-tag me-1"></i>Category</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-red w-100">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Current Filters Display -->
                @if ($search || $categoryId)
                    <div class="mb-3">
                        <span class="fw-semibold">Current Filters:</span>
                        @if ($search)
                            <span class="badge bg-light text-dark me-2">Search: {{ $search }}</span>
                        @endif
                        @if ($categoryId)
                            <span class="badge bg-light text-dark">Category: {{ $categories->find($categoryId)->name ?? 'N/A' }}</span>
                        @endif
                        <a href="{{ route('items.branch-index') }}" class="text-decoration-none ms-2">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name (English)</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->name['en'] ?? 'N/A' }}</td>
                                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                                    <td>{{ $item->brand->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $item->status == 5 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->status == 5 ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->thumb)
                                            <img src="{{ $item->thumb }}" alt="{{ $item->name['en'] ?? 'Item' }}" style="width: 50px; border-radius: 0.25rem;">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('items.edit-brand', $item) }}" class="btn btn-sm btn-outline-red">
                                                <i class="bi bi-shop-window me-1"></i>Change Brand
                                            </a>
                                            <a href="{{ route('items.edit', $item->id) }}" class="btn btn-sm btn-outline-red">
                                                <i class="bi bi-pencil me-1"></i>Update
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No items found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links('pagination::bootstrap-5') }}
                </div>
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