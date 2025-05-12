<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggleFavorite(Request $request)
    {
        $user = Auth::user();
        $itemId = $request->input('item_id');
        
        try {
            $item = Item::findOrFail($itemId);
            
            if ($user->favorites()->where('item_id', $itemId)->exists()) {
                $user->favorites()->detach($itemId);
                return response()->json(['message' => 'Item removed from favorites.'], 200);
            } else {
                $user->favorites()->attach($itemId);
                return response()->json(['message' => 'Item added to favorites.'], 200);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found.'], 404);
        }
    }

    public function getFavorites(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);
        
        // Get paginated favorites with item data
        $favorites = $user->favorites()->with([
            'category',
            'tax',
            'variations.itemAttribute',
            'extras',
            'addons',
            'offer'
        ])->paginate($perPage);
        
        // Transform through ItemResource
        return ItemResource::collection($favorites);
    }
}