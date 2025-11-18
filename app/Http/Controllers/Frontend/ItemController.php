<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Services\ItemService;
use App\Http\Resources\NormalItemResource;

class ItemController extends Controller
{
    public ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        $this->itemService = $itemService;
    }

    public function index(PaginateRequest $request)
    {
        try {
            $branchId = $request->get('branch_id');
            return NormalItemResource::collection($this->itemService->list($request, $branchId));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function featuredItems(PaginateRequest $request)
    {
        try {
            $branchId = $request->get('branch_id');
            return NormalItemResource::collection($this->itemService->featuredItems($branchId));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function mostPopularItems(PaginateRequest $request)
    {
        try {
            $branchId = $request->get('branch_id');
            return NormalItemResource::collection($this->itemService->mostPopularItems($branchId));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}