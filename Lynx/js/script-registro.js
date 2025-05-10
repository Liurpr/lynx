// =================================================
// validacion para el formulario de registro
// =================================================

const formularioRegistro = document.getElementById('formulario-registro');
const mostrarContrasena = document.getElementById('mostrar-contrasena');
const inputContrasena = document.getElementById('contrasena');

// funcionalidad para mostrar/ocultar contraseña con mejoras de accesibilidad
if (mostrarContrasena && inputContrasena) {
    // mejorar accesibilidad con aria-label
    mostrarContrasena.setAttribute('aria-label', 'Mostrar contraseña');
    mostrarContrasena.setAttribute('tabindex', '0');

    // funcion para alternar visibilidad de la contraseña
    function toggleContrasena() {
        if (inputContrasena.type === 'password') {
            inputContrasena.type = 'text';
            mostrarContrasena.classList.remove('fa-eye-slash');
            mostrarContrasena.classList.add('fa-eye');
            mostrarContrasena.setAttribute('aria-label', 'Ocultar contraseña');
        } else {
            inputContrasena.type = 'password';
            mostrarContrasena.classList.remove('fa-eye');
            mostrarContrasena.classList.add('fa-eye-slash');
            mostrarContrasena.setAttribute('aria-label', 'Mostrar contraseña');
        }
        // mantener el foco en el campo
        inputContrasena.focus();
    }

    // evento de clic para el icono
    mostrarContrasena.addEventListener('click', toggleContrasena);

    // soporte para teclado (accesibilidad)
    mostrarContrasena.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            toggleContrasena();
        }
    });
}

if (formularioRegistro) {
    // seleccionar elementos del formulario
    const usuario = document.getElementById('usuario');
    const correo = document.getElementById('correo');
    const confirmarCorreo = document.getElementById('confirmar-correo');
    const contrasena = document.getElementById('contrasena');
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    
    // establecer fecha maxima (hoy)
    if (fechaNacimiento) {
        const hoy = new Date();
        const formatoFecha = hoy.toISOString().split('T')[0];
        fechaNacimiento.setAttribute('max', formatoFecha);
    }
    
    // funcion para mostrar errores
    function mostrarError(elemento, mensaje) {
        const input = document.getElementById(elemento);
        const mensajeError = document.getElementById('error-' + elemento);
        
        if (input && mensajeError) {
            input.classList.add('campo-error');
            mensajeError.classList.add('mostrar-error');
            
            if (mensaje) {
                mensajeError.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + mensaje;
            }
        }
    }
    
    // funcion para ocultar errores
    function ocultarError(elemento) {
        const input = document.getElementById(elemento);
        const mensajeError = document.getElementById('error-' + elemento);
        
        if (input && mensajeError) {
            input.classList.remove('campo-error');
            mensajeError.classList.remove('mostrar-error');
        }
    }
    
    // ocultar todos los mensajes de error al inicio
    function ocultarTodosErrores() {
        const mensajesError = document.querySelectorAll('.mensaje-error');
        mensajesError.forEach(mensaje => {
            mensaje.classList.remove('mostrar-error');
        });
    }
    
    // Ocultar todos los errores al cargar la página
    ocultarTodosErrores();
    
    // validaciones en tiempo real
    if (usuario) {
        usuario.addEventListener('input', function() {
            if (usuario.value.trim() === '') {
                mostrarError('usuario', 'el nombre de usuario es obligatorio.');
            } else if (!/^[a-zA-Z0-9]{3,20}$/.test(usuario.value)) {
                mostrarError('usuario', 'debe tener entre 3 y 20 caracteres alfanumericos.');
            } else {
                ocultarError('usuario');
            }
        });
    }
    
    if (correo) {
        correo.addEventListener('input', function() {
            if (correo.value.trim() === '') {
                mostrarError('correo', 'el correo electronico es obligatorio.');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value)) {
                mostrarError('correo', 'ingresa un correo electronico valido.');
            } else {
                ocultarError('correo');
                // verificar coincidencia si confirmar-correo tiene valor
                if (confirmarCorreo && confirmarCorreo.value.trim() !== '') {
                    if (correo.value !== confirmarCorreo.value) {
                        mostrarError('confirmar-correo', 'los correos electronicos no coinciden.');
                    } else {
                        ocultarError('confirmar-correo');
                    }
                }
            }
        });
    }
    
    if (confirmarCorreo) {
        confirmarCorreo.addEventListener('input', function() {
            if (confirmarCorreo.value.trim() === '') {
                mostrarError('confirmar-correo', 'confirmar el correo es obligatorio.');
            } else if (correo && confirmarCorreo.value !== correo.value) {
                mostrarError('confirmar-correo', 'los correos electronicos no coinciden.');
            } else {
                ocultarError('confirmar-correo');
            }
        });
    }
    
    if (fechaNacimiento) {
        fechaNacimiento.addEventListener('input', function() {
            if (fechaNacimiento.value === '') {
                mostrarError('fecha_nacimiento', 'la fecha de nacimiento es obligatoria.');
            } else {
                // calcular edad
                const hoy = new Date();
                const fechaNac = new Date(fechaNacimiento.value);
                let edad = hoy.getFullYear() - fechaNac.getFullYear();
                const m = hoy.getMonth() - fechaNac.getMonth();
                
                if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
                    edad--;
                }
                
                if (edad < 5 || edad > 100) {
                    mostrarError('fecha_nacimiento', 'edad invalida. debes tener entre 5 y 100 años.');
                } else {
                    ocultarError('fecha_nacimiento');
                }
            }
        });
    }
    
    if (contrasena) {
        contrasena.addEventListener('input', function() {
            if (contrasena.value === '') {
                mostrarError('contrasena', 'la contraseña es obligatoria.');
            } else if (contrasena.value.length < 8) {
                mostrarError('contrasena', 'la contraseña debe tener al menos 8 caracteres.');
            } else if (!/[A-Za-z]/.test(contrasena.value) || !/[0-9]/.test(contrasena.value)) {
                mostrarError('contrasena', 'la contraseña debe contener letras y numeros.');
            } else {
                ocultarError('contrasena');
            }
        });
    }
    
    // validacion al enviar el formulario
    formularioRegistro.addEventListener('submit', function(e) {
        let formValido = true;
        
        // validar usuario
        if (usuario) {
            if (usuario.value.trim() === '') {
                mostrarError('usuario', 'el nombre de usuario es obligatorio.');
                formValido = false;
            } else if (!/^[a-zA-Z0-9]{3,20}$/.test(usuario.value)) {
                mostrarError('usuario', 'debe tener entre 3 y 20 caracteres alfanumericos.');
                formValido = false;
            }
        }
        
        // validar correo
        if (correo) {
            if (correo.value.trim() === '') {
                mostrarError('correo', 'el correo electronico es obligatorio.');
                formValido = false;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo.value)) {
                mostrarError('correo', 'ingresa un correo electronico valido.');
                formValido = false;
            }
        }
        
        // validar confirmar correo
        if (confirmarCorreo) {
            if (confirmarCorreo.value.trim() === '') {
                mostrarError('confirmar-correo', 'confirmar el correo es obligatorio.');
                formValido = false;
            } else if (correo && confirmarCorreo.value !== correo.value) {
                mostrarError('confirmar-correo', 'los correos electronicos no coinciden.');
                formValido = false;
            }
        }
        
        // validar fecha de nacimiento
        if (fechaNacimiento) {
            if (fechaNacimiento.value === '') {
                mostrarError('fecha_nacimiento', 'la fecha de nacimiento es obligatoria.');
                formValido = false;
            } else {
                // calcular edad
                const hoy = new Date();
                const fechaNac = new Date(fechaNacimiento.value);
                let edad = hoy.getFullYear() - fechaNac.getFullYear();
                const m = hoy.getMonth() - fechaNac.getMonth();
                
                if (m < 0 || (m === 0 && hoy.getDate() < fechaNac.getDate())) {
                    edad--;
                }
                
                if (edad < 5 || edad > 100) {
                    mostrarError('fecha_nacimiento', 'edad invalida. debes tener entre 5 y 100 años.');
                    formValido = false;
                }
            }
        }
        
        // validar contraseña
        if (contrasena) {
            if (contrasena.value === '') {
                mostrarError('contrasena', 'la contraseña es obligatoria.');
                formValido = false;
            } else if (contrasena.value.length < 8) {
                mostrarError('contrasena', 'la contraseña debe tener al menos 8 caracteres.');
                formValido = false;
            } else if (!/[A-Za-z]/.test(contrasena.value) || !/[0-9]/.test(contrasena.value)) {
                mostrarError('contrasena', 'la contraseña debe contener letras y numeros.');
                formValido = false;
            }
        }
        
        // validar términos y condiciones
        const terminos = document.getElementById('terminos');
        if (terminos && !terminos.checked) {
            const errorTerminos = document.getElementById('error-terminos');
            if (errorTerminos) {
                errorTerminos.style.display = 'block';
            }
            formValido = false;
        }
        
        if (!formValido) {
            e.preventDefault();
        }
    });
}