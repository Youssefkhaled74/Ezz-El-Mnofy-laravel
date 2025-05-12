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

    public function store(OrderRequest $request): \Illuminate\Http\Response | OrderDetailsResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
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
                'branch_id' => 'required|exists:branches,id',
            ]);

            // Fetch the address and branch
            $address = Address::findOrFail($request->address_id);
            $branch = Branch::findOrFail($request->branch_id);

            // Get the user's coordinates from the address
            $userLat = floatval($address->latitude);
            $userLon = floatval($address->longitude);

            // Fetch all active areas for the branch
            $areas = Area::where('branch_id', $branch->id)
                ->where('is_active', 1)
                ->get();

            if ($areas->isEmpty()) {
                return response()->json([
                    'message' => 'No active areas found for the specified branch.',
                    'is_in_zone' => false,
                ], 200);
            }

            // Check if the address is inside any area
            $isInZone = false;
            $areaName = null;
            $deliveryFee = null;

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
                    break;
                }
            }

            // Prepare the response
            $response = [
                'message' => $isInZone ? 'Address is within an active zone.' : 'The address is outside all active zones for the specified branch.',
                'is_in_zone' => $isInZone,
            ];

            if ($isInZone) {
                $response['data'] = [
                    'area_name' => $areaName,
                    'branch_name' => $branch->name,
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
