<?php

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Brand;
use App\Models\Branch;
use App\Services\BranchService;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\BranchResource;

class BranchController extends Controller
{
    public BranchService $branchService;

    public function __construct(BranchService $branch)
    {
        $this->branchService = $branch;
    }

    public function index(PaginateRequest $request)
    {
        try {
            $branches = $this->branchService->list($request);
            return BranchResource::collection($branches);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function show(Branch $branch)
    {
        try {
            return new BranchResource($branch);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
