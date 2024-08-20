<?php include 'index.html'; ?>



<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        html, body {font-family: "Raleway", sans-serif;}
        .sidebar {width: 280px; position: fixed; top: 56px; height: 100vh; background: #f8f9fa;}
        .content {margin-left: 300px; padding: 20px;}
        #map {height: 500px;}
    </style>
</head>

<div class="content">
  <h2>Dashboard</h2>

  <div class="row">
    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-calendar-day"></i> Fecha del Día
        </div>
        <div class="card-body text-center" id="date"></div>
      </div>
    </div>

    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-dollar-sign"></i> Precio del Dólar
        </div>
        <div class="card-body text-center" id="dollar-price"></div>
      </div>
    </div>

    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-gas-pump"></i> Precio del Diésel
        </div>
        <div class="card-body text-center" id="diesel-price"></div>
      </div>
    </div>

    <div class="col-md-3 mb-4">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-bell"></i> Notificaciones
        </div>
        <div class="card-body text-center" id="notifications"></div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 mb-4">
      <div class="card">
        <div class="card-header">
          <i class="fas fa-file-alt"></i> Log
        </div>
        <div class="card-body" id="log"></div>
      </div>
    </div>

    <div class="col-md-12 mb-4"> <div class="card">
        <div class="card-header">
          <i class="fas fa-map-marker-alt"></i> Viajes
        </div>
        <div class="card-body">
          <div id="map"></div>
        </div>
      </div>
    </div>
  </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script>
    // Mostrar la fecha actual
    document.getElementById('date').innerText = new Date().toLocaleDateString();

    // Función para cargar notificaciones
    function loadNotifications() {
        // Aquí puedes hacer una solicitud AJAX para cargar las notificaciones desde la base de datos
        document.getElementById('notifications').innerHTML = '<p>No hay nuevas notificaciones</p>';
    }

    // Función para cargar logs
    function loadLogs() {
        // Aquí puedes hacer una solicitud AJAX para cargar los logs desde la base de datos
        document.getElementById('log').innerHTML = '<p>No hay registros recientes</p>';
    }

    // Función para cargar el precio del dólar
    function loadDollarPrice() {
        axios.get('https://api.exchangerate-api.com/v4/latest/USD')
            .then(response => {
                const rate = response.data.rates.MXN;
                document.getElementById('dollar-price').innerText = `1 USD = ${rate} MXN`;
            })
            .catch(error => {
                console.error('Error al cargar el precio del dólar:', error);
                document.getElementById('dollar-price').innerText = 'Error al cargar';
            });
    }

    // Función para cargar el precio del diésel
    function loadDieselPrice() {
        // Puedes reemplazar este endpoint con una fuente real de datos sobre el precio del diésel
        axios.get('https://api.example.com/diesel-price')
            .then(response => {
                const price = response.data.price;
                document.getElementById('diesel-price').innerText = `${price} MXN por litro`;
            })
            .catch(error => {
                console.error('Error al cargar el precio del diésel:', error);
                document.getElementById('diesel-price').innerText = 'Error al cargar';
            });
    }

    // Función para inicializar el mapa de OpenStreetMap
    function initMap() {
        var map = L.map('map').setView([23.6345, -102.5528], 5); // Centro de México

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Aquí puedes cargar datos desde la base de datos para agregar marcadores al mapa
        var markers = [
            { lat: 19.107879773024898, lng: -104.3219892622768, title: 'Manzanillo' },
    
        ];

        markers.forEach(marker => {
            L.marker([marker.lat, marker.lng]).addTo(map)
                .bindPopup(marker.title);
        });
    }

    // Cargar los datos al cargar la página
    window.onload = function() {
        loadNotifications();
        loadLogs();
        loadDollarPrice();
        loadDieselPrice();
        initMap();
    };
</script>
</body>
</html>
