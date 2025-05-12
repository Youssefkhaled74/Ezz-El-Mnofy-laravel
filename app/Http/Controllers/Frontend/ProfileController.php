<?php

namespace App\Http\Controllers\Frontend;


use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\ProfileService;
use App\Http\Requests\EmailRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Events\SendEmailVerification;
use App\Services\OtpManagerService;
use App\Http\Requests\ProfileRequest;
use Exception;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\VerifyPhoneRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class ProfileController extends Controller
{

    private ProfileService $profileService;
    private OtpManagerService $otpManagerService;


    public function __construct(
        ProfileService $profileService,
        OtpManagerService $otpManagerService,

    ) {
        $this->profileService = $profileService;
        $this->otpManagerService = $otpManagerService;
    }

    public function profile(Request $request): UserResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  new UserResource(auth()->user());
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function update(ProfileRequest $request): UserResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return  new UserResource($this->profileService->update($request));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function updateEmail(EmailRequest $request)
    {
        $email = $request->input('email');

        // $otp = rand(100000, 999999);
        $otp = 1111;
        $token = Str::random(60);

        Otp::create([
            'phone'      => $email,
            'code'       => $otp,
            'token'      => $token,
            'created_at' => Carbon::now(),
        ]);

        event(new SendEmailVerification([
            'email' => $email,
            'pin' => $otp,
        ]));

        return response()->json([
            'message' => 'OTP has been sent to your email address.',
        ]);
    }
    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required|digits:4',
        ]);
        $email = $request->input('email');
        $code = $request->input('otp');

        $otp = Otp::where('phone', $email)
            ->where('code', $code)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->latest()
            ->first();

        if (!$otp) {
            return response()->json(['message' => 'OTP is invalid or expired.'], 422);
        }

        $user = Auth::user();
        $user->email = $email;
        $user->is_email_verified = true;
        $user->save();

        Otp::where('phone', $email)
            ->where('code', $code)
            ->delete();
        return response()->json(['message' => 'Email verified and updated successfully.']);
    }

    public function changePassword(ChangePasswordRequest $request): UserResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {

        try {
            return  new UserResource($this->profileService->changePassword($request));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function changeImage(ChangeImageRequest $request): UserResource | \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory
    {
        try {
            return new UserResource($this->profileService->changeImage($request));
        } catch (\Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
    public function sendOtpToUpdate(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'type'  => ['required', 'in:phone,whatsapp'],
            'code'  => ['required', 'string'],
        ]);

        try {
            $request->merge(['mawsln' => $request->type]);

            $this->otpManagerService->otp($request);

            return response([
                'status' => true,
                'message' => trans("all.message.check_your_phone_for_code"),
            ]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }



    public function verifyOtpToUpdate(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            $type = $request->input('type');
            $phone = $request->input('phone');

            if (!in_array($type, ['phone', 'whatsapp'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid type value.'
                ], 422);
            }

            $request->merge(['mawsln' => $type]);
            $this->otpManagerService->verify($request);

            $user = Auth::user();
            if ($user->phone === $user->whatsapp_phone_number && $user->phone === $request->phone) {
                $user->is_phone_verified = true;
                $user->is_whatsapp_verified = true;
            } elseif ($request->type === 'phone') {
                if ($user->phone !== $request->phone) {
                    $user->phone = $request->phone;
                }
                $user->is_phone_verified = true;
            } elseif ($request->type === 'whatsapp') {
                if ($user->whatsapp_phone_number !== $request->phone) {
                    $user->whatsapp_phone_number = $request->phone;
                }
                $user->is_whatsapp_verified = true;
            }

            $user->save();

            return response()->json([
                'status'  => true,
                'message' => trans('all.message.otp_verify_success'),
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
