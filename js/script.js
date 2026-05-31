// Confirmar antes de usar enlaces o botones marcados como acciones peligrosas.
document.addEventListener("DOMContentLoaded", function () {
    const botonesPeligro = document.querySelectorAll(".boton-peligro");

    botonesPeligro.forEach(function (boton) {
        boton.addEventListener("click", function (evento) {
            const confirmar = confirm("Estas seguro de realizar esta accion?");

            if (!confirmar) {
                evento.preventDefault();
            }
        });
    });

    // Ocultar mensajes de exito despues de unos segundos.
    const mensajesExito = document.querySelectorAll(".mensaje-exito");

    mensajesExito.forEach(function (mensaje) {
        setTimeout(function () {
            mensaje.style.display = "none";
        }, 4000);
    });
});
