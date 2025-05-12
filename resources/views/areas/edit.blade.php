<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Area for {{ $branch->name }}</title>
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
        
        .form-label {
            color: var(--dark-gray);
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border-radius: 0.375rem;
            border-color: #ced4da;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        #map {
            height: 600px;
            width: 100%;
            border-radius: 0.375rem;
            margin-top: 1rem;
        }
        
        #searchMap {
            position: absolute;
            top: 10px;
            left: 50px;
            width: 300px;
            z-index: 5;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
                <h2 class="mb-0"><i class="bi bi-geo-alt me-2"></i>Edit Area: {{ $area->name }}</h2>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form action="{{ route('areas.update', $area->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div style="position: relative;">
                        <input type="text" id="searchMap" class="form-control" placeholder="Search for a location...">
                        <div id="map"></div>
                    </div>
                    <input type="hidden" name="points" id="points" value="{{ $area->points }}">
                    <div class="mt-3">
                        <button type="submit" class="btn btn-red">Save Changes</button>
                        <a href="{{ route('areas.index', ['branch_id' => $branch->id]) }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('MIX_GOOGLE_MAP_KEY') }}&libraries=drawing,places&callback=initMap" async defer></script>
    <script>
        let map;
        let polygon;
        let drawingManager;

        function initMap() {
            console.log('initMap called'); // Debug: Confirm function is called

            // Parse existing points
            const initialPoints = @json($area->points ? json_decode($area->points, true) : []);
            console.log('Initial Points:', initialPoints); // Debug: Log initial points

            // Validate points
            if (!initialPoints || initialPoints.length === 0) {
                console.warn('No valid points provided. Using default center.');
                initialPoints.push({ lat: 30.0626, lng: 31.2497 }); // Default to Cairo
            }

            const center = { lat: initialPoints[0].lat, lng: initialPoints[0].lng };

            // Initialize the map
            map = new google.maps.Map(document.getElementById("map"), {
                center: center,
                zoom: 12,
            });
            console.log('Map initialized'); // Debug: Confirm map initialization

            // Add search bar
            const input = document.getElementById("searchMap");
            const searchBox = new google.maps.places.SearchBox(input);
            map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

            // Bias the SearchBox results towards current map's viewport
            map.addListener("bounds_changed", () => {
                searchBox.setBounds(map.getBounds());
            });

            // Listen for the event fired when the user selects a prediction
            searchBox.addListener("places_changed", () => {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;

                const bounds = new google.maps.LatLngBounds();
                places.forEach((place) => {
                    if (!place.geometry || !place.geometry.location) return;

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }
                });
                map.fitBounds(bounds);
            });

            // Create the initial polygon if points exist
            if (initialPoints.length > 0) {
                polygon = new google.maps.Polygon({
                    paths: initialPoints.map(p => ({ lat: p.lat, lng: p.lng })),
                    editable: true,
                    draggable: true,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                });
                polygon.setMap(map);
                console.log('Initial polygon set'); // Debug: Confirm polygon creation

                // Update points on edit
                polygon.getPaths().forEach(path => {
                    google.maps.event.addListener(path, 'set_at', updatePoints);
                    google.maps.event.addListener(path, 'insert_at', updatePoints);
                });

                // Fit map to polygon bounds
                const bounds = new google.maps.LatLngBounds();
                initialPoints.forEach(point => bounds.extend({ lat: point.lat, lng: point.lng }));
                map.fitBounds(bounds);
            }

            // Add drawing manager for creating a new polygon if needed
            drawingManager = new google.maps.drawing.DrawingManager({
                drawingMode: initialPoints.length === 0 ? google.maps.drawing.OverlayType.POLYGON : null,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON],
                },
                polygonOptions: {
                    editable: true,
                    draggable: true,
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                },
            });
            drawingManager.setMap(map);

            // Handle new polygon creation
            google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
                if (event.type === google.maps.drawing.OverlayType.POLYGON) {
                    // Remove existing polygon if any
                    if (polygon) {
                        polygon.setMap(null);
                    }
                    polygon = event.overlay;
                    polygon.getPaths().forEach(path => {
                        google.maps.event.addListener(path, 'set_at', updatePoints);
                        google.maps.event.addListener(path, 'insert_at', updatePoints);
                    });
                    updatePoints();
                    drawingManager.setDrawingMode(null); // Disable drawing mode after completion
                }
            });

            // Update hidden input with current polygon coordinates
            function updatePoints() {
                if (polygon) {
                    const coordinates = polygon.getPath().getArray().map(coord => ({
                        lat: coord.lat(),
                        lng: coord.lng()
                    }));
                    document.getElementById('points').value = JSON.stringify(coordinates);
                    console.log('Updated Points:', coordinates); // Debug: Log updated points
                }
            }
        }

        // Error handling for API loading
        window.onerror = function(message, source, lineno, colno, error) {
            console.error('Error:', message, 'Source:', source, 'Line:', lineno, 'Column:', colno, 'Error Object:', error);
        };
    </script>
</body>
</html>