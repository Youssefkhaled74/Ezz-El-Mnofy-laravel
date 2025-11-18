<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class OfferItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // Determine the locale
        $locale = $request->get('lang', App::getLocale());

        // Handle localized offer_item_name
        $offerItemName = optional($this->item)->name;
        $offerItemName = is_string($offerItemName) ? json_decode($offerItemName, true) : $offerItemName;
        $offerItemName = is_array($offerItemName)
            ? ($offerItemName[$locale] ?? $offerItemName['en'] ?? '')
            : ($offerItemName ?? '');
        if ($locale === 'fr') {
            $offerItemName = is_array(optional($this->item)->name) ? (optional($this->item)->name['en'] ?? '') : (optional($this->item)->name ?? '');
        }

        // Handle localized offer_name
        $offerName = optional($this->offer)->name;
        $offerName = is_string($offerName) ? json_decode($offerName, true) : $offerName;
        $offerName = is_array($offerName)
            ? ($offerName[$locale] ?? $offerName['en'] ?? '')
            : ($offerName ?? '');
        if ($locale === 'fr') {
            $offerName = is_array(optional($this->offer)->name) ? (optional($this->offer)->name['en'] ?? '') : (optional($this->offer)->name ?? '');
        }

        return [
            'id'                        => $this->id,
            'offer_id'                  => $this->offer_id,
            'offer_item_id'             => $this->item_id,
            'offer_name'                => $offerName,
            'offer_item_name'           => $offerItemName,
            'offer_item_price'          => optional($this->item)->price,
            'offer_item_flat_price'     => AppLibrary::flatAmountFormat(optional($this->item)->price),
            'offer_item_convert_price'  => AppLibrary::convertAmountFormat(optional($this->item)->price),
            'offer_item_currency_price' => AppLibrary::currencyAmountFormat(optional($this->item)->price),
            'offer_item_status'         => optional($this->item)->status,
        ];
    }
}