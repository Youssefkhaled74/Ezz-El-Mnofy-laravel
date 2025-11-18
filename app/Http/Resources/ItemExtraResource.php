<?php

namespace App\Http\Resources;

use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ItemExtraResource extends JsonResource
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

        // Handle localized name for the extra
        $name = is_array($this->name)
            ? ($this->name[$locale] ?? $this->name['en'] ?? '')
            : ($this->name ?? '');
        if ($locale === 'fr') {
            $name = is_array($this->name) ? ($this->name['en'] ?? '') : ($this->name ?? '');
        }

        // Handle localized name for the related item
        $itemName = optional($this->item)->name;
        $itemName = is_array($itemName)
            ? ($itemName[$locale] ?? $itemName['en'] ?? '')
            : ($itemName ?? '');
        if ($locale === 'fr') {
            $itemName = is_array($this->item->name) ? ($this->item->name['en'] ?? '') : ($this->item->name ?? '');
        }

        return [
            'id'             => $this->id,
            'item_id'        => $this->item_id,
            'name'           => $name,
            'price'          => $this->price,
            'currency_price' => AppLibrary::currencyAmountFormat($this->price),
            'flat_price'     => AppLibrary::flatAmountFormat($this->price),
            'convert_price'  => AppLibrary::convertAmountFormat($this->price),
            'status'         => $this->status,
            'item'           => $itemName,
        ];
    }
}