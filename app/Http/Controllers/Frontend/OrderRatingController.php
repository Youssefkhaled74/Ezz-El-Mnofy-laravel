<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Order;
use App\Enums\OrderStatus;
use App\Models\OrderRating;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Http\Requests\StoreOrderRatingRequest;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class OrderRatingController extends Controller
{
    public function checkLastOrderRating(): JsonResponse
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json([
                'message' => 'User not authenticated.',
            ], 401);
        }
    
        $lastOrder = Order::where('user_id', $user->id)
            ->where('status', OrderStatus::DELIVERED)
            ->orderBy('order_datetime', 'desc')
            ->first();
    
        if (!$lastOrder) {
            return response()->json([
                'message' => 'No completed orders found for this user.',
                'has_rated' => false,
            ], 404);
        }
    
        $rating = $lastOrder->rating;
    
        if ($rating) {
            $photo = $rating->getFirstMedia('rating_photo') 
                ? $rating->getFirstMedia('rating_photo')->getUrl() 
                : null;
    
            return response()->json([
                'has_rated' => true,
                'order' => new OrderResource($lastOrder),
                'rating' => [
                    'delivery_time' => $rating->delivery_time,
                    'delivery_service' => $rating->delivery_service,
                    'food_quality' => $rating->food_quality,
                    'packing' => $rating->packing,
                    'overall_experience' => $rating->overall_experience,
                    'additional_note' => $rating->additional_note,
                    'photo' => $photo,
                ],
            ], 200);
        }
    
        return response()->json([
            'has_rated' => false,
            'order' => new OrderResource($lastOrder),
        ], 200);
    }

    public function storeOrderRating(StoreOrderRatingRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        // Fetch the order
        $order = Order::where('id', $request->order_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found or does not belong to the user.'], 404);
        }

        // Check if the order has already been rated
        if ($order->rating) {
            return response()->json(['message' => 'This order has already been rated.'], 400);
        }

        // Create the rating
        $rating = OrderRating::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'delivery_time' => $request->delivery_time,
            'delivery_service' => $request->delivery_service,
            'food_quality' => $request->food_quality,
            'packing' => $request->packing,
            'overall_experience' => $request->overall_experience,
            'additional_note' => $request->additional_note,
        ]);

        // Handle photo upload if provided
        if ($request->hasFile('rating_photo')) {
            $rating->addMediaFromRequest('rating_photo')->toMediaCollection('rating_photo');
        }

        // Get the photo URL for the response
        $photo = $rating->getFirstMedia('rating_photo') 
            ? $rating->getFirstMedia('rating_photo')->getUrl() 
            : null;

        return response()->json([
            'message' => 'Order rated successfully.',
            'order' => new OrderResource($order),
            'rating' => [
                'delivery_time' => $rating->delivery_time,
                'delivery_service' => $rating->delivery_service,
                'food_quality' => $rating->food_quality,
                'packing' => $rating->packing,
                'overall_experience' => $rating->overall_experience,
                'additional_note' => $rating->additional_note,
                'photo' => $photo,
            ],
        ], 201);
    }
}