<?php

namespace App\Http\Controllers\DashboardV2;

use App\Models\User;
use App\Models\OrderRating;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Facades\DB;





class UserProfileController extends Controller
{
    public function index()
    {
        // dd('asda');
        $users = User::all();

        return view('dashboardv2.user', [
            'users' => $users,
        ]);
    }
    public function orderRatings(Request $request)
    {
        // Initialize query for order ratings
        $query = OrderRating::query()->with('user');

        // Apply search filter by user name
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply date filters
        if ($request->input('today')) {
            $query->whereDate('created_at', today());
        } else {
            if ($startDate = $request->input('start_date')) {
                $query->whereDate('created_at', '>=', $startDate);
            }
            if ($endDate = $request->input('end_date')) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        // Get paginated order ratings
        $orderRatings = $query->paginate(10);

        // Build base query for statistics
        $baseQuery = OrderRating::query()
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->input('today'), function ($q) {
                $q->whereDate('created_at', today());
            })
            ->when($request->input('start_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->input('start_date'));
            })
            ->when($request->input('end_date'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->input('end_date'));
            });

        // Calculate average ratings for each category and overall average
        $averages = $baseQuery->select([
            DB::raw('AVG(delivery_time) as avg_delivery_time'),
            DB::raw('AVG(food_quality) as avg_food_quality'),
            DB::raw('AVG(overall_experience) as avg_overall_experience'),
            DB::raw('AVG(packing) as avg_packing'),
            DB::raw('AVG(delivery_service) as avg_delivery_service'),
            DB::raw('(AVG(delivery_time) + AVG(food_quality) + AVG(overall_experience) + AVG(packing) + AVG(delivery_service)) / 5 as avg_overall_rating'),
            DB::raw('COUNT(*) as total_ratings')
        ])->first();

        // Calculate positive percentages (ratings 4+)
        $positivePercentages = $baseQuery->select([
            DB::raw('AVG(CASE WHEN delivery_time >= 4 THEN 1 ELSE 0 END) * 100 as positive_delivery_time'),
            DB::raw('AVG(CASE WHEN food_quality >= 4 THEN 1 ELSE 0 END) * 100 as positive_food_quality'),
            DB::raw('AVG(CASE WHEN overall_experience >= 4 THEN 1 ELSE 0 END) * 100 as positive_overall_experience'),
            DB::raw('AVG(CASE WHEN packing >= 4 THEN 1 ELSE 0 END) * 100 as positive_packing'),
            DB::raw('AVG(CASE WHEN delivery_service >= 4 THEN 1 ELSE 0 END) * 100 as positive_delivery_service'),
        ])->first();

        return view('dashboard_v2.order_ratings', compact('orderRatings', 'averages', 'positivePercentages'));
    }

    public function userMoreData(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'whatsapp_phone_number', 'my_referral_code', 'referral_code')
            ->withCount('orders');

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10);
        return view('dashboard_v2.userData', compact('users'));
    }







    public function managePreparationTime(Request $request)
    {
        $query = Order::with(['user', 'branch'])
            ->whereIn('status', [
                \App\Enums\OrderStatus::PENDING,
                \App\Enums\OrderStatus::PROCESSING,
                \App\Enums\OrderStatus::OUT_FOR_DELIVERY
            ])
            ->orderBy('order_datetime', 'desc');

        // Apply search filter by user name
        if ($search = $request->query('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $orders = $query->paginate(10);

        return view('dashboard_v2.manage_preparation_time', [
            'orders' => $orders,
        ]);
    }
    public function updatePreparationTime(Request $request, Order $order)
    {
        $request->validate([
            'preparation_time' => ['required', 'integer', 'min:0'],
        ]);

        $order->update([
            'preparation_time' => $request->preparation_time,
        ]);

        return redirect()->route('dashboard.orders.preparation_time')->with('success', 'Preparation time updated successfully.');
    }
}
