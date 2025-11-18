<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track All Orders</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            max-width: 1200px;
            margin: 0 auto;
        }

        .card-header {
            background-color: var(--primary-red);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0;
        }

        .card-body {
            padding: 1.5rem;
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

        .table tr {
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .table tr:hover {
            background-color: var(--light-gray);
        }

        .status-badge {
            padding: 0.35rem 0.65rem;
            border-radius: 0.375rem;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-completed { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .branch-badge {
            background-color: #6c757d;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.85rem;
        }

        .new-order-alert {
            background-color: #ffeb3b;
            color: #333;
            padding: 0.5rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            display: none;
            position: relative;
        }

        .new-order-alert button {
            margin-left: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-list-check me-2"></i>Track All Orders</h4>
            </div>
            <div class="card-body">
                <!-- Search and Filter Form -->
                <form method="GET" action="{{ route('admin.track-all-orders') }}" class="mb-4">
                    <div class="form-grid">
                        <div>
                            <label class="form-label">Order ID</label>
                            <input type="text" name="order_id" class="form-control" placeholder="Search by order ID..." value="{{ request('order_id') }}">
                        </div>
                        <div>
                            <label class="form-label">Branch</label>
                            <select name="branch_id" class="form-select">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-red w-100">
                                <i class="bi bi-search me-1"></i>Search Orders
                            </button>
                        </div>
                    </div>
                </form>

                <div class="new-order-alert" id="newOrderAlert">
                    New order added within the last 1 minute!
                    <button class="btn btn-sm btn-dark" onclick="this.parentElement.style.display='none';">Okay, I know</button>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Branch</th>
                                <th>Customer</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            @forelse($orders as $order)
                                <tr onclick="window.location.href = '{{ url('/admin/online-orders/show/' . $order->id) }}'">
                                    <td>{{ $order->order_serial_no }}</td>
                                    <td><span class="branch-badge">{{ $order->branch->name }}</span></td>
                                    <td>{{ $order->user->name }}</td>
                                    <td>{{ number_format($order->total, 2) }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($order->status) {
                                                1 => 'status-pending',
                                                7 => 'status-processing',
                                                13 => 'status-completed',
                                                16 => 'status-cancelled',
                                                default => 'status-pending'
                                            };
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $orderStatuses[$order->status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <span class="badge {{ $order->payment_status == 5 ? 'bg-success' : 'bg-warning' }}">
                                            {{ $paymentStatuses[$order->payment_status] ?? 'Unknown' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">No orders found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $orders->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Element for Alert Sound -->
    <audio id="alertSound" src="https://www.soundjay.com/buttons/beep-01a.mp3" preload="auto"></audio>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Object to store notified order IDs with their creation times
        let notifiedOrders = {};

        // Function to refresh the orders table body
        function refreshOrders() {
            $.ajax({
                url: '{{ route('admin.track-all-orders') }}',
                method: 'GET',
                data: {
                    order_id: $('input[name="order_id"]').val(),
                    branch_id: $('select[name="branch_id"]').val(),
                    status: $('select[name="status"]').val()
                },
                success: function(response) {
                    const tbody = $('#ordersTableBody');
                    tbody.html($(response).find('#ordersTableBody').html());
                    checkNewOrders(response);
                },
                error: function() {
                    console.log('Error refreshing orders.');
                }
            });
        }

        // Function to check for new orders within the last 1 minute and play sound
        function checkNewOrders(response) {
            const currentTime = new Date();
            const oneMinuteAgo = new Date(currentTime.getTime() - 1 * 60 * 1000);
            let hasNewOrder = false;

            $(response).find('#ordersTableBody tr').each(function() {
                const orderId = $(this).find('td:first-child').text(); // Order ID column
                const createdAtStr = $(this).find('td:nth-child(6)').text(); // Date column
                const createdAt = new Date(createdAtStr);

                // Check if the order is new (within 1 minute) and not previously notified
                if (createdAt > oneMinuteAgo && !notifiedOrders[orderId]) {
                    hasNewOrder = true;
                    notifiedOrders[orderId] = createdAt; // Store the creation time
                }
            });

            // Clean up notified orders older than 1 minute
            for (let orderId in notifiedOrders) {
                if (currentTime - notifiedOrders[orderId] > 1 * 60 * 1000) {
                    delete notifiedOrders[orderId];
                }
            }

            const alert = $('#newOrderAlert');
            const alertSound = $('#alertSound')[0];
            if (hasNewOrder) {
                alert.show();
                alertSound.play(); // Play the sound once for new orders
            } else if (Object.keys(notifiedOrders).length === 0) {
                alert.hide();
            }
        }

        // Refresh every 1 minute
        setInterval(refreshOrders, 10000);

        // Initial check
        $(document).ready(function() {
            refreshOrders();
        });
    </script>
</body>
</html>