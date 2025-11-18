<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Brand;
use App\Models\Branch;
use App\Services\BranchService;
use App\Models\Area;

use App\Http\Requests\BranchRequest;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\BranchResource;
use Illuminate\Http\Request;

class BranchController extends AdminController
{
    public BranchService $branchService;

    public function __construct(BranchService $branch)
    {
        parent::__construct();
        $this->branchService = $branch;
        $this->middleware(['permission:settings'])->only('store', 'update', 'destroy');
    }

    public function index(
        PaginateRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return BranchResource::collection($this->branchService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(
        Branch $branch
    ): BranchResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new BranchResource($this->branchService->show($branch));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(
        BranchRequest $request
    ): BranchResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new BranchResource($this->branchService->store($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function update(
        BranchRequest $request,
        Branch $branch
    ): BranchResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            return new BranchResource($this->branchService->update($request, $branch));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(
        Branch $branch
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->branchService->destroy($branch);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }













public function NewIndex(Request $request)
{
    $search = $request->query('search');

    $branches = Branch::with('brand')
        ->when($search, function ($query, $search) {
            return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%')
                        ->orWhereHas('brand', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
        })
        ->orderBy('name', 'asc')
        ->paginate(10);

    return view('branches.index', compact('branches'));
}

    public function editBrand(Branch $branch)
    {
        $brands = Brand::orderBy('name', 'asc')->get();
        return view('branches.edit-brand', compact('branch', 'brands'));
    }

    public function updateBrand(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
        ]);
        $branch->update($validated);
        return redirect()->route('branches.index')->with('success', 'Branch brand updated successfully.');
    }




























    public function selectBranchAreas()
    {
        $branches = Branch::all();
        return view('areas.select-branch', compact('branches'));
    }

    public function createAreas(Request $request)
    {
        $branches = Branch::all();
        $selectedBranchId = $request->query('branch_id');
        return view('areas.create', compact('branches', 'selectedBranchId'));
    }

    public function storeAreas(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'delivery_fees' => 'required|numeric|min:0',
            'is_active' => 'nullable|in:on,off', // Adjusted to accept "on" from checkbox
            'points' => 'required|json',
        ]);

        Area::create([
            'branch_id' => $validated['branch_id'],
            'name' => $validated['name'],
            'delivery_fees' => $validated['delivery_fees'],
            'is_active' => isset($validated['is_active']) && $validated['is_active'] === 'on', // Convert "on" to true, absence to false
            'points' => $validated['points'],
        ]);

        return redirect()->route('areas.index', ['branch_id' => $validated['branch_id']])
            ->with('success', 'Area created successfully.');
    }

    public function indexAreas(Request $request)
    {
        $branchId = $request->query('branch_id');
        $branch = Branch::findOrFail($branchId);
        $areas = Area::where('branch_id', $branchId)->get();
        return view('areas.index', compact('branch', 'areas'));
    }

    public function updateStatusAreas(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $area->update([
            'is_active' => !$area->is_active, // Toggle between 1 and 0
        ]);

        return redirect()->route('areas.index', ['branch_id' => $area->branch_id])
            ->with('success', 'Area status updated successfully.');
    }

    public function updateDeliveryAreas(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $validated = $request->validate([
            'delivery_fees' => 'required|numeric|min:0|max:999999.99',
        ]);

        $area->update([
            'delivery_fees' => $validated['delivery_fees'],
        ]);

        return redirect()->route('areas.index', ['branch_id' => $area->branch_id])
            ->with('success', 'Delivery fees updated successfully.');
    }

    public function viewZones(Request $request)
    {
        $branchId = $request->query('branch_id');
        $branch = Branch::findOrFail($branchId);
        $areas = Area::where('branch_id', $branchId)->get();
        return view('areas.view-zones', compact('branch', 'areas'));
    }
    public function editAreas($id)
    {
        $area = Area::findOrFail($id);
        $branch = $area->branch;
        return view('areas.edit', compact('area', 'branch'));
    }

        public function updateAreas(Request $request, $id)
    {
        $area = Area::findOrFail($id);
        $validated = $request->validate([
            'points' => 'required|json',
        ]);

        $area->update([
            'points' => $validated['points'],
        ]);

        return redirect()->route('areas.index', ['branch_id' => $area->branch_id])
            ->with('success', 'Area zone updated successfully.');
    }
}
