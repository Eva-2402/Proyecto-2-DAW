<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
     <link rel="stylesheet" href="style.css">
    <title>Contacto</title>
</head>
<body>
</body>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <h2>Contáctanos</h2>
    <div id="map" style="height: 320px; width: 100%; max-width: 600px; margin: 0 auto 32px auto; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);"></div>
    <div id="geo-box">
        <span class="geo-lat"></span> <span class="geo-lon"></span>
        <span class="geo-error"></span>
    </div>
    <form id="contacto-form" style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 24px;">
        <h3>Formulario de contacto</h3>
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" required style="width: 100%; margin-bottom: 12px;"><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required style="width: 100%; margin-bottom: 12px;"><br>
        <label for="mensaje">Mensaje:</label><br>
        <textarea id="mensaje" name="mensaje" rows="5" required style="width: 100%; margin-bottom: 12px;"></textarea><br>
        <button type="submit" style="background: #27AE60; color: #fff; border: none; border-radius: 6px; padding: 10px 24px; font-size: 1.1em; cursor: pointer;">Enviar</button>
        <div id="form-msg" style="margin-top: 12px;"></div>
    </form>
    <script>
    // Mapa con Leaflet (ubicación fija, ejemplo: CDMX)
    var map = L.map('map').setView([19.4326, -99.1332], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(map);
    L.marker([19.4326, -99.1332]).addTo(map)
        .bindPopup('¡Aquí estamos!')
        .openPopup();

    // Geolocalización del usuario
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            var lat = pos.coords.latitude.toFixed(5);
            var lon = pos.coords.longitude.toFixed(5);
            document.getElementById('geo-box').style.display = 'block';
            document.querySelector('#geo-box .geo-lat').textContent = 'Latitud: ' + lat;
            document.querySelector('#geo-box .geo-lon').textContent = ' | Longitud: ' + lon;
            map.setView([lat, lon], 14);
            L.marker([lat, lon]).addTo(map).bindPopup('Tu ubicación').openPopup();
        }, function(err) {
            document.getElementById('geo-box').style.display = 'block';
            document.querySelector('#geo-box .geo-error').textContent = 'No se pudo obtener tu ubicación.';
        });
    }

    // Envío del formulario (solo frontend, sin backend)
    document.getElementById('contacto-form').addEventListener('submit', function(e) {
        e.preventDefault();
        document.getElementById('form-msg').textContent = '¡Gracias por contactarnos! Pronto te responderemos.';
        document.getElementById('form-msg').style.color = '#27AE60';
        this.reset();
    });
    </script>
    <a href="index.php" style="display: block; text-align: center; margin-top: 24px; color: #3498DB;">Volver al inicio</a>
</body>
</html>