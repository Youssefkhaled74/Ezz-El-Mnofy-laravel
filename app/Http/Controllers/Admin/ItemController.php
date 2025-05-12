<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Branch;
use App\Exports\ItemExport;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use App\Services\ItemService;
use App\Traits\ApiRequestTrait;
use App\Http\Requests\ItemRequest;
use App\Http\Resources\ItemResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\PaginateRequest;
use App\Http\Requests\ChangeImageRequest;

class ItemController extends AdminController
{
    use ApiRequestTrait;
    protected $apiRequest;
    public ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        parent::__construct();
        //$this->apiRequest = $this->makeApiRequest();
        $this->itemService = $itemService;
         $this->middleware(['permission:items'])->only( 'export', 'changeImage');
         $this->middleware(['permission:items_create'])->only('store');
         $this->middleware(['permission:items_edit'])->only('update');
         $this->middleware(['permission:items_delete'])->only('destroy');
         $this->middleware(['permission:items_show'])->only('show');
    }

    public function index(PaginateRequest $request): \Illuminate\Http\Response | \Illuminate\Http\Resources\Json\AnonymousResourceCollection | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return ItemResource::collection($this->itemService->list($request));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }


    public function show(Item $item): \Illuminate\Http\Response | ItemResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemResource($this->itemService->show($item));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store22222222222(ItemRequest $request): \Illuminate\Http\Response | ItemResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        dd(1);
        try {
            if (env('DEMO')) {
                return new ItemResource($this->itemService->store($request));
            } else {
                if ($this->apiRequest->status) {
                    return new ItemResource($this->itemService->store($request));
                }
                return response(['status' => false, 'message' => $this->apiRequest->message], 422);
            }
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function store(ItemRequest $request): ItemResource
    {
        try {
            return new ItemResource($this->itemService->store($request));
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage(), 422);
        }
    }

    public function update(ItemRequest $request, Item $item): \Illuminate\Http\Response | ItemResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemResource($this->itemService->update($request, $item));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function destroy(Item $item): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            $this->itemService->destroy($item);
            return response('', 202);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request, Item $item): \Illuminate\Http\Response | ItemResource | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new ItemResource($this->itemService->changeImage($request, $item));
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function export(PaginateRequest $request): \Illuminate\Http\Response | \Symfony\Component\HttpFoundation\BinaryFileResponse | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return Excel::download(new ItemExport($this->itemService, $request), 'Item.xlsx');
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }












    public function brandIndex()
    {
        $items = Item::with(['category', 'brand', 'tax'])
            ->orderBy('order', 'asc')
            ->paginate(10);

        return view('items.brand-index', compact('items'));
    }

    public function editBrand(Item $item)
    {
        $brands = Brand::orderBy('name', 'asc')->get();

        return view('items.edit-brand', compact('item', 'brands'));
    }
    public function updateBrand(Request $request, Item $item)
    {
        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
        ]);

        $item->update($validated);

        return redirect()->route('items.branch-index')->with('success', 'Item brand updated successfully.');
    }


    public function branchIndex(Request $request)
    {
        $query = Item::with(['category', 'brand', 'tax', 'branches'])
            ->orderBy('order', 'asc');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($categoryId = $request->input('category_id')) {
            $query->where('item_category_id', $categoryId);
        }

        $items = $query->paginate(10)->appends($request->only(['search', 'category_id']));
        $categories = ItemCategory::orderBy('name', 'asc')->get();
        
        return view('items.item-relations', compact('items', 'categories', 'search', 'categoryId'));
    }

    public function editBranches(Request $request, Item $item)
    {
        $query = Branch::orderBy('name', 'asc');

        if ($search = $request->input('search')) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        if ($brandId = $request->input('brand_id')) {
            $query->where('brand_id', $brandId);
        }

        $branches = $query->get();
        $brands = Brand::orderBy('name', 'asc')->get();

        return view('items.edit-branches', compact('item', 'branches', 'brands', 'search', 'brandId'));
    }

    public function updateBranches(Request $request, Item $item)
    {
        $validated = $request->validate([
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id',
        ]);

        $item->branches()->sync($validated['branch_ids']);

        return redirect()->route('items.branch-index')->with('success', 'Item branches updated successfully.');
    }
}
