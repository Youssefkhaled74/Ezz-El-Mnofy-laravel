<?php

namespace App\Http\Resources;

use App\Enums\TaxType;
use App\Libraries\AppLibrary;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // Get the branch ID from the request
        $branchId = $request->get('branch_id');

        // Determine the locale and select the appropriate item name
        $locale = $request->get('lang', App::getLocale());
        $itemName = is_string($this->orderItem?->name) 
            ? $this->orderItem->name 
            : ($this->orderItem?->name[$locale] ?? $this->orderItem?->name['en'] ?? '');

        if ($locale === 'fr') {
            $itemName = $this->orderItem?->name['en'] ?? ''; // Use English name for French locale
        }

        // Find branch-specific price if branch_id is provided
        $price = $this->price;
        if ($branchId && $this->orderItem && $this->orderItem->branches) {
            $branch = $this->orderItem->branches->firstWhere('id', $branchId);
            $price = $branch && $branch->pivot->price !== null ? $branch->pivot->price : $this->price;
        }


        return [
            'id'                               => $this->id,
            'order_id'                         => $this->order_id,
            'branch_id'                        => $this->branch_id,
            'item_id'                          => $this->orderItem?->id,
            'item_name'                        => $itemName, // Locale-based item name
            'item_image'                       => $this->orderItem?->thumb,
            'quantity'                         => $this->quantity,
            'discount'                         => AppLibrary::currencyAmountFormat($this->discount),
            'price'                            => AppLibrary::currencyAmountFormat($price), // Branch-specific price
            'item_variations'                  => json_decode($this->item_variations),
            'item_extras'                      => json_decode($this->item_extras),
            'item_variation_currency_total'    => AppLibrary::currencyAmountFormat($this->item_variation_total),
            'item_extra_currency_total'        => AppLibrary::currencyAmountFormat($this->item_extra_total),
            'total_convert_price'              => AppLibrary::convertAmountFormat($this->total_price),
            'total_currency_price'             => AppLibrary::currencyAmountFormat($this->total_price),
            'instruction'                      => $this->instruction,
            'tax_type'                         => $this->tax_type === TaxType::FIXED ? env('CURRENCY') : '%',
            'tax_rate'                         => $this->tax_rate,
            'tax_currency_rate'                => AppLibrary::flatAmountFormat($this->tax_rate),
            'tax_name'                         => $this->tax_name,
            'tax_currency_amount'              => AppLibrary::currencyAmountFormat($this->tax_amount),
            'total_without_tax_currency_price' => AppLibrary::currencyAmountFormat($this->total_price - $this->tax_amount),
        ];
    }
}