<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ezz El Mnofy - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts (Poppins) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #dc3545;
            --dark-red: #c82333;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #333;
            --white: #ffffff;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Poppins', sans-serif;
            color: var(--dark-gray);
        }

        /* Navbar Styles */
        .navbar {
            background-color: var(--white);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 1rem 0;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-red);
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 0.5rem;
        }

        .navbar-nav .nav-link {
            color: var(--dark-gray);
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            color: var(--dark-red);
            transform: translateY(-2px);
        }

        .navbar-nav .nav-link.active {
            color: var(--primary-red);
            font-weight: 600;
            border-bottom: 2px solid var(--primary-red);
        }

        .nav-back-btn {
            color: var(--primary-red);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-back-btn:hover {
            color: var(--dark-red);
            transform: translateX(-5px);
        }

        /* Dashboard Card Styles */
        .dashboard-card {
            background-color: var(--white);
            border-radius: 0.75rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .dashboard-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: linear-gradient(145deg, var(--white), #f9f9f9);
        }

        .dashboard-card i {
            font-size: 3rem;
            color: var(--primary-red);
            margin-bottom: 1.5rem;
        }

        .dashboard-card h5 {
            color: var(--dark-gray);
            font-weight: 600;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .dashboard-card p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0;
        }

        /* Card Header */
        .card-header {
            background-color: var(--primary-red);
            color: var(--white);
            padding: 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .card-header h2 {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .navbar-nav .nav-link {
                padding: 0.75rem 0;
            }

            .dashboard-card i {
                font-size: 2.5rem;
            }

            .dashboard-card h5 {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="https://via.placeholder.com/40x40?text=EEM" alt="Ezz El Mnofy Logo">
                Ezz El Mnofy
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-house-door me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('offers.create') ? 'active' : '' }}" href="{{ route('offers.create') }}">
                            <i class="bi bi-tag me-1"></i>Create Offer
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard.order_ratings') ? 'active' : '' }}" href="{{ route('dashboard.order_ratings') }}">
                            <i class="bi bi-star-fill me-1"></i>Order Ratings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::is('dashboard.userMoreData') ? 'active' : '' }}" href="{{ route('dashboard.userMoreData') }}">
                            <i class="bi bi-people me-1"></i>User More Data
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-back-btn" href="{{ env('APP_URL') }}/admin/dashboard">
                            <i class="fas fa-arrow-left me-1"></i>Back to Home
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white">
                <h2 class="mb-0"><i class="bi bi-house-door me-2"></i>Ezz El Mnofy Dashboard</h2>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('offers.create') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-tag"></i>
                                    <h5 class="card-title">Manage Offers</h5>
                                    <p class="card-text text-muted">Create and manage promotional offers.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('dashboard.order_ratings') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-star-fill"></i>
                                    <h5 class="card-title">Order Ratings</h5>
                                    <p class="card-text text-muted">View customer feedback and ratings.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('dashboard.userMoreData') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-people"></i>
                                    <h5 class="card-title">User More Data</h5>
                                    <p class="card-text text-muted">Access user contact and referral details.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('dashboard.orders.preparation_time') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-clock-fill"></i>
                                    <h5 class="card-title">Preparation Time</h5>
                                    <p class="card-text text-muted">Set order preparation times.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('areas.select-branch') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-map"></i>
                                    <h5 class="card-title">Manage Areas</h5>
                                    <p class="card-text text-muted">Configure areas for branches.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('chef_management.index') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-person"></i>
                                    <h5 class="card-title">Manage Chefs</h5>
                                    <p class="card-text text-muted">Assign and manage chef profiles.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('coupon') }}" class="text-decoration-none">
                            <div class="card dashboard-card">
                                <div class="card-body">
                                    <i class="bi bi-tags"></i>
                                    <h5 class="card-title">Coupon Dashboard</h5>
                                    <p class="card-text text-muted">Administer coupons and promotions.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>