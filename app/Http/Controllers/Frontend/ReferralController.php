<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function getMyReferralCode(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'status'        => true,
            'referral_code' => $user->my_referral_code,
            'message'      => 'Referral code retrieved successfully'
        ]);
    }
}
