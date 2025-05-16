document.addEventListener('DOMContentLoaded', function() {
    // contador de caracteres para el formulario de mensaje
    // porque nadie puede resistirse a escribir la biblia completa
    const mensajeTextarea = document.getElementById('mensaje');
    const contadorMensaje = document.getElementById('contador-mensaje');
    
    if (mensajeTextarea && contadorMensaje) {
        mensajeTextarea.addEventListener('input', function() {
            const caracteresActuales = this.value.length;
            contadorMensaje.textContent = caracteresActuales;
            
            // cambiar color si se acerca al limite rojo significa peligro, como cuando vale vrga el semestre 
            if (caracteresActuales > 450) {
                contadorMensaje.classList.add('contador-limite');
            } else {
                contadorMensaje.classList.remove('contador-limite');
            }
        });
    }
    
    // manejar likes, dislikes y comentarios
    document.querySelectorAll('.boton-accion-mensaje').forEach(boton => {
        boton.addEventListener('click', function(e) {
            e.preventDefault();
            
            // si el usuario no esta logueado, redirigir a login (no hay likes anonimos aqui, asume tu responsabilidad)
            if (!esUsuarioLogueado()) {
                window.location.href = 'iniciosesion.php?redirect=comunidad.php';
                return;
            }
            
            const accion = this.getAttribute('data-accion');
            const mensajeId = this.getAttribute('data-mensaje-id');
            
            switch(accion) {
                case 'like':
                    manejarLike(this, mensajeId);
                    break;
                case 'dislike':
                    manejarDislike(this, mensajeId);
                    break;
                case 'mostrar-comentarios':
                    toggleComentarios(mensajeId);
                    break;
                case 'compartir':
                    compartirMensaje(mensajeId);
                    break;
            }
        });
    });
    
    // manejar envio de comentarios (porque todos quieren dar su opinion aunque nadie la pidio)
    document.querySelectorAll('.boton-enviar-comentario').forEach(boton => {
        boton.addEventListener('click', function() {
            const mensajeId = this.getAttribute('data-mensaje-id');
            const textareaComentario = this.closest('.formulario-comentario').querySelector('.input-comentario');
            const comentario = textareaComentario.value.trim();
            
            if (comentario) {
                enviarComentario(mensajeId, comentario, textareaComentario);
            }
        });
    });
});

// funcion para verificar si el usuario esta logueado (detectives privados de sesiones)
function esUsuarioLogueado() {
    // por ahora usaremos una verificacion simple basada en los elementos del DOM
    return !document.querySelector('.mensaje-invitado');
}

// funcion para manejar los likes
function manejarLike(botonLike, mensajeId) {
    const tieneClaseActiva = botonLike.classList.contains('activo');
    const accion = tieneClaseActiva ? 'unlike' : 'like';
    
    // buscar el boton de dislike relacionado porque no puedes odiar y amar algo al mismo tiempo
    const botonDislike = botonLike.closest('.acciones-mensaje').querySelector('.no-me-gusta');
    
    // si el boton de dislike esta activo, desactivarlo primero (traicion al odio, el amor triunfa)
    if (botonDislike && botonDislike.classList.contains('activo')) {
        botonDislike.classList.remove('activo');
        botonDislike.querySelector('i').classList.remove('fa-solid');
        botonDislike.querySelector('i').classList.add('fa-regular');
        
        // actualizar el contador de dislikes (un hater menos en el mundo)
        const contadorDislikes = botonDislike.querySelector('.contador-acciones');
        contadorDislikes.textContent = parseInt(contadorDislikes.textContent) - 1;
    }
    
    // realizar la peticion AJAX (ajax, el heroe silencioso de internet)
    const formData = new FormData();
    formData.append('accion', accion);
    formData.append('mensaje_id', mensajeId);
    
    fetch('acciones_comunidad.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            // actualizar la UI
            const icono = botonLike.querySelector('i');
            const contador = botonLike.querySelector('.contador-acciones');
            
            if (accion === 'like') {
                botonLike.classList.add('activo');
                icono.classList.remove('fa-regular');
                icono.classList.add('fa-solid');
                contador.textContent = parseInt(contador.textContent) + 1;
            } else {
                botonLike.classList.remove('activo');
                icono.classList.remove('fa-solid');
                icono.classList.add('fa-regular');
                contador.textContent = parseInt(contador.textContent) - 1;
            }
        } else {
            mostrarMensajeError(data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensajeError('Error al procesar la solicitud.');
    });
}

// funcion para manejar los dislikes porque odiar es mas facil que dar like
function manejarDislike(botonDislike, mensajeId) {
    const tieneClaseActiva = botonDislike.classList.contains('activo');
    const accion = tieneClaseActiva ? 'unlike' : 'dislike';
    
    // buscar el boton de like relacionado
    const botonLike = botonDislike.closest('.acciones-mensaje').querySelector('.me-gusta');
    
    // si el boton de like esta activo, desactivarlo primero (traicion al amor, el odio gana esta vez)
    if (botonLike && botonLike.classList.contains('activo')) {
        botonLike.classList.remove('activo');
        botonLike.querySelector('i').classList.remove('fa-solid');
        botonLike.querySelector('i').classList.add('fa-regular');
        
        // actualizar el contador de likes (un admirador menos en tu club de fans)
        const contadorLikes = botonLike.querySelector('.contador-acciones');
        contadorLikes.textContent = parseInt(contadorLikes.textContent) - 1;
    }
    
    // realizar la peticion AJAX mientras tanto en el servidor...
    const formData = new FormData();
    formData.append('accion', accion);
    formData.append('mensaje_id', mensajeId);
    
    fetch('acciones_comunidad.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            // actualizar la UI
            const icono = botonDislike.querySelector('i');
            const contador = botonDislike.querySelector('.contador-acciones');
            
            if (accion === 'dislike') {
                botonDislike.classList.add('activo');
                icono.classList.remove('fa-regular');
                icono.classList.add('fa-solid');
                contador.textContent = parseInt(contador.textContent) + 1;
            } else {
                botonDislike.classList.remove('activo');
                icono.classList.remove('fa-solid');
                icono.classList.add('fa-regular');
                contador.textContent = parseInt(contador.textContent) - 1;
            }
        } else {
            mostrarMensajeError(data.mensaje);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensajeError('Error al procesar la solicitud.');
    });
}

// funcion para mostrar/ocultar la seccion de comentarios
function toggleComentarios(mensajeId) {
    const tarjetaMensaje = document.querySelector(`.tarjeta-mensaje[data-mensaje-id="${mensajeId}"]`);
    const seccionComentarios = tarjetaMensaje.querySelector('.seccion-comentarios');
    
    if (seccionComentarios.style.display === 'none') {
        seccionComentarios.style.display = 'block';
        cargarComentarios(mensajeId);
    } else {
        seccionComentarios.style.display = 'none';
    }
}

// funcion para cargar comentarios (invocando opiniones del mas alla)
function cargarComentarios(mensajeId) {
    const listaComentarios = document.querySelector(`.lista-comentarios[data-mensaje-id="${mensajeId}"]`);
    
    fetch(`acciones_comunidad.php?accion=obtener_comentarios&mensaje_id=${mensajeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.exito) {
                // limpiar el contenedor borrando evidencia anterior
                listaComentarios.innerHTML = '';
                
                if (data.comentarios.length === 0) {
                    listaComentarios.innerHTML = '<div class="sin-comentarios">No hay comentarios aún. ¡Sé el primero!</div>';
                } else {
                    // mostrar los comentarios hora de mostrar lo que piensa la gente
                    data.comentarios.forEach(comentario => {
                        listaComentarios.appendChild(crearElementoComentario(comentario));
                    });
                }
            } else {
                listaComentarios.innerHTML = '<div class="error-comentarios">Error al cargar comentarios.</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            listaComentarios.innerHTML = '<div class="error-comentarios">Error al cargar comentarios.</div>';
        });
}

// funcion para crear un elemento de comentario fabrica de opiniones no solicitadas
function crearElementoComentario(comentario) {
    const comentarioDiv = document.createElement('div');
    comentarioDiv.className = 'comentario-item';
    
    comentarioDiv.innerHTML = `
        <div class="cabecera-comentario">
            <img src="${comentario.foto_perfil}" alt="Foto de perfil" class="mini-foto-perfil">
            <div class="info-comentario">
                <span class="nombre-usuario-comentario">${comentario.nombre_usuario}</span>
                <span class="fecha-comentario">${comentario.fecha_formateada}</span>
            </div>
        </div>
        <div class="contenido-comentario">
            <p>${comentario.comentario}</p>
        </div>
    `;
    
    return comentarioDiv;
}

// funcion para enviar un comentario 
function enviarComentario(mensajeId, comentario, textareaComentario) {
    // deshabilitar textarea y boton mientras se envia paciencia joven padawan
    textareaComentario.disabled = true;
    
    const formData = new FormData();
    formData.append('accion', 'comentar');
    formData.append('mensaje_id', mensajeId);
    formData.append('comentario', comentario);
    
    fetch('acciones_comunidad.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            // limpiar textareaborrando evidencia del crimen
            textareaComentario.value = '';
            
            // actualizar lista de comentarios
            const listaComentarios = document.querySelector(`.lista-comentarios[data-mensaje-id="${mensajeId}"]`);
            
            // si es el primer comentario, limpiar el mensaje de "no hay comentarios" adios soledad, hola controversia
            if (listaComentarios.querySelector('.sin-comentarios')) {
                listaComentarios.innerHTML = '';
            }
            
            // añadir el nuevo comentario al inicio tu opinion primero, porque eres especial
            listaComentarios.insertBefore(crearElementoComentario(data.comentario), listaComentarios.firstChild);
            
            // actualizar contador de comentarios
            const botonComentar = document.querySelector(`.boton-accion-mensaje[data-accion="mostrar-comentarios"][data-mensaje-id="${mensajeId}"]`);
            const contador = botonComentar.querySelector('.contador-acciones');
            contador.textContent = parseInt(contador.textContent) + 1;
        } else {
            mostrarMensajeError(data.mensaje);
        }
        // rehabilitar textarea devolviendo la libertad de expresion
        textareaComentario.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarMensajeError('Error al enviar el comentario.');
        textareaComentario.disabled = false;
    });
}

// funcion para compartir un mensaje
function compartirMensaje(mensajeId) {
    // verificar si el navegador soporta la API de compartir
    if (navigator.share) {
        // obtener URL de la pagina actual
        const urlBase = window.location.origin + window.location.pathname;
        const urlCompartir = `${urlBase}?mensaje=${mensajeId}`;
        
        navigator.share({
            title: 'Mensaje compartido de Lynx Comunidad',
            text: 'Mira este mensaje de la comunidad Lynx',
            url: urlCompartir
        })
        .then(() => console.log('Mensaje compartido exitosamente'))
        .catch((error) => {
            console.error('Error al compartir:', error);
            copiarAlPortapapeles(urlCompartir);
        });
    } else {
        // fallback para navegadores que no soportan la API Share plan b para navegadores anticuados
        const urlBase = window.location.origin + window.location.pathname;
        const urlCompartir = `${urlBase}?mensaje=${mensajeId}`;
        copiarAlPortapapeles(urlCompartir);
    }
}

// funcion para copiar texto al portapapeles
function copiarAlPortapapeles(texto) {
    const input = document.createElement('input');
    input.style.position = 'fixed';
    input.style.opacity = '0';
    input.value = texto;
    document.body.appendChild(input);
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    document.body.removeChild(input);
    
    mostrarMensajeExito('Enlace copiado al portapapeles');
}

// funcion para mostrar mensaje de error
function mostrarMensajeError(mensaje) {
    mostrarMensajeAlerta(mensaje, 'error');
}

// funcion para mostrar mensaje de exito
function mostrarMensajeExito(mensaje) {
    mostrarMensajeAlerta(mensaje, 'exito');
}

// funcion para mostrar alertas temporales
function mostrarMensajeAlerta(mensaje, tipo) {
    const alertaExistente = document.querySelector('.mensaje-alerta-flotante');
    if (alertaExistente) {
        alertaExistente.remove();
    }
    
    const alerta = document.createElement('div');
    alerta.className = `mensaje-alerta mensaje-alerta-flotante ${tipo}`;
    
    const icono = document.createElement('i');
    if (tipo === 'exito') {
        icono.className = 'fas fa-check-circle';
    } else if (tipo === 'error') {
        icono.className = 'fas fa-exclamation-circle';
    } else if (tipo === 'advertencia') {
        icono.className = 'fas fa-exclamation-triangle';
    }
    
    const texto = document.createElement('p');
    texto.textContent = mensaje;
    
    alerta.appendChild(icono);
    alerta.appendChild(texto);
    document.body.appendChild(alerta);
    
    // posicionar la alerta en la parte superior
    alerta.style.position = 'fixed';
    alerta.style.top = '20px';
    alerta.style.left = '50%';
    alerta.style.transform = 'translateX(-50%)';
    alerta.style.zIndex = '9999';
    alerta.style.opacity = '0';
    alerta.style.transition = 'opacity 0.3s ease-in-out';
    
    // mostrar la alerta con animacion
    setTimeout(() => {
        alerta.style.opacity = '1';
    }, 10);
    
    // ocultar despues de 3 segundos
    setTimeout(() => {
        alerta.style.opacity = '0';
        setTimeout(() => {
            alerta.remove();
        }, 300);
    }, 3000);
}