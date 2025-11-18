<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Exports\CouponExport;
use App\Services\CouponService;
use App\Models\PushNotification;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CouponRequest;

use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\CouponResource;


class CouponController extends AdminController
{

    private CouponService $couponService;

    public function __construct(CouponService $coupon)
    {
        parent::__construct();
        $this->couponService = $coupon;
        $this->middleware(['permission:coupons'])->only('index', 'export');
        $this->middleware(['permission:coupons_create'])->only('store');
        $this->middleware(['permission:coupons_edit'])->only('update');
        $this->middleware(['permission:coupons_delete'])->only('destroy');
        $this->middleware(['permission:coupons_show'])->only('show');
    }

    public function index(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return CouponResource::collection($this->couponService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(CouponRequest $request): CouponResource | \Illuminate\Http\Response
    {
        try {
            return new CouponResource($this->couponService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(
        Coupon $coupon
    ): CouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new CouponResource($this->couponService->show($coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(
        CouponRequest $request,
        Coupon $coupon
    ): CouponResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new CouponResource($this->couponService->update($request, $coupon));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        Coupon $coupon
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->couponService->destroy($coupon);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return Excel::download(new CouponExport($this->couponService, $request), 'Coupons.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }







    public function indexDash()
    {
        // Users with birthdays today
        $today = Carbon::today();
        $birthdayUsers = User::whereMonth('birthday', $today->month)
            ->whereDay('birthday', $today->day)
            ->get();

        // Top users by order count
        $topUsers = User::select('users.*')
            ->withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        return view('coupon.index', compact('birthdayUsers', 'topUsers'));
    }

    public function createCouponDash(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:coupons,code',
            'discount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
			'end_date' => ['required', 'date', 'after_or_equal:today', 'after_or_equal:start_date'],
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Create the coupon
            $coupon = Coupon::create([
                'name' => $request->name,
                'code' => $request->code,
                'discount' => $request->discount,
                'discount_type' => 1, // Assuming 1 is a valid type; adjust as needed
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'user_id' => $request->user_id,
                'minimum_order' => $request->minimum_order ?? 0,
                'maximum_discount' => $request->maximum_discount ?? null,
                'limit_per_user' => $request->limit_per_user ?? 1,
            ]);

            // Create and send push notification
            $pushNotification = new PushNotification();
            $pushNotification->title = $request->title;
            $pushNotification->description = strip_tags($request->description);
            $pushNotification->role_id = 0; // No role, user-specific
            $pushNotification->user_id = $request->user_id;
            $pushNotification->branch_id = $coupon->user->branch_id ?? 0; // Assuming the user has a branch_id
            $pushNotification->save();

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $pushNotification->clearMediaCollection('pushNotifications');
                $pushNotification->addMediaFromRequest('image')->toMediaCollection('pushNotifications');
            }

            // Fetch device tokens for the specific user
            $fcmWebDeviceToken = User::where(['id' => $pushNotification->user_id])
                ->whereNotNull('web_token')
                ->pluck('web_token')
                ->toArray();
            $fcmMobileDeviceToken = User::where(['id' => $pushNotification->user_id])
                ->whereNotNull('device_token')
                ->pluck('device_token')
                ->toArray();

            $fcmTokenArray = array_merge($fcmWebDeviceToken, $fcmMobileDeviceToken);
            $firebase = new FirebaseService();
            $firebase->sendNotification($pushNotification, $fcmTokenArray, "promotion");

            return redirect()->back()->with('success', 'Coupon created successfully for the selected user, and a notification has been sent.');
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            return redirect()->back()->withErrors(['error' => $exception->getMessage()])->withInput();
        }
    }
}
