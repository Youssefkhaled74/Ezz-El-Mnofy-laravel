<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Ratings</title>
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
            padding: 0.4rem 1.2rem;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.9rem;
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
            border: none;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            border-bottom: none;
            padding: 1rem 1.25rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .form-label {
            color: var(--dark-gray);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-control {
            border-radius: 0.375rem;
            border-color: #ced4da;
            padding: 0.4rem 0.6rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
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
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .table td {
            vertical-align: middle;
            padding: 0.5rem;
            font-size: 0.85rem;
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

        .rating-stars .fa-star {
            color: #ddd;
        }

        .rating-stars .fa-star.filled {
            color: #ffc107;
        }

        .photo-preview {
            cursor: pointer;
            max-width: 40px;
            max-height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .modal-photo {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

        /* Rating Statistics Section */
        .rating-card .card-body {
            padding: 1rem;
        }

        .rating-card .card-title {
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .rating-value {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .rating-stars {
            font-size: 0.8rem;
        }

        .rating-progress {
            height: 0.5rem;
        }

        .rating-progress .progress-bar {
            background-color: #28a745;
        }

        .rating-green { color: #28a745; }
        .rating-yellow { color: #ffc107; }
        .rating-red { color: var(--primary-red); }

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
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .table tbody td {
                display: block;
                text-align: left;
                padding: 0.5rem;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 40%;
                font-size: 0.8rem;
            }

            .table tbody td::before {
                content: attr(data-label);
                position: absolute;
                left: 0.5rem;
                width: 35%;
                font-weight: 600;
                color: var(--primary-red);
                text-transform: uppercase;
                font-size: 0.75rem;
            }

            .table tbody td:last-child {
                border-bottom: none;
            }
        }

        /* Table fixed layout */
        .table-fixed-layout {
            table-layout: fixed;
        }

        .table-fixed-layout th,
        .table-fixed-layout td {
            width: auto;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Adjust column widths */
        .th-comment { 
            width: 200px !important;
            white-space: normal !important;
        }

        .th-user { width: 120px !important; }
        .th-order-id { width: 60px !important; }
        .th-order-type { width: 80px !important; } /* Added for Order Type */
        .th-delivery-time { width: 90px !important; }
        .th-food-quality { width: 90px !important; }
        .th-experience { width: 100px !important; }
        .th-packing { width: 80px !important; }
        .th-service { width: 100px !important; }
        .th-photo { width: 50px !important; }
    </style>
</head>

<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="container py-3">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white d-flex justify-content-between align-items-center">
                <h2 class="mb-0 fs-5"><i class="bi bi-star-fill me-2"></i>Order Ratings</h2>
                <a href="{{ route('dashboard.order_ratings', array_merge(request()->query(), ['export' => 'true'])) }}" class="btn btn-light btn-sm">
                    <i class="bi bi-file-earmark-excel me-1"></i>Export to Excel
                </a>
            </div>
            <div class="card-body">
                <!-- Rating Statistics -->
                <div class="mb-4">
                    <h4 class="mb-3 fs-6">Rating Statistics</h4>
                    @if ($averages->total_ratings > 0)
                        <div class="row g-2">
                            <!-- Delivery Time -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Delivery Time</h6>
                                        <p class="rating-value {{ $averages->avg_delivery_time >= 4 ? 'rating-green' : ($averages->avg_delivery_time >= 3 ? 'rating-yellow' : 'rating-red') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Based on {{ $averages->total_ratings }} reviews">
                                            {{ number_format($averages->avg_delivery_time, 2) }} / 5
                                        </p>
                                        <div class="rating-stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averages->avg_delivery_time) ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="progress rating-progress">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ ($averages->avg_delivery_time / 5) * 100 }}%"
                                                aria-valuenow="{{ $averages->avg_delivery_time }}" aria-valuemin="0"
                                                aria-valuemax="5">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">{{ number_format($positivePercentages->positive_delivery_time, 1) }}% rated 4+</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Food Quality -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Food Quality</h6>
                                        <p class="rating-value {{ $averages->avg_food_quality >= 4 ? 'rating-green' : ($averages->avg_food_quality >= 3 ? 'rating-yellow' : 'rating-red') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Based on {{ $averages->total_ratings }} reviews">
                                            {{ number_format($averages->avg_food_quality, 2) }} / 5
                                        </p>
                                        <div class="rating-stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averages->avg_food_quality) ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="progress rating-progress">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ ($averages->avg_food_quality / 5) * 100 }}%"
                                                aria-valuenow="{{ $averages->avg_food_quality }}" aria-valuemin="0"
                                                aria-valuemax="5">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">{{ number_format($positivePercentages->positive_food_quality, 1) }}% rated 4+</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Overall Experience -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Overall Experience</h6>
                                        <p class="rating-value {{ $averages->avg_overall_experience >= 4 ? 'rating-green' : ($averages->avg_overall_experience >= 3 ? 'rating-yellow' : 'rating-red') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Based on {{ $averages->total_ratings }} reviews">
                                            {{ number_format($averages->avg_overall_experience, 2) }} / 5
                                        </p>
                                        <div class="rating-stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averages->avg_overall_experience) ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="progress rating-progress">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ ($averages->avg_overall_experience / 5) * 100 }}%"
                                                aria-valuenow="{{ $averages->avg_overall_experience }}"
                                                aria-valuemin="0" aria-valuemax="5">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">{{ number_format($positivePercentages->positive_overall_experience, 1) }}% rated 4+</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Packing -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Packing</h6>
                                        <p class="rating-value {{ $averages->avg_packing >= 4 ? 'rating-green' : ($averages->avg_packing >= 3 ? 'rating-yellow' : 'rating-red') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Based on {{ $averages->total_ratings }} reviews">
                                            {{ number_format($averages->avg_packing, 2) }} / 5
                                        </p>
                                        <div class="rating-stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averages->avg_packing) ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="progress rating-progress">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ ($averages->avg_packing / 5) * 100 }}%"
                                                aria-valuenow="{{ $averages->avg_packing }}" aria-valuemin="0"
                                                aria-valuemax="5">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">{{ number_format($positivePercentages->positive_packing, 1) }}% rated 4+</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Delivery Service -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Delivery Service</h6>
                                        <p class="rating-value {{ $averages->avg_delivery_service >= 4 ? 'rating-green' : ($averages->avg_delivery_service >= 3 ? 'rating-yellow' : 'rating-red') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Based on {{ $averages->total_ratings }} reviews">
                                            {{ number_format($averages->avg_delivery_service, 2) }} / 5
                                        </p>
                                        <div class="rating-stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averages->avg_delivery_service) ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="progress rating-progress">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ ($averages->avg_delivery_service / 5) * 100 }}%"
                                                aria-valuenow="{{ $averages->avg_delivery_service }}"
                                                aria-valuemin="0" aria-valuemax="5">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">{{ number_format($positivePercentages->positive_delivery_service, 1) }}% rated 4+</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Overall Average Rating -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Overall Average</h6>
                                        <p class="rating-value {{ $averages->avg_overall_rating >= 4 ? 'rating-green' : ($averages->avg_overall_rating >= 3 ? 'rating-yellow' : 'rating-red') }}"
                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                            title="Based on {{ $averages->total_ratings }} reviews">
                                            {{ number_format($averages->avg_overall_rating, 2) }} / 5
                                        </p>
                                        <div class="rating-stars mb-2">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($averages->avg_overall_rating) ? 'filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="progress rating-progress">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                style="width: {{ ($averages->avg_overall_rating / 5) * 100 }}%"
                                                aria-valuenow="{{ $averages->avg_overall_rating }}"
                                                aria-valuemin="0" aria-valuemax="5">
                                            </div>
                                        </div>
                                        <small class="text-muted mt-1">Across all categories</small>
                                    </div>
                                </div>
                            </div>
                            <!-- Total Ratings -->
                            <div class="col-md-4 col-lg-2">
                                <div class="card h-100 rating-card">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Total Ratings</h6>
                                        <p class="rating-value">{{ $averages->total_ratings }}</p>
                                        <div class="rating-stars mb-2">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <small class="text-muted mt-1">Reviews</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No ratings available for the selected filters.</p>
                    @endif
                </div>

                <!-- Search and Date Filter Form -->
                <form method="GET" action="{{ route('dashboard.order_ratings') }}" class="mb-3">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search by user name..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="start_date" class="form-control" placeholder="Start Date"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <input type="date" name="end_date" class="form-control" placeholder="End Date"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <div class="form-check">
                                <input type="checkbox" name="today" id="todayFilter" class="form-check-input"
                                    {{ request('today') ? 'checked' : '' }}>
                                <label for="todayFilter" class="form-check-label">Today</label>
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
                <div class="mb-3">
                    <span class="fw-semibold fs-6">Current Filters:</span>
                    @if (request('search'))
                        <span class="badge bg-light text-dark me-2">Search: {{ request('search') }}</span>
                    @endif
                    @if (request('today'))
                        <span class="badge bg-light text-dark me-2">Today</span>
                    @elseif (request('start_date') || request('end_date'))
                        <span class="badge bg-light text-dark me-2">
                            Date: {{ request('start_date') ?: 'Any' }} to {{ request('end_date') ?: 'Any' }}
                        </span>
                    @endif
                    @if (request('search') || request('start_date') || request('end_date') || request('today'))
                        <a href="{{ route('dashboard.order_ratings') }}" class="text-decoration-none ms-2">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    @endif
                </div>

                <!-- Ratings Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-fixed-layout">
                        <thead>
                            <tr>
                                <th class="th-user">User</th>
                                <th class="th-order-id">Order ID</th>
                                <th class="th-order-type">Order Type</th>
                                <th class="th-delivery-time">Delivery Time</th>
                                <th class="th-food-quality">Food Quality</th>
                                <th class="th-experience">Overall Experience</th>
                                <th class="th-packing">Packing</th>
                                <th class="th-service">Delivery Service</th>
                                <th class="th-comment">Comment</th>
                                <th class="th-photo">Photo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orderRatings as $rating)
                                <tr>
                                    <td data-label="User">{{ $rating->user->name ?? 'No Name' }}</td>
                                    <td data-label="Order ID">
                                        <a href="{{ config('app.url') }}/admin/online-orders/show/{{ $rating->order_id }}"
                                            class="order-id-link">
                                            {{ $rating->order_id }}
                                        </a>
                                    </td>
                                    <td data-label="Order Type">
                                        {{ $rating->order->order_type ?? 'N/A' }}
                                    </td>
                                    <td data-label="Delivery Time">
                                        <span class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->delivery_time ? 'filled' : '' }}"></i>
                                            @endfor
                                        </span>
                                    </td>
                                    <td data-label="Food Quality">
                                        <span class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->food_quality ? 'filled' : '' }}"></i>
                                            @endfor
                                        </span>
                                    </td>
                                    <td data-label="Overall Experience">
                                        <span class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->overall_experience ? 'filled' : '' }}"></i>
                                            @endfor
                                        </span>
                                    </td>
                                    <td data-label="Packing">
                                        <span class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->packing ? 'filled' : '' }}"></i>
                                            @endfor
                                        </span>
                                    </td>
                                    <td data-label="Delivery Service">
                                        <span class="rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->delivery_service ? 'filled' : '' }}"></i>
                                            @endfor
                                        </span>
                                    </td>
                                    <td data-label="Comment" style="white-space: normal;">
                                        {{ $rating->additional_note }}
                                    </td>
                                    <td data-label="Photo">
                                        @if ($rating->hasMedia('rating_photo'))
                                            <img src="{{ $rating->getFirstMediaUrl('rating_photo') }}"
                                                alt="Rating Photo for Order {{ $rating->order_id }}"
                                                class="photo-preview" data-bs-toggle="modal"
                                                data-bs-target="#photoModal"
                                                data-photo="{{ $rating->getFirstMediaUrl('rating_photo') }}">
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No order ratings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $orderRatings->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Photo Modal -->
    <div class="modal fade" id="photoModal" tabindex="-1" aria-labelledby="photoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="photoModalLabel">Rating Photo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" alt="Rating Photo" class="modal-photo" id="modalPhoto">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- JavaScript for Modal and Today Filter -->
    <script>
        // Handle photo modal display
        document.querySelectorAll('.photo-preview[data-bs-toggle="modal"]').forEach(img => {
            img.addEventListener('click', function() {
                const photoUrl = this.getAttribute('data-photo');
                document.getElementById('modalPhoto').src = photoUrl;
            });
        });

        // Disable date inputs when Today is checked
        document.getElementById('todayFilter').addEventListener('change', function() {
            const startDateInput = document.querySelector('input[name="start_date"]');
            const endDateInput = document.querySelector('input[name="end_date"]');
            if (this.checked) {
                startDateInput.disabled = true;
                endDateInput.disabled = true;
                startDateInput.value = '';
                endDateInput.value = '';
            } else {
                startDateInput.disabled = false;
                endDateInput.disabled = false;
            }
        });
    </script>
</body>

</html>