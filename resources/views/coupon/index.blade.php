<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon Dashboard</title>
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
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white">
                <h2 class="mb-0"><i class="bi bi-tags me-2"></i>Coupon Dashboard</h2>
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
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Birthday Users Section -->
                <div class="mb-5">
                    <h4><i class="bi bi-gift me-2"></i>Users with Birthdays Today</h4>
                    @if ($birthdayUsers->isEmpty())
                        <p class="text-muted">No users have a birthday today.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($birthdayUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <button class="btn btn-outline-red btn-sm" data-bs-toggle="modal" data-bs-target="#couponModal{{ $user->id }}">
                                                    <i class="bi bi-tag me-1"></i>Create Coupon
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

                <!-- Top Users by Orders Section -->
                <div class="mb-5">
                    <h4><i class="bi bi-trophy me-2"></i>Top Users by Order Count</h4>
                    @if ($topUsers->isEmpty())
                        <p class="text-muted">No users have placed orders yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Order Count</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($topUsers as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->orders_count }}</td>
                                            <td>
                                                <button class="btn btn-outline-red btn-sm" data-bs-toggle="modal" data-bs-target="#couponModal{{ $user->id }}">
                                                    <i class="bi bi-tag me-1"></i>Create Coupon
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modals for Creating Coupons -->
    @foreach ($birthdayUsers->merge($topUsers) as $user)
        <div class="modal fade" id="couponModal{{ $user->id }}" tabindex="-1" aria-labelledby="couponModalLabel{{ $user->id }}" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-red text-white">
                        <h5 class="modal-title" id="couponModalLabel{{ $user->id }}">Create Coupon for {{ $user->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('coupon.create_coupon') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="mb-3">
                                <label for="name{{ $user->id }}" class="form-label">Coupon Name</label>
                                <input type="text" class="form-control" id="name{{ $user->id }}" name="name" value="{{ old('name') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="code{{ $user->id }}" class="form-label">Coupon Code</label>
                                <input type="text" class="form-control" id="code{{ $user->id }}" name="code" value="{{ old('code') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="discount{{ $user->id }}" class="form-label">Discount</label>
                                <input type="number" class="form-control" id="discount{{ $user->id }}" name="discount" step="0.01" value="{{ old('discount') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="start_date{{ $user->id }}" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date{{ $user->id }}" name="start_date" value="{{ old('start_date') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="end_date{{ $user->id }}" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date{{ $user->id }}" name="end_date" value="{{ old('end_date') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="minimum_order{{ $user->id }}" class="form-label">Minimum Order</label>
                                <input type="number" class="form-control" id="minimum_order{{ $user->id }}" name="minimum_order" step="0.01" value="{{ old('minimum_order') }}">
                            </div>
                            <div class="mb-3">
                                <label for="maximum_discount{{ $user->id }}" class="form-label">Maximum Discount</label>
                                <input type="number" class="form-control" id="maximum_discount{{ $user->id }}" name="maximum_discount" step="0.01" value="{{ old('maximum_discount') }}">
                            </div>
                            <div class="mb-3">
                                <label for="limit_per_user{{ $user->id }}" class="form-label">Limit Per User</label>
                                <input type="number" class="form-control" id="limit_per_user{{ $user->id }}" name="limit_per_user" value="{{ old('limit_per_user', 1) }}">
                            </div>
                            <div class="mb-3">
                                <label for="title{{ $user->id }}" class="form-label">Notification Title</label>
                                <input type="text" class="form-control" id="title{{ $user->id }}" name="title" value="{{ old('title') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="description{{ $user->id }}" class="form-label">Notification Description</label>
                                <textarea class="form-control" id="description{{ $user->id }}" name="description" required>{{ old('description') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image{{ $user->id }}" class="form-label">Notification Image (Optional)</label>
                                <input type="file" class="form-control" id="image{{ $user->id }}" name="image" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-red">Create Coupon</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>