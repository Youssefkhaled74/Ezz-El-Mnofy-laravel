<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\ChangeImageRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;

class ProfileService
{

    /**
     * @param ProfileRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws Exception
     */
    public function update(ProfileRequest $request)
    {
        try {
            $user = User::find(auth()->user()->id);
            if (blank($user)) {
                throw new Exception('User not found', 404);
            }
    
            // $oldEmail = $user->email;
            // $oldPhone = $user->phone;
            // $oldWhatsapp = $user->whatsapp_phone;
    
            // $newEmail = $request->get('email');
            // $newPhone = $request->get('phone');
            // $newWhatsapp = $request->get('whatsapp_phone');
    
            // $emailChanged = $oldEmail !== $newEmail;
            // $phoneChanged = $oldPhone !== $newPhone;
            // $whatsappChanged = $oldWhatsapp !== $newWhatsapp;
    
            $user->name          = $request->first_name . ' ' . $request->last_name;
            // $user->email         = $newEmail;
            // $user->phone         = $newPhone;
            // $user->whatsapp_phone_number = $newWhatsapp;
            // $user->country_code  = $request->get('country_code');
            $user->birthday      = $request->get('birthday');
    
            // if ($emailChanged) {
            //     $user->is_email_verified = 0;
            // }
    
            // if ($phoneChanged) {
            //     $user->is_phone_verified = 0;
            // }
    
            // if ($whatsappChanged) {
            //     $user->is_whatsapp_verified = 0;
            // }
    
            $user->save();
    
            return $user; 
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
    

    /**
     * @param ChangePasswordRequest $request
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     * @throws Exception  02-02
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $user = User::find(auth()->user()->id);
            $user->password = bcrypt($request->get('password'));
            $user->save();
            return $user;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }

    public function changeImage(ChangeImageRequest $request)
    {
        try {
            $user = User::find(auth()->user()->id);
            if ($request->image) {
                $user->clearMediaCollection('profile');
                $user->addMediaFromRequest('image')->toMediaCollection('profile');
            }
            $user->save();
            return $user;
        } catch (Exception $exception) {
            Log::info($exception->getMessage());
            throw new Exception($exception->getMessage(), 422);
        }
    }
}
