// MENÚ HAMBURGUESA
// Este código controla el menú tipo hamburguesa (el de las tres rayitas)
console.log('app.js cargado');

var menu = document.querySelector('#menu_hamburguesa');
console.log('Menu encontrado:', menu);

if (menu) {
  // Cuando haces click en el menú hamburguesa, abre o cierra el menú
  menu.addEventListener('click', function(e) {
    e.preventDefault();   
    e.stopPropagation();       
    document.body.classList.toggle('menu-open');
    console.log('Menu abierto:', document.body.classList.contains('menu-open'));
  });
  
  console.log('Event listener agregado al menu');
}

// Cerrar menú al hacer clic en un link
// Si haces click en cualquier enlace del menú, se cierra el menú
var menuLinks = document.querySelectorAll('.menu_desplegable a');
console.log('Links encontrados:', menuLinks.length);

menuLinks.forEach(function(link) {
  link.addEventListener('click', function() {
    document.body.classList.remove('menu-open');
    console.log('Menu cerrado por click en link');
  });
});

//Solución con jQUery (comentada, por si la quieres usar)
/*$(document).ready(function(){
  $('.hamburger').click(function() {
    $('.hamburger').toggleClass('is-active');
    $('.menuresponsive').toggleClass('is-active');
    return false;
  });
});*/

// cookies helpers
// Estas funciones sirven para guardar, leer y borrar cookies (pequeños datos que se guardan en tu navegador)
function setCookie(name, value, days) {
  var expires = "";
  if (days) {
    var date = new Date();
    date.setTime(date.getTime() + (days*24*60*60*1000));
    expires = "; expires=" + date.toUTCString();
  }
  document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

function getCookie(name) {
  var nameEQ = name + "=";
  var ca = document.cookie.split(';');
  for(var i=0;i < ca.length;i++) {
    var c = ca[i];
    while (c.charAt(0)==' ') c = c.substring(1,c.length);
    if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
  }
  return null;
}

function eraseCookie(name) { 
  document.cookie = name+'=; Max-Age=-99999999; path=/';
}

// establecer cookie de animal (gato|perro)
// Esta función guarda la preferencia de animal (gato o perro) en una cookie
function setAnimalCookie(animal) {
  if (!animal) return;
  setCookie('animal', animal, 30);
  // feedback simple
  alert('Preferencia guardada: ' + animal);
}

// aplicar gif junto al mensaje si el usuario es admin
// Si eres admin, muestra un gif de animal según tu preferencia
function applyAnimalGif() {
  var img = document.getElementById('animalGif');
  if (!img) return;
  var isAdmin = document.body && (document.body.dataset.userAdmin === '1' || document.body.getAttribute('data-user-admin') === '1');
  if (!isAdmin) { img.style.display = 'none'; return; }
  var animal = getCookie('animal');
  if (!animal) {
    // mostrar gif por defecto si existe
    img.src = 'images/admin.gif';
    img.style.display = 'inline-block';
    return;
  }
  if (animal === 'perro') img.src = 'images/perro.gif';
  else if (animal === 'gato') img.src = 'images/gato.gif';
  else img.style.display = 'none';
  img.style.display = 'inline-block';
}

// Cuando la página termina de cargar, aplicamos el gif de animal si eres admin
document.addEventListener('DOMContentLoaded', function(){
    applyAnimalGif();
});

// Cuando la página termina de cargar, insertamos el widget de valoración (estrellas)
document.addEventListener('DOMContentLoaded', function() {
  // Forzar widget de valoración para depuración
  let anchor = document.getElementById('valoracion-anchor');
  if (!anchor) {
    anchor = document.createElement('div');
    anchor.id = 'valoracion-anchor';
    document.body.appendChild(anchor);
    console.log('Ancla de valoración creada dinámicamente.');
  }
  if (anchor) {
    console.log('Insertando widget de valoración...');
    crearValoracionInteractiva();
  } else {
    console.log('No se encontró el ancla de valoración.');
  }
});

// ==========================
// VALORACIÓN INTERACTIVA CON MEDIA
// ==========================
// Esta función crea el widget de valoración con estrellas y muestra la media
function crearValoracionInteractiva() {
  // Evita duplicar el widget
  if (document.getElementById('widget-valoracion')) return;
  const cont = document.createElement('div');
  cont.id = 'widget-valoracion';
  cont.className = 'widget-valoracion';

  // Título
  const titulo = document.createElement('div');
  titulo.innerHTML = '<strong>Valora la web:</strong>';
  cont.appendChild(titulo);

  // Estrellas (puedes hacer click para valorar)
  const estrellasDiv = document.createElement('div');
  estrellasDiv.className = 'estrellas-valoracion';
  for (let i = 1; i <= 5; i++) {
    const star = document.createElement('span');
    star.textContent = '☆';
    star.className = 'estrella-valoracion';
    star.dataset.valor = i;
    star.addEventListener('mouseenter', function() {
      pintarEstrellas(estrellasDiv, i);
    });
    star.addEventListener('mouseleave', function() {
      const actual = Number(localStorage.getItem('valoracion_usuario') || 0);
      pintarEstrellas(estrellasDiv, actual);
    });
    star.addEventListener('click', async function() {
      localStorage.setItem('valoracion_usuario', i);
      await guardarValoracion(i);
      pintarEstrellas(estrellasDiv, i);
      mostrarMediaValoracion();
    });
    estrellasDiv.appendChild(star);
  }
  cont.appendChild(estrellasDiv);

  // Media de valoración
  const mediaDiv = document.createElement('div');
  mediaDiv.id = 'media-valoracion';
  mediaDiv.className = 'media-valoracion';
  cont.appendChild(mediaDiv);

  // Insertar en la página justo después del ancla
  let anchor = document.getElementById('valoracion-anchor');
  if (anchor && anchor.parentNode) {
    anchor.parentNode.insertBefore(cont, anchor.nextSibling);
    console.log('Widget de valoración insertado tras el ancla.');
  } else {
    document.body.appendChild(cont);
    console.log('Widget de valoración insertado al final del body.');
  }

  // Pintar estado inicial
  const actual = Number(localStorage.getItem('valoracion_usuario') || 0);
  pintarEstrellas(estrellasDiv, actual);
  mostrarMediaValoracion();
}

function pintarEstrellas(div, valor) {
  Array.from(div.children).forEach((star, idx) => {
    star.textContent = idx < valor ? '★' : '☆';
    star.classList.toggle('estrella-activa', idx < valor);
  });
}

async function guardarValoracion(valor) {
  // Simula almacenamiento global usando localStorage (en producción sería en servidor)
  let arr = JSON.parse(localStorage.getItem('valoraciones_web') || '[]');
  arr.push(valor);
  localStorage.setItem('valoraciones_web', JSON.stringify(arr));
  // Simula espera asíncrona
  await new Promise(res => setTimeout(res, 200));
}

function mostrarMediaValoracion() {
  let arr = JSON.parse(localStorage.getItem('valoraciones_web') || '[]');
  let media = arr.length ? (arr.reduce((a,b) => a+b,0) / arr.length).toFixed(2) : 'N/A';
  let mediaDiv = document.getElementById('media-valoracion');
  if (mediaDiv) {
    mediaDiv.innerHTML = `<span class="media-label">Media de valoración:</span> <span class="media-num">${media}</span>`;
  }
}

// ==========================
// CARRUSEL UNIVERSAL
// ==========================
// Este código hace que el carrusel de productos funcione (puedes ver productos deslizando)
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".carrusel-contenedor").forEach(contenedor => {
    const carousel = contenedor.querySelector(".carousel");
    const track = contenedor.querySelector(".carousel-track");
    const items = track ? track.querySelectorAll(".producto-item") : [];
    const prevBtn = contenedor.querySelector(".carousel-btn.prev");
    const nextBtn = contenedor.querySelector(".carousel-btn.next");
    if (!carousel || !track || !items.length) return;
    let index = 0;
    function getVisible() {
      return 1; // Solo 1 producto visible siempre
    }
    function updateCarousel() {
      const visible = getVisible();
      const itemW = items[0].offsetWidth;
      const maxIndex = Math.max(0, items.length - visible);
      if (index > maxIndex) index = maxIndex;
      if (index < 0) index = 0;
      track.style.transform = `translateX(-${index * itemW}px)`;
      prevBtn.disabled = index === 0;
      nextBtn.disabled = index >= maxIndex;
    }
    window.addEventListener('resize', updateCarousel);
    updateCarousel();
    prevBtn.addEventListener('click', () => {
      index--;
      updateCarousel();
    });
    nextBtn.addEventListener('click', () => {
      index++;
      updateCarousel();
    });
  });
});
// ==========================

// Integración de geolocalización con el mapa
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar el mapa en una ubicación por defecto
  var map = L.map('map').setView([51.505, -0.09], 13);
  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
  }).addTo(map);

  var marker, circle;

  function showLocationOnMap(lat, lon) {
    map.setView([lat, lon], 15);
    if (marker) map.removeLayer(marker);
    if (circle) map.removeLayer(circle);
    marker = L.marker([lat, lon]).addTo(map);
    circle = L.circle([lat, lon], {
      color: 'blue',
      fillColor: '#3fa',
      fillOpacity: 0.3,
      radius: 200
    }).addTo(map);
    marker.bindPopup("<b>¡Estás aquí!</b><br>Latitud: " + lat.toFixed(6) + "<br>Longitud: " + lon.toFixed(6)).openPopup();
  }

  function showErrorOnMap(msg) {
    map.setView([51.505, -0.09], 13);
    L.popup()
      .setLatLng([51.505, -0.09])
      .setContent("Error de geolocalización: " + msg)
      .openOn(map);
  }

  if ("geolocation" in navigator) {
    navigator.geolocation.getCurrentPosition(
      function (position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        showLocationOnMap(lat, lon);
        console.log("Latitud:", lat);
        console.log("Longitud:", lon);
      },
      function (error) {
        showErrorOnMap("No se pudo obtener la ubicación: " + error.message);
        console.warn("Error de geolocalización:", error.message);
      }
    );
  } else {
    showErrorOnMap("¡Lo sentimos mucho! Tu navegador no soporta geolocalización.");
  }
});


