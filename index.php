<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- leaflet css link  -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <title>Web-GIS with GeoServer and Leaflet</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        #map {
            width: 100%;
            height: calc(100vh - 50px); /* Pengaturan Tinggi Navbar */
            margin-top: 50px; 
        }

        .navbar {
            height: 50px;
            background-color: #70add5;
            color: white;
            display: flex;
            align-items: center;
            padding: 0 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            cursor: pointer;
        }

        .legend-control {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: #70add5;
            color: white;
            padding: 5px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1000;
        }

        .legend {
            position: absolute;
            bottom: 50px; /* Adjusted to be above the toggle button */
            left: 10px;
            background-color: white;
            padding: 10px;
            border-radius: 5px;
            display: none;
            z-index: 1000;
        }

        .leaflet-control {
            margin-top: 60px; /* Ensure controls are below the navbar */
        }

        .leaflet-bar a {
            background-color: #70add5; /* Blue theme */
            color: white;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="#">KABUPATEN SLEMAN</a>
        <a href="https://geoportal.slemankab.go.id">Source</a>
        <a id="aboutLink">About</a>
    </div>
    <div id="map"></div>
    <div class="legend-control" id="legendControl">LEGEND</div>
    <div class="legend" id="legend">
        <!-- Legend content will be dynamically added here -->
    </div>

    <!-- Modal for About section -->
    <div id="aboutModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">×</span>
            <h2>About Me</h2>
            <p>Name: Caca Cintiya Bela</p>
            <p>NIM: 23/515886/SV/22634</p>
            <p>Contact: cacacintiyabela2005@mail.ugm.ac.id</p>
            <p>Github: <a href="https://github.com/cacinty" target="_blank"
            rel="noopener noreferrer">https://github.com/cacinty</a></p>
        </div>
    </div>

    <!-- leaflet js link  -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>

    <!-- jquery link  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- leaflet geoserver request link  -->
    <script src="lib/L.Geoserver.js"></script>

    <script>
        // Initialize the map
        var map = L.map("map").setView([-7.699894, 110.386749], 11);

        // Add OpenStreetMap layer
        var osm = L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 19,
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        });
        osm.addTo(map);

        // Add WMS layers
        var wmsLayer = L.Geoserver.wms("http://localhost:8080/geoserver/pgweb/wms", {
            layers: "pgweb:ADMINISTRASIDESA_AR_25K",
            transparent: true,
        });
        wmsLayer.addTo(map);

        var wmsLayer2 = L.Geoserver.wms("https://geoportal.slemankab.go.id/geoserver/wms", {
            layers: "geonode:jalan_ln",
            transparent: true,
        });
        wmsLayer2.addTo(map);

        var wmsLayer3 = L.Geoserver.wms("http://localhost:8080/geoserver/pgweb/wms", {
            layers: "pgweb:Jumlah_Penduduk",
            transparent: true,
        });
        wmsLayer3.addTo(map);

        var wmsLayer4 = L.Geoserver.wms("http://localhost:8080/geoserver/pgweb/wms", {
            layers: "pgweb:JALAN_LN_25K",
            transparent: true,
        });
        wmsLayer4.addTo(map);

        // Add legend content
        var legendContent = '<img src="http://localhost:8080/geoserver/pgweb/wms?REQUEST=GetLegendGraphic&VERSION=1.0.0&FORMAT=image/png&LAYER=pgweb:ADMINISTRASIDESA_AR_25K" alt="Legend">';
        document.getElementById('legend').innerHTML = legendContent;

        // Base maps and overlay maps
        var baseMaps = {
            "OpenStreetMap": osm
        };

        var overlayMaps = {
            "Batas Administrasi Desa": wmsLayer,
            "Jalan Arteri": wmsLayer2,
            "Jalan Lokal": wmsLayer4,
            "Penduduk": wmsLayer3
        };

        // Add layer control
        var layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);

        // Toggle legend visibility
        var legendVisible = false;
        document.getElementById('legendControl').onclick = function () {
            var legend = document.getElementById('legend');
            if (legendVisible) {
                legend.style.display = 'none';
            } else {
                legend.style.display = 'block';
            }
            legendVisible = !legendVisible;
        };

        // Modal functionality
        var aboutModal = document.getElementById("aboutModal");
        var aboutLink = document.getElementById("aboutLink");
        var closeModal = document.getElementById("closeModal");

        aboutLink.onclick = function () {
            aboutModal.style.display = "block";
        }

        closeModal.onclick = function () {
            aboutModal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == aboutModal) {
                aboutModal.style.display = "none";
            }
        }
    </script>
</body>

</html>
