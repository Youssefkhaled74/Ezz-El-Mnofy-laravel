<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Brand for {{ $item->name }}</title>
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
                <h2 class="mb-0"><i class="bi bi-shop-window me-2"></i>Change Brand for {{ $item->name }}</h2>
            </div>
            <div class="card-body">
                <form action="{{ route('items.update-brand', $item) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Brand Selection -->
                    <div class="mb-4">
                        <label for="brand_id" class="form-label fw-semibold"><i
                                class="bi bi-shop-window me-2"></i>Brand</label>
                        <select name="brand_id" id="brand_id" class="form-select" required>
                            <option value="">Select Brand</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ old('brand_id', $item->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-red me-2">
                            <i class="bi bi-check-circle me-2"></i>Update Brand
                        </button>
                        <a href="{{ route('items.branch-index') }}" class="btn btn-outline-red">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
