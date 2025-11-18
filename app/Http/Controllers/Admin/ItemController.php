<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Branch;
use App\Models\Tax;
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
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;



class ItemController extends AdminController
{
    use ApiRequestTrait;
    protected $apiRequest;
    public ItemService $itemService;

    public function __construct(ItemService $itemService)
    {
        parent::__construct();
        $this->apiRequest = $this->makeApiRequest();
        $this->itemService = $itemService;
        // $this->middleware(['permission:items'])->only( 'export', 'changeImage');
        // $this->middleware(['permission:items_create'])->only('store');
        // $this->middleware(['permission:items_edit'])->only('update');
        // $this->middleware(['permission:items_delete'])->only('destroy');
        // $this->middleware(['permission:items_show'])->only('show');
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

   // public function update(ItemRequest $request, Item $item): \Illuminate\Http\Response | ItemResource | //\Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
//    {
     //   try {
     //       return new ItemResource($this->itemService->update($request, $item));
    //    } catch (Exception $exception) {
      //      return response(['status' => false, 'message' => $exception->getMessage()], 422);
      //  }
  //  }

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






	    public function createNew()
    {
        $itemCategories = ItemCategory::all();
        $taxes = Tax::all();
        $branches = Branch::all();
        $brands = Brand::all();
        return view('Items.create', compact('itemCategories', 'taxes', 'branches', 'brands'));
    }
	
    public function storeNew(Request $request)
    {
        try {
            $validated = $request->validate([
                'name.en' => 'required|string|max:255',
                'name.ar' => 'required|string|max:255',
                'description.en' => 'nullable|string',
                'description.ar' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'price' => 'required|numeric|min:0',
                'caution' => 'nullable|string',
                'item_category_id' => 'required|exists:item_categories,id',
                'item_type' => 'required|in:1,2',
                'is_featured' => 'required|in:1,2',
                'status' => 'required|in:5,10',
                // 'brand_id' => 'required|exists:brands,id',
                'branch_prices' => 'nullable|array',
                'branch_prices.*' => 'nullable|numeric|min:0',
            ]);

            // Create the item
            $item = Item::create([
                'name' => [
                    'en' => $validated['name']['en'],
                    'ar' => $validated['name']['ar'],
                ],
                'description' => [
                    'en' => $validated['description']['en'] ?? '',
                    'ar' => $validated['description']['ar'] ?? '',
                ],
                'price' => $validated['price'],
                'caution' => $validated['caution'],
                'item_category_id' => $validated['item_category_id'],
                'tax_id' => null, // Explicitly set to null
                'item_type' => $validated['item_type'],
                'is_featured' => $validated['is_featured'],
                'status' => $validated['status'],
                'brand_id' => 1,
                'slug' => Str::slug($validated['name']['en']),
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $item->addMedia($request->file('image'))->toMediaCollection('item');
            }

            // Attach branches with specific prices
            if (!empty($validated['branch_prices'])) {
                $syncData = [];
                foreach ($validated['branch_prices'] as $branchId => $price) {
                    if ($price !== null && $price !== '') {
                        $syncData[$branchId] = ['price' => $price];
                    }
                }
                $item->branches()->sync($syncData);
            }

            // Redirect to the show route with the item ID
            $baseUrl = config('app.url', 'https://peking.evyx.lol'); // Fallback to provided URL
            $showUrl = "{$baseUrl}/admin/items/show/{$item->id}";
            return redirect()->to($showUrl)->with('success', 'Item created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error("Failed to create item: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to create item: ' . $e->getMessage())->withInput();
        }
    }





public function branchIndex(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $query = Item::with(['category', 'brand', 'branches']);

        if ($search) {
            $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.ar')) LIKE ?", ["%{$search}%"]);
        }

        if ($categoryId) {
            $query->where('item_category_id', $categoryId);
        }

        $items = $query->paginate(10);
        $categories = ItemCategory::all();

        return view('Items.item-relations', compact('items', 'categories', 'search', 'categoryId'));
    }

  //  public function editBrand(Item $item)
 //   {
  //      $brands = Brand::orderBy('name', 'asc')->get();
//
     //   return view('Items.edit-brand', compact('item', 'brands'));
    //}
//    public function updateBrand(Request $request, Item $item)
  //  {
  //      $validated = $request->validate([
   //         'brand_id' => 'required|exists:brands,id',
  //      ]);

   //     $item->update($validated);

    //    return redirect()->route('items.branch-index')->with('success', 'Item brand updated successfully.');
   // }


    //public function branchIndex(Request $request)
  //  {
     //   $query = Item::with(['category', 'brand', 'tax', 'branches'])
    //        ->orderBy('order', 'asc');
//
    //    if ($search = $request->input('search')) {
    //        $query->where('name', 'like', '%' . $search . '%');
    //    }

   //     if ($categoryId = $request->input('category_id')) {
     //       $query->where('item_category_id', $categoryId);
    //    }

    //    $items = $query->paginate(10)->appends($request->only(['search', 'category_id']));
     //   $categories = ItemCategory::orderBy('name', 'asc')->get();
        
     //   return view('Items.item-relations', compact('items', 'categories', 'search', 'categoryId'));
  //  }

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

        return view('Items.edit-branches', compact('item', 'branches', 'brands', 'search', 'brandId'));
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
	
	
	
	
	
	
	
	
	
	
	
	public function edit($id)
    {
        $item = Item::with('branches')->findOrFail($id);
        $itemCategories = ItemCategory::all();
        $taxes = Tax::all();
        $branches = Branch::all();
        $brands = Brand::all();

        // Prepare branch prices for the form
        $branchPrices = $item->branches->pluck('pivot.price', 'id')->toArray();

        return view('Items.edit', compact('item', 'itemCategories', 'taxes', 'branches', 'brands', 'branchPrices'));
    }

    /**
     * Update the specified item in storage.
     */
        public function update(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);

            $validated = $request->validate([
                'name.en' => 'required|string|max:255',
                'name.ar' => 'required|string|max:255',
                'description.en' => 'nullable|string',
                'description.ar' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'price' => 'required|numeric|min:0',
                'caution' => 'nullable|string',
                'item_category_id' => 'required|exists:item_categories,id',
                'item_type' => 'required|in:1,2',
                'is_featured' => 'required|in:1,2',
                'status' => 'required|in:5,10',
                'brand_id' => 'required|exists:brands,id',
                'branch_prices' => 'nullable|array',
                'branch_prices.*' => 'nullable|numeric|min:0',
            ]);

            // Prepare description array
            $description = [
                'en' => $validated['description']['en'] ?? '',
                'ar' => $validated['description']['ar'] ?? '',
            ];

            // Log for debugging
            Log::debug("Updating item {$id} with description:", $description);

            // Update the item
            $item->update([
                'name' => [
                    'en' => $validated['name']['en'],
                    'ar' => $validated['name']['ar'],
                ],
                'description' => $description,
                'price' => $validated['price'],
                'caution' => $validated['caution'],
                'item_category_id' => $validated['item_category_id'],
                'tax_id' => null,
                'item_type' => $validated['item_type'],
                'is_featured' => $validated['is_featured'],
                'status' => $validated['status'],
                'brand_id' => $validated['brand_id'],
                'slug' => Str::slug($validated['name']['en']),
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $item->clearMediaCollection('item');
                $item->addMedia($request->file('image'))->toMediaCollection('item');
            }

            // Sync branches with specific prices
            if (!empty($validated['branch_prices'])) {
                $syncData = [];
                foreach ($validated['branch_prices'] as $branchId => $price) {
                    if ($price !== null && $price !== '') {
                        $syncData[$branchId] = ['price' => $price];
                    }
                }
                $item->branches()->sync($syncData);
            } else {
                $item->branches()->detach();
            }

            return redirect()->route('Items.branch-index')->with('success', 'Item updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error("Failed to update item: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to update item: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the brand of an item.
     */
    public function editBrand($id)
    {
        $item = Item::findOrFail($id);
        $brands = Brand::all();
        return view('Items.edit-brand', compact('item', 'brands'));
    }

    /**
     * Update the brand of an item.
     */
    public function updateBrand(Request $request, $id)
    {
        try {
            $item = Item::findOrFail($id);
            $validated = $request->validate([
                'brand_id' => 'required|exists:brands,id',
            ]);

            $item->update(['brand_id' => $validated['brand_id']]);
            return redirect()->route('items.branch-index')->with('success', 'Brand updated successfully.');
        } catch (\Exception $e) {
            Log::error("Failed to update brand: {$e->getMessage()}");
            return redirect()->back()->with('error', 'Failed to update brand: ' . $e->getMessage())->withInput();
        }
    }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
