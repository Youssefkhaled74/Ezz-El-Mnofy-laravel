<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chef Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Orders in Processing</h4>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Branch</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>{{ $order->order_serial_no }}</td>
                                <td>{{ $order->branch->name ?? 'N/A' }}</td>
                                <td>{{ number_format($order->total, 2) }}</td>
                                <td>
                                    @if ($order->status == 1)
                                        Pending
                                    @elseif ($order->status == 4)
                                        Accepted
                                    @elseif ($order->status == 7)
                                        Processing
                                    @elseif ($order->status == 10)
                                        Out for Delivery
                                    @elseif ($order->status == 13)
                                        Delivered
                                    @elseif ($order->status == 16)
                                        Canceled
                                    @elseif ($order->status == 19)
                                        Rejected
                                    @elseif ($order->status == 22)
                                        Returned
                                    @else
                                        Unknown
                                    @endif
                                </td>
                               <td>
    @if ($order->status == 4 || $order->status == 7)
        <form action="{{ route('chef.orders.updateStatus', $order) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-check form-switch">
                <input type="hidden" name="status" value="7">
                <input type="checkbox" class="form-check-input" id="statusSwitch{{ $order->id }}" 
                       name="toggle" 
                       onchange="this.form.submit()"
                       {{ ($order->status == 7) ? 'checked disabled' : '' }}>
                <label class="form-check-label" for="statusSwitch{{ $order->id }}">Processing</label>
            </div>
        </form>
    @elseif (in_array($order->status, [10, 13, 16, 19, 22]))
        <span class="text-muted">No Action Available</span>
    @else
        <form action="{{ route('chef.orders.updateStatus', $order) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-check form-switch">
                <input type="hidden" name="status" value="7">
                <input type="checkbox" class="form-check-input" id="statusSwitch{{ $order->id }}" 
                       name="toggle" 
                       onchange="this.form.submit()"
                       checked disabled>
                <label class="form-check-label" for="statusSwitch{{ $order->id }}">Processing</label>
            </div>
        </form>
    @endif
</td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <form method="POST" action="{{ route('chef.logout') }}">
			@csrf
			<button type="submit" class="btn btn-danger">Logout</button>
		</form>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>