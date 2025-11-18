<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary-red: #dc3545;
            --dark-red: #c82333;
            --light-gray: #f8f9fa;
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
        }

        .btn-red:hover {
            background-color: var(--dark-red);
            border-color: var(--dark-red);
        }

        .card {
            border-radius: 0.5rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
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

        .user-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }

        .search-form {
            max-width: 400px;
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
    @include('layouts.partials.navbar')

    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white">
                <h2 class="mb-0"><i class="bi bi-people me-2"></i>User Dashboard</h2>
            </div>
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="mb-4">
                    <form action="{{ route('dashboard.userMoreData') }}" method="GET" class="search-form">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-red"><i class="bi bi-search"></i></button>
                        </div>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>WhatsApp Number</th>
                                <th>Orders</th>
                                <th>My Referral Code</th>
                                <th>Referred By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td data-label="Photo">
                                        <a href="{{ $user->image }}" target="_blank">
                                            <img src="{{ $user->image }}" alt="{{ $user->name }}" class="user-photo">
                                        </a>
                                    </td>
                                    <td data-label="Name">{{ $user->name }}</td>
                                    <td data-label="Email">{{ $user->email }}</td>
                                    <td data-label="WhatsApp Number">
                                        @if ($user->whatsapp_phone_number)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->whatsapp_phone_number) }}" target="_blank" class="text-decoration-none">
                                                {{ $user->whatsapp_phone_number }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="Orders">{{ $user->orders_count }}</td>
                                    <td data-label="My Referral Code">
                                        @if ($user->my_referral_code)
                                            <a href="{{ url('/register?ref=' . $user->my_referral_code) }}" target="_blank" class="text-decoration-none">
                                                {{ $user->my_referral_code }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td data-label="Referred By">{{ $user->referral_code ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No users found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>