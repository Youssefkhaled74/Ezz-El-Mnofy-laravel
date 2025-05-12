<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::select('id', 'name', 'logo')->get()->map(function ($brand) {
            return [
                'id'   => $brand->id,
                'name' => $brand->name,
                'logo' => env('APP_URL') . Storage::url('brands/' . $brand->logo),
            ];
        });

        return response()->json($brands);
    }
    public function show(Brand $brand)
    {
        return response()->json([
            'id'   => $brand->id,
            'name' => $brand->name,
            'logo' => env('APP_URL') . Storage::url('brands/' . $brand->logo),
        ]);
    }




    public function indexView()
    {
        $brands = Brand::orderBy('name', 'asc')->paginate(10);
        return view('brands.index', compact('brands'));
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $brand = Brand::create([
            'name' => $validated['name'],
        ]);

        if ($request->hasFile('image')) {
            $brand->addMedia($request->image)->toMediaCollection('brands');
        }

        return redirect()->route('brands')->with('success', 'Brand created successfully.');
    }
    public function edit(Brand $brand)
    {
        return response()->json([
            'id' => $brand->id,
            'name' => $brand->name,
            'logo' => $brand->getFirstMediaUrl('brands'),
        ]);
    }
    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $brand->update([
            'name' => $validated['name'],
        ]);

        if ($request->hasFile('image')) {
            $brand->clearMediaCollection('brands');
            $brand->addMedia($request->image)->toMediaCollection('brands');
        }

        return redirect()->route('brands')->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        if ($brand->branches()->exists() || $brand->categories()->exists() || $brand->items()->exists()) {
            return redirect()->route('brands')->with('error', 'Cannot delete brand with associated branches, categories, or items.');
        }

        $brand->clearMediaCollection('brands');
        $brand->delete();

        return redirect()->route('brands')->with('success', 'Brand deleted successfully.');
    }
}
