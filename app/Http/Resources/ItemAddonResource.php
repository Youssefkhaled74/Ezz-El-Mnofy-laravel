<?php

namespace App\Http\Resources;

use App\Enums\Status;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class ItemAddonResource extends JsonResource
{
    public $variation = 0;

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $this->variation = $this->variationTotal();
        $price = $this?->addonItem?->price;
        $offer = $this->addonItem?->offer?->filter(function ($offer) use ($price) {
            if (Carbon::now()->between(
                $offer->start_date,
                $offer->end_date
            ) && $offer->status == Status::ACTIVE) {
                $amount = ($price - ($price / 100 * $offer->amount));
                $offer->flat_price = AppLibrary::flatAmountFormat($amount);
                $offer->convert_price = AppLibrary::convertAmountFormat($amount);
                $offer->currency_price = AppLibrary::currencyAmountFormat($amount);
                return $offer;
            }
        });
        if (is_null($offer)) {
            $offer = [];
        }
        $total = $this->variation?->price + (count($offer) ? $offer[0]->convert_price : $this->addonItem?->price);

        // Determine the locale
        $locale = $request->get('lang', App::getLocale());

        // Handle localized item_name
        $itemName = optional($this->item)->name;
        $itemName = is_array($itemName)
            ? ($itemName[$locale] ?? $itemName['en'] ?? '')
            : ($itemName ?? '');
        if ($locale === 'fr') {
            $itemName = is_array(optional($this->item)->name) ? (optional($this->item)->name['en'] ?? '') : (optional($this->item)->name ?? '');
        }

        // Handle localized addon_item_name
        $addonItemName = optional($this->addonItem)->name;
        $addonItemName = is_array($addonItemName)
            ? ($addonItemName[$locale] ?? $addonItemName['en'] ?? '')
            : ($addonItemName ?? '');
        if ($locale === 'fr') {
            $addonItemName = is_array(optional($this->addonItem)->name) ? (optional($this->addonItem)->name['en'] ?? '') : (optional($this->addonItem)->name ?? '');
        }

        return [
            'id'                             => $this->id,
            'item_id'                        => $this->item_id,
            'item_addon_id'                  => $this->addon_item_id,
            'item_name'                      => $itemName,
            'addon_item_name'                => $addonItemName,
            'addon_item_price'               => optional($this->addonItem)->price,
            'addon_item_flat_price'          => AppLibrary::flatAmountFormat(optional($this->addonItem)->price),
            'addon_item_convert_price'       => AppLibrary::convertAmountFormat(optional($this->addonItem)->price),
            'addon_item_currency_price'      => AppLibrary::currencyAmountFormat(optional($this->addonItem)->price),
            'addon_item_status'              => optional($this->addonItem)->status,
            'variations'                     => json_decode($this->addon_item_variation),
            'variation_total'                => optional($this->variation)->price,
            'variation_total_flat_price'     => AppLibrary::flatAmountFormat(optional($this->variation)->price),
            'variation_total_convert_price'  => AppLibrary::convertAmountFormat(optional($this->variation)->price),
            'variation_total_currency_price' => AppLibrary::currencyAmountFormat(optional($this->variation)->price),
            'total'                          => $total,
            'total_flat_price'               => AppLibrary::flatAmountFormat($total),
            'total_convert_price'            => AppLibrary::convertAmountFormat($total),
            'total_currency_price'           => AppLibrary::currencyAmountFormat($total),
            'variation_names'                => $this->variation?->name,
            'thumb'                          => $this->addonItem?->thumb,
            'cover'                          => $this->addonItem?->cover,
            'preview'                        => $this->addonItem?->preview,
            'caution'                        => optional($this->addonItem?->caution) == null ? '' : optional($this->addonItem)->caution,
            'offer'                          => SimpleOfferResource::collection($offer)
        ];
    }

    private function variationTotal()
    {
        $variationArray = $this->addonItem?->variations?->mapWithKeys(function ($variation) {
            return [$variation->id => $variation];
        });
        if ($this->addon_item_variation) {
            $variations = (object) json_decode($this->addon_item_variation, true);
            $price = 0;
            $name = [];
            foreach ($variations as $variation) {
                if (isset($variationArray[$variation])) {
                    $name[] = [
                        'id'             => $variationArray[$variation]->id,
                        'name'           => $variationArray[$variation]->name,
                        'attribute_name' => $variationArray[$variation]->itemAttribute->name
                    ];
                    $price += $variationArray[$variation]->price;
                }
            }
            return (object)[
                'price' => $price,
                'name'  => $name
            ];
        }
    }
}