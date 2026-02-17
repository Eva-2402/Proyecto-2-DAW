// ==========================
//  CARRITO DE COMPRAS
// ==========================

// --- CARGAR CARRITO DESDE LOCALSTORAGE ---
// Aquí recuperamos el carrito guardado en el navegador, o lo dejamos vacío si no hay nada
let carrito = JSON.parse(localStorage.getItem("carrito")) || [];

// --- GUARDAR CARRITO ---
// Esta función guarda el carrito en el navegador para que no se pierda si recargas la página
function guardarCarrito() {
    localStorage.setItem("carrito", JSON.stringify(carrito));
}

// --- MOSTRAR CARRITO EN PANTALLA ---
// Esta función muestra todos los productos del carrito en la página
function mostrarCarrito() {
    const lista = document.getElementById("lista-carrito");
    const totalTexto = document.getElementById("total");

    if (!lista || !totalTexto) return;

    lista.innerHTML = "";
    let total = 0;

    carrito.forEach((producto, index) => {

        // Si el producto no tiene cantidad, le ponemos 1 por defecto
        if (!producto.cantidad) producto.cantidad = 1;

        const subtotal = Number(producto.precio) * producto.cantidad;
        total += subtotal;

        const li = document.createElement("li");
        li.className = "item-carrito";

        // Aquí armamos el HTML de cada producto en el carrito
        li.innerHTML = `
            <div class="producto-info">
                <span class="producto-nombre">${producto.nombre}</span>
                <span class="producto-precio">${Number(producto.precio).toFixed(2)}€</span>
            </div>

            <div class="controles-cantidad">
                <button class="btn-menos" data-index="${index}">−</button>
                <span class="cantidad">${producto.cantidad}</span>
                <button class="btn-mas" data-index="${index}">+</button>
            </div>

            <div class="subtotal">
                <span>${subtotal.toFixed(2)}€</span>
            </div>

            <button class="borrar" data-index="${index}">✕</button>
        `;

        lista.appendChild(li);
    });

    totalTexto.innerHTML = `<strong>Total: ${total.toFixed(2)}€</strong>`;

    // --- BOTÓN + ---
    // Cuando le das al botón +, aumenta la cantidad de ese producto
    document.querySelectorAll(".btn-mas").forEach(btn => {
        btn.addEventListener("click", () => {
            const i = Number(btn.dataset.index);
            carrito[i].cantidad++;
            guardarCarrito();
            mostrarCarrito();
        });
    });

    // --- BOTÓN - ---
    // Cuando le das al botón -, baja la cantidad y si llega a 0, lo quita del carrito
    document.querySelectorAll(".btn-menos").forEach(btn => {
        btn.addEventListener("click", () => {
            const i = Number(btn.dataset.index);
            carrito[i].cantidad--;

            if (carrito[i].cantidad <= 0) {
                carrito.splice(i, 1);
            }

            guardarCarrito();
            mostrarCarrito();
        });
    });

    // --- ELIMINAR PRODUCTO ---
    // Si le das a la X, borra ese producto del carrito
    document.querySelectorAll(".borrar").forEach(btn => {
        btn.addEventListener("click", () => {
            const i = Number(btn.dataset.index);
            carrito.splice(i, 1);
            guardarCarrito();
            mostrarCarrito();
        });
    });
}

// ==========================
//  VACIAR CARRITO
// ==========================
// Esta función activa el botón para vaciar todo el carrito
function activarVaciar() {
    const btnVaciar = document.getElementById("vaciar-carrito");

    if (!btnVaciar) return;

    btnVaciar.addEventListener("click", () => {
        if (!confirm("¿Seguro que quieres vaciar el carrito?")) return;

        carrito = [];
        guardarCarrito();
        mostrarCarrito();
    });
}

// ==========================
//  COMPRAR PRODUCTOS
// ==========================
// Esta función activa el botón de comprar y manda el carrito al servidor
function activarCompra() {
    const btnComprar = document.getElementById("btn-comprar");
    if (!btnComprar) return;

    btnComprar.addEventListener("click", async (e) => {
        e.preventDefault();

        if (carrito.length === 0) {
            alert("El carrito está vacío.");
            return;
        }

        if (!confirm("¿Confirmas la compra?")) return;

        try {
            // Enviamos el carrito al servidor usando fetch y esperamos la respuesta
            const response = await fetch("procesar_compra.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ carrito })
            });

            const data = await response.json();

            if (!data.success) {
                alert("Error: " + data.mensaje);
                return;
            }

            alert(`✅ ${data.mensaje}\nID de venta: ${data.id_venta}`);

            carrito = [];
            guardarCarrito();
            mostrarCarrito();

        } catch (error) {
            alert("Error de conexión con el servidor");
            console.error(error);
        }
    });
}


// ==========================
//  AL CARGAR LA PÁGINA
// ==========================
// Cuando la página termina de cargar, mostramos el carrito y activamos los botones
document.addEventListener("DOMContentLoaded", () => {
    mostrarCarrito();
    activarVaciar();
    activarCompra();
});
