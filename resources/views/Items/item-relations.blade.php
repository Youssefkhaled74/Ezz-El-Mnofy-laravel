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
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')


    <!-- Main Content -->
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white">
                <h2 class="mb-0"><i class="bi bi-box-seam me-2"></i>Items - Manage Relations</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('items.branch-index') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search items by name..." value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
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
                            <span class="badge bg-light text-dark">Category:
                                {{ $categories->find($categoryId)->name ?? 'N/A' }}</span>
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
                                <th>Name</th>
                                <th>Category</th>
                                <th>Brand</th>
                                <th>Branches</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->category->name ?? 'N/A' }}</td>
                                    <td>{{ $item->brand->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($item->branches->isEmpty())
                                            N/A
                                        @else
                                            {{ $item->branches->pluck('name')->join(', ') }}
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->price, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $item->status == 5 ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $item->status == 5 ? 'Available' : 'Unavailable' }}
                                        </span>
                                    </td>
                                    <td>
                                        <img src="{{ $item->thumb }}" alt="{{ $item->name }}"
                                            style="width: 50px; border-radius: 0.25rem;">
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('items.edit-brand', $item) }}"
                                                class="btn btn-sm btn-outline-red">
                                                <i class="bi bi-shop-window me-1"></i>Change Brand
                                            </a>
                                            <a href="{{ route('items.edit-branches', $item) }}"
                                                class="btn btn-sm btn-outline-red">
                                                <i class="bi bi-geo-alt me-1"></i>Change Branches
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No items found.</td>
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
</body>

</html>
