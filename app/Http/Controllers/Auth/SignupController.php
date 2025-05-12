<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Activity;
use App\Enums\Ask;
use App\Http\Requests\SignupPhoneRequest;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Exception;
use App\Models\User;
use App\Enums\Status;
use App\Http\Resources\MenuResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\UserResource;
use App\Services\DefaultAccessService;
use App\Services\MenuService;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\OtpManagerService;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignupRequest;
use Illuminate\Support\Facades\Hash;
use Smartisan\Settings\Facades\Settings;

use App\Http\Requests\VerifyPhoneRequest;
use App\Enums\Role as EnumRole;

class SignupController extends Controller
{

    private OtpManagerService $otpManagerService;
    public string $token;
    public DefaultAccessService $defaultAccessService;
    public PermissionService $permissionService;
    public MenuService $menuService;


    public function __construct(
        OtpManagerService $otpManagerService,
        DefaultAccessService $defaultAccessService,
        PermissionService $permissionService,
        MenuService $menuService
    ) {
        $this->otpManagerService = $otpManagerService;
        $this->defaultAccessService = $defaultAccessService;
        $this->permissionService = $permissionService;
        $this->menuService = $menuService;
    }

    public function otp(
        SignupPhoneRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        try {
            $this->otpManagerService->otp($request);
            return response(['status' => true, 'message' => trans("all.message.check_your_phone_for_code")]);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function verify(VerifyPhoneRequest $request): JsonResponse
    {
        try {
            $this->otpManagerService->verify($request);

            $user = User::where('phone', $request->phone)
                ->orWhere('whatsapp_phone_number', $request->phone)
                ->first();

            if (!$user) {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'User not found with the provided phone number.'
                ], 404);
            }

            $isSamePhone = $user->phone === $user->whatsapp_phone_number;

            if ($isSamePhone) {
                $user->is_phone_verified = true;
                $user->is_whatsapp_verified = true;
            } elseif ($request->mawsln === 'is_phone_verified' && $request->phone === $user->phone) {
                $user->is_phone_verified = true;
            } elseif ($request->mawsln === 'is_whatsapp_verified' && $request->phone === $user->whatsapp_phone_number) {
                $user->is_whatsapp_verified = true;
            } else {
                return new JsonResponse([
                    'status' => false,
                    'message' => 'Phone number does not match the selected verification type.'
                ], 422);
            }

            $user->save();

            $this->token = $user->createToken('auth_token')->plainTextToken;

            if (!isset($user->roles[0])) {
                return new JsonResponse([
                    'errors' => ['validation' => trans('all.message.role_exist')]
                ], 400);
            }

            $permission = PermissionResource::collection($this->permissionService->permission($user->roles[0]));
            $defaultPermission = AppLibrary::defaultPermission($permission);

            return new JsonResponse([
                'message'           => trans('all.message.otp_verify_success'),
                'token'             => $this->token,
                'branch_id'         => (int) $user->branch_id,
                'user'              => new UserResource($user),
                'menu'              => MenuResource::collection(collect($this->menuService->menu($user->roles[0]))),
                'permission'        => $permission,
                'defaultPermission' => $defaultPermission,
            ], 201);
        } catch (Exception $exception) {
            return new JsonResponse(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }

    public function register(
        SignupRequest $request
    ): \Illuminate\Http\Response | \Illuminate\Contracts\Foundation\Application | \Illuminate\Contracts\Routing\ResponseFactory {
        $flag = false;
        $otp  = DB::table('otps')->where([
            ['phone', $request->post('phone')]
        ]);

        if (env('DEMO')) {
            $flag = true;
        } else {
            if (Settings::group('site')->get('site_phone_verification') == Activity::DISABLE) {
                $otp?->delete();
                $flag = true;
            } else {
                if (!$otp->exists()) {
                    $flag = true;
                }
            }
        }

        if ($flag) {
            $user = User::where(['phone' => $request->post('phone'), 'is_guest' => Ask::YES])->first();
            $name = AppLibrary::name($request->post('first_name'), $request->post('last_name'));
            if ($user) {
                $user->name     = $name;
                $user->username = Str::slug($name);
                $user->email    = $request->post('email');
                $user->password = Hash::make($request->post('password'));
                $user->is_guest = Ask::NO;
                $user->whatsapp_phone_number = $request->post('whatsapp_phone_number');
                $user->referral_code = $request->post('referral_code');
                $user->birthday = $request->post('birthday');
                $user->whatsapp_country_code = $request->post('whatsapp_country_code');
                $user->discount_id_photo = $request->post('discount_id_photo');
                $user->save();
            } else {
                $user = User::create([
                    'name'              => $name,
                    'username'          => Str::slug($name),
                    'email'             => $request->post('email'),
                    'phone'             => $request->post('phone'),
                    'country_code'      => $request->post('country_code'),
                    'branch_id'         => 0,
                    'email_verified_at' => Carbon::now()->getTimestamp(),
                    'is_guest'          => Ask::NO,
                    'password'          => Hash::make($request->post('password')),
                    'whatsapp_phone_number' => $request->post('whatsapp_phone_number'),
                    'referral_code' => $request->post('referral_code'),
                    'birthday' => $request->post('birthday'),
                    'whatsapp_country_code' => $request->post('whatsapp_country_code'),
                ]);
                if ($request->hasFile('discount_id_photo')) {
                    $user->addMediaFromRequest('discount_id_photo')->toMediaCollection('profile');
                }
                $user->assignRole(EnumRole::CUSTOMER);
            }
            return response(['status' => true, 'message' => trans('all.message.register_successfully')], 201);
        }
        return response(['status' => false, 'message' => trans('all.message.code_is_invalid')], 422);
    }
    public function verifyToSignup(VerifyPhoneRequest $request)
    {
        try {
            $this->otpManagerService->verify($request);

            $user = User::where('phone', $request->phone)->first();
            if ($user) {
                $user->is_phone_verified = true;
                $user->save();
            }

            return response(['status' => true, 'message' => trans("all.message.otp_verify_success_you_are_signup")], 201);
        } catch (Exception $exception) {
            return response(['status' => false, 'message' => $exception->getMessage()], 422);
        }
    }
}
