<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Branch;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Http\Request;

class TrackAllOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query()
            ->with(['branch', 'user'])
            ->withoutGlobalScope('App\Models\Scopes\BranchScope');

        // Apply filters
        if ($request->filled('order_id')) {
            $query->where('order_serial_no', 'like', '%' . $request->order_id . '%');
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            switch($request->status) {
                case 'pending':
                    $query->where('status', OrderStatus::PENDING);
                    break;
                case 'processing':
                    $query->where('status', OrderStatus::PROCESSING);
                    break;
                case 'completed':
                    $query->where('status', OrderStatus::DELIVERED);
                    break;
                case 'cancelled':
                    $query->where('status', OrderStatus::CANCELED);
                    break;
            }
        }

        // Get all branches for the filter dropdown
        $branches = Branch::all();

        // Get orders with pagination
        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        // Map status codes to labels
        $orderStatuses = [
            OrderStatus::PENDING => 'Pending',
            OrderStatus::ACCEPT => 'Accepted',
            OrderStatus::PROCESSING => 'Processing',
            OrderStatus::OUT_FOR_DELIVERY => 'Out for Delivery',
            OrderStatus::DELIVERED => 'Delivered',
            OrderStatus::CANCELED => 'Cancelled',
            OrderStatus::REJECTED => 'Rejected',
            OrderStatus::RETURNED => 'Returned'
        ];

        $paymentStatuses = [
            PaymentStatus::PAID => 'Paid',
            PaymentStatus::UNPAID => 'Unpaid'
        ];

        return view('orders.track-all-orders', compact('orders', 'branches', 'orderStatuses', 'paymentStatuses'));
    }
} 