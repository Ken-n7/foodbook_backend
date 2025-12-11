<?php
// Load JSON file
$jsonData = file_get_contents("tacloban_restaurants.json");
$restaurants = json_decode($jsonData, true)["restaurants"];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tacloban Restaurants Map</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .layout {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* LEFT SIDE - Scrollable List */
        .left-side {
            width: 35%;
            background: #fafafa;
            border-right: 2px solid #ddd;
            overflow-y: auto;
            padding: 20px;
        }

        .restaurant {
            padding: 12px 10px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }

        .restaurant:hover {
            background: #eee;
        }

        /* RIGHT SIDE - Fixed Map + Info */
        .right-side {
            width: 65%;
            display: flex;
            flex-direction: column;
        }

        #info-box {
            background: #f0f9ff;
            padding: 15px;
            border-bottom: 2px solid #ddd;
            font-size: 16px;
        }

        #map {
            flex: 1;
        }
    </style>
</head>

<body>

    <div class="layout">
        <!-- LEFT SIDE -->
        <div class="left-side">
            <h2>Tacloban Restaurants</h2>

            <?php foreach ($restaurants as $r): ?>

                <?php if (!empty($r['name']) && strtolower($r['name']) !== "unnamed"): ?>
                    <div class="restaurant"
                        onclick="selectPlace('<?php echo addslashes($r['name']); ?>', <?php echo $r['latitude']; ?>, <?php echo $r['longitude']; ?>)">
                        <?php echo $r['name']; ?>
                    </div>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>

        <!-- RIGHT SIDE -->
        <div class="right-side">
            <div id="info-box">
                <strong>Select a restaurant from the left</strong>
            </div>
            <div id="map"></div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Initialize map
        let map = L.map('map').setView([11.2445, 125.0026], 13);

        // Load map tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Marker placeholder
        let marker;

        function selectPlace(name, lat, lon) {
            // Update info box
            document.getElementById("info-box").innerHTML = `
        <h3>${name}</h3>
        <p><strong>Lat:</strong> ${lat} | <strong>Lon:</strong> ${lon}</p>
        <p><a href="https://www.google.com/maps?q=${lat},${lon}" target="_blank">Open in Google Maps</a></p>
    `;

            // Move / create marker
            if (marker) {
                marker.setLatLng([lat, lon]);
            } else {
                marker = L.marker([lat, lon]).addTo(map);
            }

            // Center map
            map.setView([lat, lon], 16);
        }
    </script>

</body>

</html>