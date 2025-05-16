document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos
    const modalTerminos = document.getElementById('modal-terminos');
    const modalPrivacidad = document.getElementById('modal-privacidad');
    const mostrarTerminos = document.getElementById('mostrar-terminos');
    const mostrarPrivacidad = document.getElementById('mostrar-privacidad');
    const cerrarTerminos = document.getElementById('cerrar-terminos');
    const cerrarPrivacidad = document.getElementById('cerrar-privacidad');
    const aceptarTerminos = document.getElementById('aceptar-terminos');
    const aceptarPrivacidad = document.getElementById('aceptar-privacidad');
    const checkboxTerminos = document.getElementById('terminos');
    const mensajeErrorTerminos = document.getElementById('error-terminos');
    
    // Mostrar modal de términos
    mostrarTerminos.addEventListener('click', function() {
        modalTerminos.style.display = 'block';
    });
    
    // Mostrar modal de privacidad
    mostrarPrivacidad.addEventListener('click', function() {
        modalPrivacidad.style.display = 'block';
    });
    
    // Cerrar modales
    cerrarTerminos.addEventListener('click', function() {
        modalTerminos.style.display = 'none';
    });
    
    cerrarPrivacidad.addEventListener('click', function() {
        modalPrivacidad.style.display = 'none';
    });
    
    aceptarTerminos.addEventListener('click', function() {
        modalTerminos.style.display = 'none';
    });
    
    aceptarPrivacidad.addEventListener('click', function() {
        modalPrivacidad.style.display = 'none';
    });
    
    // Cerrar modales si se hace clic fuera de ellos
    window.addEventListener('click', function(event) {
        if (event.target == modalTerminos) {
            modalTerminos.style.display = 'none';
        }
        if (event.target == modalPrivacidad) {
            modalPrivacidad.style.display = 'none';
        }
    });
    
    // Validar checkbox de términos
    checkboxTerminos.addEventListener('change', function() {
        if (this.checked) {
            mensajeErrorTerminos.style.display = 'none';
        }
    });
    
    // Validación del formulario
    const formulario = document.getElementById('formulario-registro');
    
    formulario.addEventListener('submit', function(event) {
        let valido = true;
        
        // Validar términos y condiciones
        if (!checkboxTerminos.checked) {
            mensajeErrorTerminos.style.display = 'block';
            valido = false;
        } else {
            mensajeErrorTerminos.style.display = 'none';
        }
        
        // Si hay errores, evitar envío del formulario
        if (!valido) {
            event.preventDefault();
        }
    });
    
    // Actualizar validación de usuario para mínimo 4 caracteres
    const usuarioInput = document.getElementById('usuario');
    const errorUsuario = document.getElementById('error-usuario');
    
    usuarioInput.addEventListener('input', function() {
        if (this.value.length > 0 && this.value.length < 4) {
            errorUsuario.style.display = 'block';
            this.classList.add('campo-error');
        } else {
            errorUsuario.style.display = 'none';
            this.classList.remove('campo-error');
        }
    });
});