<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Zones for {{ $branch->name }}</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-red: #dc3545;
            --dark-red: #c82333;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #333;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            background-color: white !important;
        }
        
        .bg-red {
            background-color: var(--primary-red);
        }
        
        .text-white {
            color: #ffffff !important;
        }
        
        .btn-red {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            color: #ffffff;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-red:hover, .btn-red:focus {
            background-color: var(--dark-red);
            border-color: var(--dark-red);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .card {
            border-radius: 0.5rem;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .card-header {
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        #map {
            height: 600px;
            width: 100%;
            border-radius: 0.375rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    @include('layouts.partials.navbar')

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-red text-white">
                <h2 class="mb-0"><i class="bi bi-map me-2"></i>View Zones for {{ $branch->name }}</h2>
            </div>
            <div class="card-body">
                <div id="map"></div>
                <div class="mt-3">
                    <a href="{{ route('areas.select-branch') }}" class="btn btn-outline-secondary">Back to Branch Selection</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyByGILjqDwyW9fMzjnXSCcPB11K8qboJEI"></script>
    <script>
        function initMap() {
            const map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 30.0626, lng: 31.2497 }, // Default to Cairo coordinates
                zoom: 12,
            });

            // Array of areas with their points
            const areas = @json($areas);

            // Colors for different areas
            const colors = ['#FF0000', '#00FF00', '#0000FF', '#FFA500', '#800080', '#00FFFF', '#FF00FF'];

            // Render each area as a polygon
            areas.forEach((area, index) => {
                const coordinates = JSON.parse(area.points);
                const polygon = new google.maps.Polygon({
                    paths: coordinates,
                    strokeColor: colors[index % colors.length],
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: colors[index % colors.length],
                    fillOpacity: 0.35,
                });
                polygon.setMap(map);

                // Calculate bounds to fit all polygons
                const bounds = new google.maps.LatLngBounds();
                coordinates.forEach(coord => {
                    bounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
                });

                // Fit the map to the bounds of this polygon
                map.fitBounds(bounds);

                // Add an info window to display area name on click
                const infoWindow = new google.maps.InfoWindow({
                    content: `<h6>${area.name}</h6>`,
                });

                polygon.addListener('click', (event) => {
                    infoWindow.setPosition(event.latLng);
                    infoWindow.open(map);
                });
            });

            // Adjust map to fit all polygons if there are multiple areas
            if (areas.length > 1) {
                const overallBounds = new google.maps.LatLngBounds();
                areas.forEach(area => {
                    const coordinates = JSON.parse(area.points);
                    coordinates.forEach(coord => {
                        overallBounds.extend(new google.maps.LatLng(coord.lat, coord.lng));
                    });
                });
                map.fitBounds(overallBounds);
            }
        }

        initMap();
    </script>
</body>
</html>