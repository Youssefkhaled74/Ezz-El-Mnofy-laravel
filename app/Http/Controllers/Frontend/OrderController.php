<?php

namespace App\Http\Controllers\Frontend;


use Exception;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Address;
use App\Models\FrontendOrder;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Requests\PaginateRequest;
use App\Services\FrontendOrderService;
use App\Http\Requests\OrderStatusRequest;
use App\Http\Resources\OrderDetailsResource;
use App\Events\SendOrderMail;
use App\Events\SendOrderPush;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;
use App\Enums\OrderStatus;
use Smartisan\Settings\Facades\Settings;
use App\Models\Tax;
use App\Models\Item;
use App\Enums\TaxType;
use App\Models\OrderCoupon;
use App\Events\SendOrderSms;
use App\Models\OrderAddress;
use App\Libraries\AppLibrary;

class OrderController extends Controller
{
    private FrontendOrderService $frontendOrderService;

    public function __construct(FrontendOrderService $frontendOrderService)
    {
        $this->frontendOrderService = $frontendOrderService;
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return OrderResource::collection($this->frontendOrderService->myOrder($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(OrderRequest $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\Routing\ResponseFactory|OrderDetailsResource
    {
        try {
            DB::transaction(function () use ($request, &$order) {
                // إنشاء الطلب
                $order = Order::create(
                    $request->validated() + [
                        'user_id'          => Auth::user()->id,
                        'status'           => OrderStatus::PENDING,
                        'order_datetime'   => date('Y-m-d H:i:s'),
                        'preparation_time' => Settings::group('order_setup')->get('order_setup_food_preparation_time')
                    ]
                );

                $i            = 0;
                $totalTax     = 0;
                $itemsArray   = [];
                $requestItems = json_decode($request->items);
                $items        = Item::get()->pluck('tax_id', 'id');
                $taxes        = AppLibrary::pluck(Tax::get(), 'obj', 'id');

                if (!blank($requestItems)) {
                    foreach ($requestItems as $item) {
                        $taxId    = isset($items[$item->item_id]) ? $items[$item->item_id] : 0;
                        $taxName  = isset($taxes[$taxId]) ? $taxes[$taxId]->name : null;
                        $taxRate  = isset($taxes[$taxId]) ? $taxes[$taxId]->tax_rate : 0;
                        $taxType  = isset($taxes[$taxId]) ? $taxes[$taxId]->type : TaxType::FIXED;
                        $taxPrice = $taxType === TaxType::FIXED ? $taxRate : ($item->total_price * $taxRate) / 100;

                        $itemsArray[$i] = [
                            'order_id'             => $order->id,
                            'branch_id'            => $item->branch_id,
                            'item_id'              => $item->item_id,
                            'quantity'             => $item->quantity,
                            'discount'             => (float)$item->discount,
                            'tax_name'             => $taxName,
                            'tax_rate'             => $taxRate,
                            'tax_type'             => $taxType,
                            'tax_amount'           => $taxPrice,
                            'price'                => $item->item_price,
                            'item_variations'      => json_encode($item->item_variations),
                            'item_extras'          => json_encode($item->item_extras),
                            'instruction'          => $item->instruction,
                            'item_variation_total' => $item->item_variation_total,
                            'item_extra_total'     => $item->item_extra_total,
                            'total_price'          => $item->total_price,
                        ];
                        $totalTax += $taxPrice;
                        $i++;
                    }
                }

                if (!blank($itemsArray)) {
                    OrderItem::insert($itemsArray);
                }

                // تحديث رقم الطلب والضرائب
                $order->order_serial_no = date('dmy') . $order->id;
                $order->total_tax       = $totalTax;
                $order->save();

                // حفظ عنوان الطلب إن وُجد
                if ($request->address_id) {
                    $address = Address::find($request->address_id);
                    if ($address) {
                        OrderAddress::create([
                            'order_id'  => $order->id,
                            'user_id'   => Auth::user()->id,
                            'label'     => $address->label,
                            'address'   => $address->address,
                            'apartment' => $address->apartment,
                            'latitude'  => $address->latitude,
                            'longitude' => $address->longitude
                        ]);
                    }
                }

                // تطبيق الكوبون إن وُجد
                if ($request->coupon_id > 0) {
                    OrderCoupon::create([
                        'order_id'  => $order->id,
                        'coupon_id' => $request->coupon_id,
                        'user_id'   => Auth::user()->id,
                        'discount'  => $request->discount
                    ]);
                }

                // إرسال الإشعارات الخاصة بالطلب
                //SendOrderMail::dispatch(['order_id' => $order->id, 'status' => $order->status]);
                //SendOrderSms::dispatch(['order_id' => $order->id, 'status' => $order->status]);
                SendOrderPush::dispatch(['order_id' => $order->id, 'status' => $order->status]);
			//dd($request->status);
			});

            return new OrderDetailsResource($order);
        } catch (Exception $exception) {
            // في حال وجود خطأ نقوم بالـ rollback تلقائياً من داخل transaction
            Log::info($exception->getMessage());
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function storeold(OrderRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {

        try {

            return new OrderDetailsResource($this->frontendOrderService->myOrderStore($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(FrontendOrder $frontendOrder): \Illuminate\Http\Response|OrderDetailsResource|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {

        try {
            return new OrderDetailsResource($this->frontendOrderService->show($frontendOrder));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeStatus(FrontendOrder $frontendOrder, OrderStatusRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new OrderDetailsResource($this->frontendOrderService->changeStatus($frontendOrder, $request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function checkAddressZone(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'address_id' => 'required|exists:addresses,id',
            ]);

            // Fetch the address
            $address = Address::findOrFail($request->address_id);

            // Get the user's coordinates from the address
            $userLat = floatval($address->latitude);
            $userLon = floatval($address->longitude);

            // Fetch all active areas with their branches
            $areas = Area::where('is_active', 1)->with('branch')->get();

            if ($areas->isEmpty()) {
                return response()->json([
                    'message' => 'No delivery zones are available.',
                    'is_in_zone' => false,
                ], 200);
            }

            // Check if the address is inside any area
            $isInZone = false;
            $areaName = null;
            $deliveryFee = null;
            $branchId = null;
            $branchName = null;

            foreach ($areas as $area) {
                // Decode the points JSON
                $coordinates = json_decode($area->points, true);

                // Validate the coordinates structure
                if (!is_array($coordinates) || empty($coordinates)) {
                    Log::warning('Invalid or empty coordinates for area ' . $area->id, ['points' => $area->points]);
                    continue; // Skip this area
                }

                // Transform coordinates to [lat, lon] format
                $transformedCoordinates = array_map(function ($point) {
                    return [floatval($point['lat']), floatval($point['lng'])];
                }, $coordinates);

                // Ensure each coordinate pair has both latitude and longitude
                $validCoordinates = true;
                foreach ($transformedCoordinates as $point) {
                    if (!is_array($point) || count($point) < 2 || !isset($point[0], $point[1])) {
                        $validCoordinates = false;
                        break;
                    }
                }

                if (!$validCoordinates) {
                    Log::warning('Invalid coordinate structure for area ' . $area->id, ['transformed_coordinates' => $transformedCoordinates]);
                    continue; // Skip this area
                }

                // Check if the point is inside the polygon
                if ($this->isPointInPolygon($userLat, $userLon, $transformedCoordinates)) {
                    $isInZone = true;
                    $areaName = $area->name;
                    $deliveryFee = $area->delivery_fees;
                    $branchId = $area->branch->id ?? null;
                    $branchName = $area->branch->name ?? null;
                    break;
                }
            }

            // Prepare the response
            $response = [
                'message' => $isInZone ? 'Address is within an active zone.' : 'The address is outside all active zones.',
                'is_in_zone' => $isInZone,
            ];

            if ($isInZone) {
                $response['data'] = [
                    'area_name' => $areaName,
                    'branch_name' => $branchName,
                    'branch_id' => $branchId,
                    'delivery_fee' => $deliveryFee,
                ];
            }

            return response()->json($response, 200);
        } catch (Exception $exception) {
            Log::error('Error checking address zone: ' . $exception->getMessage());
            return response()->json([
                'message' => 'An error occurred while checking the address zone.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    // Helper method to check if a point is inside a polygon
    private function isPointInPolygon($latitude, $longitude, $coordinates)
    {
        $vertices = count($coordinates);
        $inside = false;

        for ($i = 0, $j = $vertices - 1; $i < $vertices; $j = $i++) {
            $xi = $coordinates[$i][1]; // Longitude
            $yi = $coordinates[$i][0]; // Latitude
            $xj = $coordinates[$j][1];
            $yj = $coordinates[$j][0];

            $intersect = (($yi > $latitude) != ($yj > $latitude)) &&
                ($longitude < ($xj - $xi) * ($latitude - $yi) / ($yj - $yi) + $xi);
            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    public function areas()
    {
        $areas = Area::with('branch')->get();

        $response = $areas->map(function ($area) {
            return [
                'id' => $area->id,
                'branch_id' => $area->branch_id,
                'name' => $area->name,
                'points' => $area->points,
                'delivery_fees' => $area->delivery_fees,
                'is_active' => $area->is_active,
                'branch' => [
                    'id' => $area->branch->id,
                    'name' => $area->branch->name,
                    'email' => $area->branch->email,
                    'phone' => $area->branch->phone,
                    'latitude' => $area->branch->latitude,
                    'longitude' => $area->branch->longitude,
                    'city' => $area->branch->city,
                    'state' => $area->branch->state,
                    'zip_code' => $area->branch->zip_code,
                    'address' => $area->branch->address,
                    'status' => $area->branch->status,
                    'brand_id' => $area->branch->brand_id,
                ],
            ];
        });

        return response()->json($response);
    }
}
