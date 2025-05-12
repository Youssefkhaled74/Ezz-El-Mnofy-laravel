<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Order Preparation Time</title>
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
            border: none;
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
        
        .form-control {
            border-radius: 0.375rem;
            border-color: #ced4da;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
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
        
        .nav-link.active {
            color: var(--primary-red) !important;
            font-weight: 600;
        }
        
        .nav-link:hover {
            color: var(--dark-red) !important;
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

        .order-id-link {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 500;
        }

        .order-id-link:hover {
            color: var(--dark-red);
            text-decoration: underline;
        }

        /* Responsive table for mobile */
        @media screen and (max-width: 768px) {
            .table-responsive {
                border: none;
            }
            .table {
                background-color: transparent;
                box-shadow: none;
            }
            .table thead {
                display: none;
            }
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                background-color: white;
                border-radius: 0.375rem;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .table tbody td {
                display: block;
                text-align: left;
                padding: 0.75rem;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }
            .table tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 0.75rem;
                width: 45%;
                font-weight: 600;
                color: var(--primary-red);
                text-transform: uppercase;
            }
            .table tbody td:last-child {
                border-bottom: none;
            }
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
                <h2 class="mb-0"><i class="bi bi-clock-fill me-2"></i>Manage Order Preparation Time</h2>
            </div>
            <div class="card-body">
                <!-- Search Form -->
                <form method="GET" action="{{ route('dashboard.orders.preparation_time') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Search by user name..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-red w-100">
                                <i class="bi bi-funnel me-1"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Current Filters Display -->
                @if (request('search'))
                    <div class="mb-3">
                        <span class="fw-semibold">Current Filters:</span>
                        <span class="badge bg-light text-dark me-2">Search: {{ request('search') }}</span>
                        <a href="{{ route('dashboard.orders.preparation_time') }}" class="text-decoration-none ms-2">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                @endif

                <!-- Success/Error Messages -->
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

                <!-- Orders Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Branch</th>
                                <th>Order Type</th>
                                <th>Status</th>
                                <th>Order Date/Time</th>
                                <th>Preparation Time (min)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td data-label="Order ID">
                                        <a href="{{ config('app.url') }}/admin/online-order/show/{{ $order->id }}" class="order-id-link">
                                            {{ $order->id }}
                                        </a>
                                    </td>
                                    <td data-label="User">{{ $order->user ? $order->user->name : 'N/A' }}</td>
                                    <td data-label="Branch">{{ $order->branch ? $order->branch->name : 'N/A' }}</td>
                                    <td data-label="Order Type">
                                        @switch($order->order_type)
                                            @case(\App\Enums\OrderType::DELIVERY)
                                                Delivery
                                                @break
                                            @case(\App\Enums\OrderType::TAKEAWAY)
                                                Takeaway
                                                @break
                                            @case(\App\Enums\OrderType::POS)
                                                Point of Sale
                                                @break
                                            @case(\App\Enums\OrderType::DINING_TABLE)
                                                Dine-In
                                                @break
                                            @default
                                                Unknown
                                        @endswitch
                                    </td>
                                    <td data-label="Status">
                                        @switch($order->status)
                                            @case(\App\Enums\OrderStatus::PENDING)
                                                Pending
                                                @break
                                            @case(\App\Enums\OrderStatus::ACCEPT)
                                                Accepted
                                                @break
                                            @case(\App\Enums\OrderStatus::PROCESSING)
                                                Processing
                                                @break
                                            @case(\App\Enums\OrderStatus::OUT_FOR_DELIVERY)
                                                Out for Delivery
                                                @break
                                            @case(\App\Enums\OrderStatus::DELIVERED)
                                                Delivered
                                                @break
                                            @case(\App\Enums\OrderStatus::CANCELED)
                                                Canceled
                                                @break
                                            @case(\App\Enums\OrderStatus::REJECTED)
                                                Rejected
                                                @break
                                            @case(\App\Enums\OrderStatus::RETURNED)
                                                Returned
                                                @break
                                            @default
                                                Unknown
                                        @endswitch
                                    </td>
                                    <td data-label="Order Date/Time">
                                        {{ $order->order_datetime->format('Y-m-d H:i') }}
                                    </td>
                                    <td data-label="Preparation Time">
                                        {{ $order->preparation_time ?? 'Not Set' }}
                                    </td>
                                    <td data-label="Action">
                                        <form action="{{ route('dashboard.orders.update_preparation_time', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('POST')
                                            <div class="input-group input-group-sm">
                                                <input type="number" name="preparation_time" class="form-control" value="{{ $order->preparation_time ?? '' }}" min="0" required>
                                                <button type="submit" class="btn btn-red">Update</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>