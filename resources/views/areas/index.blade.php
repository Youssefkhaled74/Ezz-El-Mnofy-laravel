<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Areas for {{ $branch->name }}</title>
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
        
        .form-check-switch {
            display: inline-block;
            position: relative;
            width: 60px;
            height: 34px;
        }
        
        .form-check-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .form-check-switch label {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 34px;
            cursor: pointer;
        }
        
        .form-check-switch input:checked + label {
            background-color: var(--primary-red);
        }
        
        .form-check-switch label::before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }
        
        .form-check-switch input:checked + label::before {
            transform: translateX(26px);
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
                <h2 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Areas for {{ $branch->name }}</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Delivery Fees</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($areas as $area)
                                <tr>
                                    <td>{{ $area->name }}</td>
                                    <td>
                                        <form action="{{ route('areas.update-delivery', $area->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="number" step="0.01" name="delivery_fees" value="{{ $area->delivery_fees }}" class="form-control" style="width: 100px; display: inline-block;">
                                            <button type="submit" class="btn btn-sm btn-red">Update</button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="form-check form-check-switch">
                                            <input type="checkbox" class="form-check-input" id="statusSwitch_{{ $area->id }}" name="is_active" {{ $area->is_active == 1 ? 'checked' : '' }} onchange="this.form.submit()" form="statusForm_{{ $area->id }}">
                                            <label class="form-check-label" for="statusSwitch_{{ $area->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <form id="statusForm_{{ $area->id }}" action="{{ route('areas.update-status', $area->id) }}" method="POST" style="display:none;">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_active" value="{{ $area->is_active === 'active' ? 'inactive' : 'active' }}">
                                        </form>
                                        <a href="{{ route('areas.edit', $area->id) }}" class="btn btn-sm btn-outline-danger">Resize</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('areas.create', ['branch_id' => $branch->id]) }}" class="btn btn-red">Add New Area</a>
                    <a href="{{ route('areas.select-branch') }}" class="btn btn-outline-secondary">Back to Branch Selection</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>