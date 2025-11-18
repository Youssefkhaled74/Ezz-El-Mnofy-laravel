<?php

namespace App\Http\Resources;

use App\Enums\Status;
use App\Libraries\AppLibrary;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;

class NormalItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $branchId = $request->get('branch_id');
        $branch = $branchId ? $this->branches->firstWhere('id', $branchId) : null;
        $price = $branch && $branch->pivot->price !== null ? $branch->pivot->price : $this->price;
        $user = $request->user();
        $locale = $request->get('lang', App::getLocale());
        $name = $this->name[$locale] ?? $this->name['en'] ?? '';
        if ($locale === 'fr') {
            $name = $this->name['en'] ?? '';
        }

        // Decode JSON description if it’s a string, then apply localization
        $description = is_string($this->description) ? json_decode($this->description, true) : $this->description;
        $description = is_array($description)
            ? ($description[$locale] ?? $description['en'] ?? '')
            : ($description ?? '');
        if ($locale === 'fr') {
            $description = is_array($description) ? ($description['en'] ?? '') : ($description ?? '');
        }

        return [
            "id"             => $this->id,
            "name"           => $name,
            "slug"           => $this->slug,
            "flat_price"     => AppLibrary::flatAmountFormat($price),
            "convert_price"  => AppLibrary::convertAmountFormat($price),
            "currency_price" => AppLibrary::currencyAmountFormat($price),
            "price"          => $price,
            "item_type"      => $this->item_type,
            "status"         => $this->status,
            "description"    => $description,
            "caution"        => $this->caution === null ? '' : $this->caution,
            "thumb"          => $this->thumb,
            "cover"          => $this->cover,
            "preview"        => $this->preview,
            "is_favorite"    => $user ? $user->favorites->contains($this->id) : false,
            "variations"     => $this->variations->groupBy('item_attribute_id'),
            "itemAttributes" => ItemAttributeResource::collection($this->itemAttributeList($this->variations)),
            "extras"         => ItemExtraResource::collection($this->extras),
            "addons"         => ItemAddonResource::collection($this->addons),
            "offer"          => SimpleOfferResource::collection(
                $this->offer->filter(function ($offer) use ($price) {
                    if (Carbon::now()->between(
                        $offer->start_date,
                        $offer->end_date
                    ) && $offer->status === Status::ACTIVE) {
                        $offer->flat_price     = AppLibrary::flatAmountFormat($price - ($price / 100 * $offer->amount));
                        $offer->convert_price  = AppLibrary::convertAmountFormat(
                            $price - ($price / 100 * $offer->amount)
                        );
                        $offer->currency_price = AppLibrary::currencyAmountFormat(
                            $price - ($price / 100 * $offer->amount)
                        );
                        return $offer;
                    }
                })
            ),
            "branches" => $this->branches->map(function ($branch) {
                return [
                    "id"       => $branch->id,
                    "name"     => $branch->name,
                    "location" => $branch->location ?? '',
                    "price"    => $branch->pivot->price ?? null,
                ];
            }),
            "brand_id"      => $this->brand_id,
            "brand_name"    => optional($this->brand)->name,
            "brand_logo"    => optional($this->brand)->logo === null ? '' : env('APP_URL') . '/public/' . optional($this->brand)->logo,
        ];
    }

    private function itemAttributeList($variations): \Illuminate\Support\Collection
    {
        $array = [];
        foreach ($variations as $b) {
            if (!isset($array[$b->itemAttribute->id])) {
                $array[$b->itemAttribute->id] = (object)[
                    'id'     => $b->itemAttribute->id,
                    'name'   => $b->itemAttribute->name,
                    'status' => $b->itemAttribute->status
                ];
            }
        }
        return collect($array);
    }
}