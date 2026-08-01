$(document).ready(function () {
    cargarUsuarios();

    $("#formulario-ingreso").on("submit", enviarFormulario);
});

function cargarUsuarios(destacarPrimera = false) { //destacarPrimera lo uso para saber si quiero resaltar al primer usuario
    $.ajax({
        url: "api.php",
        method: "GET",
        success: (response) => {
            if (response.status === 'success') {
                mostrarUsuariosPantalla(response);
                if (destacarPrimera) {
                    $('#lista-usuarios .tarjeta-usuario').first().addClass('tarjeta-nueva');
                }
            }
        },
        error: () => {
            $('#lista-usuarios .text-danger').remove();
            $('#lista-usuarios').append('<p class="text-danger text-center">No se pudo cargar el listado.</p>');
        }
    });
}

function mostrarUsuariosPantalla(response) {
    let usuarios = response.data.usuarios || []; //Garantizo q sea array
    $('#lista-usuarios').empty();
    if (usuarios.length === 0) {
        $('#lista-usuarios').append('<p class="text-muted">No hay ningun usuario registrado.</p>');
    } else {
        usuarios.forEach(usuario => {
            let tarjetaHtml = crearTarjeta(usuario);
            $('#lista-usuarios').append(tarjetaHtml);
        });
    }
}
/*
//Procedimiento a seguir cuando envio un formulario:
1. Limpio errores
2. Obtengo los valores del formulario
3. Envio con AJAX
4.a. Caso success: limpio formulario, limpio errores y muestro usuarios
4.b. Caso error: me fijo si es error 422 (error en validaciones) o si es otro error distinto de 422.
*/
function enviarFormulario(event) {
    event.preventDefault();
    const datos = {
        nombre: $("#nombre").val(),
        email: $("#email").val(),
        telefono: $("#telefono").val()
    }

    $.ajax({
        url: "api.php",
        method: "POST",
        data: datos,
        success: (response) => {
            if (response.status === 'success') {
                $('#formulario-ingreso')[0].reset();
                limpiarErrores();
                cargarUsuarios(true); //true -> resltar usuario que voy a ingresar
            }
        },
        error: (xhr) => {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                mostrarErrores(xhr.responseJSON.errors);
            } else {
                let mensaje = 'No se pudo guardar el usuario.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                $('#lista-usuarios .text-danger').remove();
                $('#lista-usuarios').append(`<p class="text-danger text-center">${mensaje}</p>`);
            }
        }
    });
}

function mostrarErrores(errores) {
    limpiarErrores(); //Limpio errores previos antes de mostrar nuevos

    if (errores.nombre) {
        $("#nombre-feedback").text(errores.nombre);
        $("#nombre").addClass("is-invalid");
    }
    if (errores.email) {
        $("#email-feedback").text(errores.email);
        $("#email").addClass("is-invalid");
    }
    if (errores.telefono) {
        $("#telefono-feedback").text(errores.telefono);
        $("#telefono").addClass("is-invalid");
    }

}

function limpiarErrores() {
    $('#formulario-ingreso .is-invalid').removeClass('is-invalid');
    $('#formulario-ingreso .invalid-feedback').empty();
}

function crearTarjeta(usuario) {
    //Si telefono es null entonces muestro un guion
    return `
        <div class="card tarjeta-usuario">
                                        <div class="card-body">
                                            <div class="campo">
                                                <span class="campo-label">Nombre:</span>
                                                <span class="campo-valor">${usuario.nombre}</span>
                                            </div>
                                            <div class="campo">
                                                <span class="campo-label">Email:</span>
                                                <span
                                                    class="campo-valor email">${usuario.email}</span>
                                            </div>
                                            <div class="campo">
                                                <span class="campo-label">Teléfono:</span>
                                                <span class="campo-valor">${usuario.telefono || '-'}</span>
                                            </div>
                                            <div class="campo">
                                                <span class="campo-label">Ingresado:</span>
                                                <span class="campo-valor">${usuario.fecha_ingresado}</span>
                                            </div>
                                        </div>
        </div>    
    `;

}