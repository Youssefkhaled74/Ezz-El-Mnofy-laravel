<?php

namespace App\Http\Resources;

use App\Models\Message;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $messageId = Message::where('user_id', $this->id)
            ->where('branch_id', 1)
            ->value('id');

        // dd($messageId);

        return [
            "id"               => $this->id,
            "name"             => $this->name,
            "first_name"       => $this->FirstName,
            "last_name"        => $this->LastName,
            "phone"            => $this->phone,
            "whatsapp_phone" => $this->whatsapp_phone_number,
            "Whatsapp_Country_Code" => $this->whatsapp_country_code,
            "email"            => $this->email,
            "username"         => $this->username,
            "birthday"        => AppLibrary::date($this->birthday),  
            "my_referral_code" => $this->my_referral_code,
            "balance"          => AppLibrary::flatAmountFormat($this->balance),
            "currency_balance" => AppLibrary::currencyAmountFormat($this->balance),
            "points"           => $this->points ?? 0,
            "message_id"       => $messageId,
            "image"            => $this->image,
            "role_id"          => $this->myRole,
            "country_code"     => $this->country_code,
            "order"            => $this->orders->count(),
            'email_verified' => (bool) $this->is_email_verified,
            'phone_verified' => (bool) $this->is_phone_verified,
            'whatsapp_verified' => (bool) $this->is_whatsapp_verified,
            'create_date'      => AppLibrary::date($this->created_at),
            'update_date'      => AppLibrary::date($this->updated_at),
        ];
    }
}
