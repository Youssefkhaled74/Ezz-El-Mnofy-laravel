<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Branches for {{ $item->name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
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

        /* Enhanced Select2 Styling */
        .select2-container {
            width: 100% !important;
        }
        
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            min-height: 42px;
            padding: 0.25rem 0.5rem;
            transition: all 0.3s ease;
        }
        
        .select2-container--default .select2-selection--multiple:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--primary-red);
            color: white;
            border: none;
            border-radius: 0.25rem;
            padding: 0.25rem 0.5rem;
            margin-top: 0.25rem;
            margin-bottom: 0.25rem;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 0.5rem;
        }
        
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            color: #f8d7da;
        }
        
        .select2-container--default .select2-search--inline .select2-search__field {
            margin-top: 0.5rem;
            height: 26px;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary-red);
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e9ecef;
            color: var(--dark-gray);
        }
        
        .select2-container--default .select2-results__option[aria-selected=true]:hover {
            background-color: var(--primary-red);
            color: white;
        }
        
        /* Branch List Container */
        .branch-list-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-top: 0.5rem;
        }
        
        .branch-item {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #dee2e6;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .branch-item:hover {
            background-color: #f8f9fa;
        }
        
        .branch-item.selected {
            background-color: var(--primary-red);
            color: white;
        }
        
        .branch-item:last-child {
            border-bottom: none;
        }
        
        /* Quick Filter Buttons */
        .quick-filter-buttons {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .quick-filter-btn {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 1rem;
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
                <h2 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Change Branches for {{ $item->name }}</h2>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('items.edit-branches', $item) }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control"
                                    placeholder="Search branches by name..." value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="brand_id" class="form-select">
                                <option value="">All Brands</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ $brandId == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
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
                @if ($search || $brandId)
                    <div class="mb-3">
                        <span class="fw-semibold">Current Filters:</span>
                        @if ($search)
                            <span class="badge bg-light text-dark me-2">Search: {{ $search }}</span>
                        @endif
                        @if ($brandId)
                            <span class="badge bg-light text-dark">Brand:
                                {{ $brands->find($brandId)->name ?? 'N/A' }}</span>
                        @endif
                        <a href="{{ route('items.edit-branches', $item) }}" class="text-decoration-none ms-2">
                            <i class="bi bi-x-circle"></i> Clear Filters
                        </a>
                    </div>
                @endif

                <!-- Branch Selection Form -->
                <form action="{{ route('items.update-branches', $item) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Branch Selection -->
                    <div class="mb-4">
                        <label for="branch_ids" class="form-label fw-semibold">
                            <i class="bi bi-geo-alt me-2"></i>Branches
                            <span class="badge bg-secondary" id="branch-count">
                                {{ $item->branches->count() }} selected
                            </span>
                        </label>

                        <!-- Quick Filter Buttons -->
                        <div class="quick-filter-buttons">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="select-all">
                                <i class="bi bi-check-all me-1"></i>Select All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="deselect-all">
                                <i class="bi bi-x-circle me-1"></i>Deselect All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-filter-btn" data-filter="north">
                                <i class="bi bi-compass me-1"></i>North
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-filter-btn" data-filter="south">
                                <i class="bi bi-compass me-1"></i>South
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-filter-btn" data-filter="east">
                                <i class="bi bi-compass me-1"></i>East
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-filter-btn" data-filter="west">
                                <i class="bi bi-compass me-1"></i>West
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quick-filter-btn" data-filter="central">
                                <i class="bi bi-compass me-1"></i>Central
                            </button>
                        </div>

                        <!-- Enhanced Select2 with Search -->
                        <select name="branch_ids[]" id="branch_ids" class="form-select select2" multiple required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $item->branches->contains($branch->id) ? 'selected' : '' }}
                                    data-region="{{ strtolower($branch->region ?? '') }}"
                                    data-location="{{ $branch->location }}">
                                    {{ $branch->name }} ({{ $branch->location }})
                                </option>
                            @endforeach
                        </select>

                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> Click to select branches. No need to hold Ctrl. Type to search.
                        </div>

                        @error('branch_ids')
                            <div class="alert alert-danger mt-2">
                                <i class="bi bi-exclamation-triangle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-red me-2">
                            <i class="bi bi-check-circle me-2"></i>Update Branches
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
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 with enhanced options
            $('.select2').select2({
                placeholder: "Select branches...",
                allowClear: true,
                width: '100%',
                closeOnSelect: false,
                minimumResultsForSearch: 3,
                templateResult: formatBranch,
                templateSelection: formatBranchSelection
            });

            // Update branch count on change
            $('#branch_ids').on('change', function() {
                const count = $(this).val() ? $(this).val().length : 0;
                $('#branch-count').text(count + ' selected');
                $('#branch-count').toggleClass('bg-secondary', count === 0);
                $('#branch-count').toggleClass('bg-primary', count > 0);
            });

            // Select all branches
            $('#select-all').click(function() {
                $('#branch_ids option').prop('selected', true);
                $('#branch_ids').trigger('change');
                $('#branch_ids').select2('close');
            });

            // Deselect all branches
            $('#deselect-all').click(function() {
                $('#branch_ids option').prop('selected', false);
                $('#branch_ids').trigger('change');
                $('#branch_ids').select2('close');
            });

            // Quick filter buttons
            $('.quick-filter-btn').click(function() {
                const region = $(this).data('filter');
                $('#branch_ids option').each(function() {
                    const optionRegion = $(this).data('region') || '';
                    if (optionRegion.includes(region)) {
                        $(this).prop('selected', true);
                    }
                });
                $('#branch_ids').trigger('change');
                $('#branch_ids').select2('close');
            });

            // Format branch display in dropdown
            function formatBranch(branch) {
                if (!branch.id) return branch.text;
                
                const $branch = $(
                    '<div class="branch-option">' +
                    '<span class="branch-name">' + branch.text + '</span>' +
                    '<small class="text-muted ms-2 branch-location">' + $(branch.element).data('location') + '</small>' +
                    '</div>'
                );
                return $branch;
            }

            // Format selected branch display
            function formatBranchSelection(branch) {
                if (!branch.id) return branch.text;
                return $('<span>' + branch.text.split('(')[0].trim() + '</span>');
            }
        });
    </script>
</body>

</html>