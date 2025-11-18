<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

        .btn-red:hover, .btn-red:focus {
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
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
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

        .form-control, .form-select {
            border-radius: 0.375rem;
            border-color: #ced4da;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
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

        .offer-image {
            max-width: 100px;
            height: auto;
            border-radius: 0.25rem;
        }

        .btn-delete {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .item-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
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
                <h2 class="mb-0"><i class="bi bi-tag me-2"></i>Offers</h2>
                <a href="{{ route('offers.create') }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Create New Offer
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Alert for AJAX messages -->
                <div id="alert-container"></div>

                <!-- Search Form -->
                <div class="mb-4">
                    <form action="{{ route('offers.index') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, amount, or brand" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-red"><i class="bi bi-search me-2"></i>Search</button>
                        </div>
                        @if (request('search'))
                            <a href="{{ route('offers.index') }}" class="btn btn-outline-red ms-3">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover" id="offers-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount (%)</th>
                                <th>Brand</th>
                                <th>Items</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($offers as $offer)
                                <tr id="offer-{{ $offer->id }}">
                                    <td>{{ $offer->name }}</td>
                                    <td>{{ number_format($offer->amount, 2) }}</td>
                                    <td>{{ $offer->brand->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($offer->items->isNotEmpty())
                                            <div class="item-list">
                                                @foreach ($offer->items as $item)
                                                    <div>{{ $item->name['en'] ?? 'N/A' }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $offer->start_date->format('Y-m-d H:i') }}</td>
                                    <td>{{ $offer->end_date->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-outline-danger btn-delete" onclick="deleteNew('{{ $offer->id }}', '{{ $offer->name }}')">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No offers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $offers->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteNew(offerId, offerName) {
            if (confirm(`Are you sure you want to delete the offer "${offerName}"?`)) {
                fetch(`/offers/${offerId}/delete`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    const alertContainer = document.getElementById('alert-container');
                    if (data.status) {
                        // Remove the row from the table
                        document.getElementById(`offer-${offerId}`).remove();
                        // Show success alert
                        alertContainer.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                    } else {
                        // Show error alert
                        alertContainer.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                ${data.message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `;
                    }
                    // Auto-dismiss alert after 5 seconds
                    setTimeout(() => {
                        alertContainer.innerHTML = '';
                    }, 5000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('alert-container').innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            An error occurred while deleting the offer.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    setTimeout(() => {
                        document.getElementById('alert-container').innerHTML = '';
                    }, 5000);
                });
            }
        }
    </script>
</body>
</html>