<nav class="navbar navbar-expand-lg navbar-light mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="bi bi-shop me-2"></i>Item Management
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
                    <a class="nav-link {{ Route::is('items.branch-index', 'items.edit-branches', 'items.edit-brand') ? 'active' : '' }}" href="{{ route('items.branch-index') }}">
                        <i class="bi bi-box-seam me-1"></i>Items
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('branches.index') ? 'active' : '' }}" href="{{ route('branches.index') }}">
                        <i class="bi bi-geo-alt me-1"></i>Branches
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ Route::is('brands') ? 'active' : '' }}" href="{{ route('brands') }}">
                        <i class="bi bi-bookmark-star me-1"></i>Brands
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