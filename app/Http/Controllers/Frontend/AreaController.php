<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function nearest(Request $request)
    {
        $validated = $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude  = (float) $validated['latitude'];
        $longitude = (float) $validated['longitude'];

        $areas = Area::with('branch')->where('is_active', '1')->get();

        $bestArea              = null;
        $bestAreaDistanceKm    = null;

        foreach ($areas as $area) {
            $points = $this->normalizePoints($area->points);

            if (!empty($points)) {
                // Prefer areas that contain the point
                if ($this->pointInPolygon($latitude, $longitude, $points)) {
                    $bestArea           = $area;
                    $bestAreaDistanceKm = 0.0;
                    break;
                }

                // Otherwise, use centroid distance as a fallback
                $centroid = $this->polygonCentroid($points);
                if ($centroid) {
                    $distanceKm = $this->haversineKm($latitude, $longitude, $centroid['lat'], $centroid['lng']);
                    if ($bestArea === null || $distanceKm < $bestAreaDistanceKm) {
                        $bestArea           = $area;
                        $bestAreaDistanceKm = $distanceKm;
                    }
                }
            }
        }

        if (!$bestArea) {
            return response()->json(['message' => 'No areas found'], 404);
        }

        return response()->json([
            'area_id'     => $bestArea->id,
            'area_name'   => $bestArea->name,
            'branch_id'   => $bestArea->branch_id,
            'distance_km' => $bestAreaDistanceKm,
        ]);
    }

    private function normalizePoints($points): array
    {
        // points may be stored as json string or array; ensure array of ['lat' => , 'lng' => ]
        if (is_string($points)) {
            $decoded = json_decode($points, true);
        } else {
            $decoded = $points;
        }
        if (!is_array($decoded)) {
            return [];
        }
        $normalized = [];
        foreach ($decoded as $pt) {
            if (is_array($pt) && isset($pt['lat']) && isset($pt['lng'])) {
                $normalized[] = ['lat' => (float) $pt['lat'], 'lng' => (float) $pt['lng']];
            } elseif (is_array($pt) && isset($pt['latitude']) && isset($pt['longitude'])) {
                $normalized[] = ['lat' => (float) $pt['latitude'], 'lng' => (float) $pt['longitude']];
            } elseif (is_array($pt) && array_key_exists(0, $pt) && array_key_exists(1, $pt)) {
                $normalized[] = ['lat' => (float) $pt[0], 'lng' => (float) $pt[1]];
            }
        }
        return $normalized;
    }

    private function pointInPolygon(float $lat, float $lng, array $polygon): bool
    {
        // Ray casting algorithm
        $inside = false;
        $numPoints = count($polygon);
        for ($i = 0, $j = $numPoints - 1; $i < $numPoints; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $lng) != ($yj > $lng)) &&
                ($lat < ($xj - $xi) * ($lng - $yi) / (($yj - $yi) ?: 1e-9) + $xi);
            if ($intersect) $inside = !$inside;
        }
        return $inside;
    }

    private function polygonCentroid(array $polygon): ?array
    {
        // Centroid of polygon (lat= x, lng = y)
        $area = 0.0;
        $cx = 0.0;
        $cy = 0.0;
        $count = count($polygon);
        if ($count < 3) {
            // Fallback simple average for line/point
            $sumLat = 0.0; $sumLng = 0.0;
            foreach ($polygon as $p) { $sumLat += $p['lat']; $sumLng += $p['lng']; }
            $n = max(1, $count);
            return ['lat' => $sumLat / $n, 'lng' => $sumLng / $n];
        }
        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];
            $f = ($xi * $yj - $xj * $yi);
            $area += $f;
            $cx += ($xi + $xj) * $f;
            $cy += ($yi + $yj) * $f;
        }
        $area *= 0.5;
        if (abs($area) < 1e-12) {
            return null;
        }
        $cx /= (6.0 * $area);
        $cy /= (6.0 * $area);
        return ['lat' => $cx, 'lng' => $cy];
    }

    private function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadiusKm = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadiusKm * $c;
    }
}


