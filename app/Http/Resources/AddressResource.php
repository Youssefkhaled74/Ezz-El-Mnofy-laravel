<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        // Default values
        $inZone = false;
        $zoneBranch = null;

        // Check if lat/lon are set
        if ($this->latitude && $this->longitude) {
            $userLat = floatval($this->latitude);
            $userLon = floatval($this->longitude);

            // Get all active areas with their branches
            $areas = \App\Models\Area::where('is_active', 1)->with('branch')->get();

            foreach ($areas as $area) {
                $coordinates = json_decode($area->points, true);
                if (!is_array($coordinates) || empty($coordinates)) {
                    continue;
                }
                $transformedCoordinates = array_map(function ($point) {
                    return [floatval($point['lat']), floatval($point['lng'])];
                }, $coordinates);

                $validCoordinates = true;
                foreach ($transformedCoordinates as $point) {
                    if (!is_array($point) || count($point) < 2 || !isset($point[0], $point[1])) {
                        $validCoordinates = false;
                        break;
                    }
                }
                if (!$validCoordinates) {
                    continue;
                }

                // Use the same point-in-polygon logic as your controller
                $vertices = count($transformedCoordinates);
                $inside = false;
                for ($i = 0, $j = $vertices - 1; $i < $vertices; $j = $i++) {
                    $xi = $transformedCoordinates[$i][1];
                    $yi = $transformedCoordinates[$i][0];
                    $xj = $transformedCoordinates[$j][1];
                    $yj = $transformedCoordinates[$j][0];

                    $intersect = (($yi > $userLat) != ($yj > $userLat)) &&
                        ($userLon < ($xj - $xi) * ($userLat - $yi) / ($yj - $yi) + $xi);
                    if ($intersect) {
                        $inside = !$inside;
                    }
                }
                if ($inside) {
                    $inZone = true;
                    $zoneBranch = [
                        'branch_id' => $area->branch->id ?? null,
                        'branch_name' => $area->branch->name ?? null,
                    ];
                    break;
                }
            }
        }

        return [
            "id"         => $this->id,
            "user_id"    => $this->user_id,
            "label"      => $this->label,
            "address"    => $this->address,
            "apartment"  => $this->apartment,
            "latitude"   => $this->latitude,
            "longitude"  => $this->longitude,
            "in_zone"    => $inZone,
            "zone_branch" => $zoneBranch,
        ];
    }
}
