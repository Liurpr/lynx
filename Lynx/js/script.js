// esperar a que el DOM esté completamente cargado antes de ejecutar el script
// el DOM (Document Object Model) es el html y el css, en pocas palabras, 
// esta esperando que cargue lo que es estatico del sitio (casi todo)
document.addEventListener('DOMContentLoaded', function() {
    
    // ============================================
    // cambiar de tema (modo Axel/ modo Neri)
    // ============================================
    
    // seleccionar elementos necesarios para el cambio de tema
    const botonTema = document.getElementById('boton-tema');
    const html = document.documentElement;
    
    // verificar si hay un tema guardado en localStorage (de cierta forma en el cache)
    const temaGuardado = localStorage.getItem('tema');
    
    // si hay un tema guardado, aplicarlo
    if (temaGuardado) {
        html.setAttribute('data-tema', temaGuardado);
    }
    
    // función para cambiar el tema
    function cambiarTema() {
        // obtener el tema actual
        const temaActual = html.getAttribute('data-tema');
        // definir el nuevo tema (alternar entre claro y oscuro)
        const nuevoTema = temaActual === 'claro' ? 'oscuro' : 'claro';
        
        // aplicar el nuevo tema
        html.setAttribute('data-tema', nuevoTema);
        // guardar el tema en localStorage para mantenerlo en futuras visitas
        localStorage.setItem('tema', nuevoTema);
    }
    
    // asignar evento de clic al botón de tema
    botonTema.addEventListener('click', cambiarTema);
    
    // =========================================================================================
    // menu hamburguesa chidoris
    // =========================================================================================
    
    // seleccionar elementos necesarios para el menú móvil
    const botonHamburguesa = document.getElementById('hamburguesa');
    const menuNav = document.getElementById('menu-nav');
    
    // función para alternar la visibilidad del menú móvil
    function toggleMenuMovil() {
        // alternar clase activo en el botón hamburguesa
        botonHamburguesa.classList.toggle('activo');
        // alternar clase activo en el menú de navegación
        menuNav.classList.toggle('activo');
    }
    
    // asignar evento de clic al botón hamburguesa
    botonHamburguesa.addEventListener('click', toggleMenuMovil);
    
    // cerrar el menú móvil cuando se hace clic en un enlace (para mejor experiencia de usuario o user experience)
    const enlacesNav = document.querySelectorAll('.enlace-nav');
    
    enlacesNav.forEach(enlace => {
        enlace.addEventListener('click', function() {
            // verificar si el menú está activo (solo en vista de un chaifon)
            if (window.innerWidth <= 768 && menuNav.classList.contains('activo')) {
                toggleMenuMovil();
            }
        });
    });
    
    // ===========================================================
    // funcionalidad para mostrar/ocultar contraseña (me ves, ya no me ves XD)
    // ===========================================================
    
    const mostrarContrasena = document.getElementById('mostrar-contrasena');
    const inputContrasena = document.getElementById('contrasena');
    
    if (mostrarContrasena && inputContrasena) {
        mostrarContrasena.addEventListener('click', function() {
            if (inputContrasena.type === 'password') {
                inputContrasena.type = 'text';
                mostrarContrasena.classList.remove('fa-eye-slash');
                mostrarContrasena.classList.add('fa-eye');
            } else {
                inputContrasena.type = 'password';
                mostrarContrasena.classList.remove('fa-eye');
                mostrarContrasena.classList.add('fa-eye-slash');
            }
        });
    }
    
    // ===========================================================
    // validación de formulario para despues
    // ===========================================================
    
    const formulario = document.getElementById('formulario-registro');
    
    if (formulario) {
        const correo = document.getElementById('correo');
        const confirmarCorreo = document.getElementById('confirmar-correo');
        
        formulario.addEventListener('submit', function(event) {
            let valido = true;
            
            // validar que los correos coincidan
            if (correo && confirmarCorreo && correo.value !== confirmarCorreo.value) {
                mostrarError(confirmarCorreo, 'Los correos electrónicos no coinciden');
                valido = false;
            } else if (confirmarCorreo) {
                limpiarError(confirmarCorreo);
            }
            
            // validar edad (entre 5 y 100)
            const edad = document.getElementById('edad');
            if (edad && (edad.value < 5 || edad.value > 100)) {
                mostrarError(edad, 'Ingresa una edad válida (entre 5 y 120 años)');
                valido = false;
            } else if (edad) {
                limpiarError(edad);
            }
            
            // si no es válido, prevenir envío, porque si no... :c
            if (!valido) {
                event.preventDefault();
            }
        });
    }
    
    // función para mostrar error
    function mostrarError(input, mensaje) {
        const ayudaTexto = input.nextElementSibling;
        if (ayudaTexto && ayudaTexto.classList.contains('ayuda-texto')) {
            ayudaTexto.textContent = mensaje;
            ayudaTexto.classList.add('error');
        } else {
            const nuevoMensaje = document.createElement('p');
            nuevoMensaje.textContent = mensaje;
            nuevoMensaje.classList.add('ayuda-texto', 'error');
            input.parentNode.insertBefore(nuevoMensaje, input.nextSibling);
        }
        input.classList.add('error');
    }
    
    // función para limpiar error
    function limpiarError(input) {
        const ayudaTexto = input.nextElementSibling;
        if (ayudaTexto && ayudaTexto.classList.contains('error')) {
            // si el elemento siguiente es un mensaje de error, restablecerlo
            ayudaTexto.textContent = '.';
            ayudaTexto.classList.remove('error');
        }
        input.classList.remove('error');
    }
    
    // ===========================================================
    // carrusel de testimonios pa mejor presentación
    // ===========================================================
    
    // seleccionar elementos necesarios para el carrusel
    const testimonios = document.querySelectorAll('.testimonio');
    const botonAnterior = document.getElementById('anterior-testimonio');
    const botonSiguiente = document.getElementById('siguiente-testimonio');
    
    // Solo inicializar el carrusel si existen los elementos
    if (testimonios.length > 0 && botonAnterior && botonSiguiente) {
        // indice para seguir el testimonio actual
        let testimonioActual = 0;
        
        // inicializar el carrusel mostrando el primer testimonio
        function inicializarCarrusel() {
            testimonios[testimonioActual].classList.add('activo');
        }
        
        // función para mostrar un testimonio específico
        function mostrarTestimonio(indice) {
            // ocultar todos los testimonios
            testimonios.forEach(testimonio => {
                testimonio.classList.remove('activo');
            });
            
            // mostrar el testimonio del índice indicado
            testimonios[indice].classList.add('activo');
        }
        
        // función para mostrar el siguiente testimonio
        function siguienteTestimonio() {
            // incrementar el índice y volver al principio si es necesario
            testimonioActual = (testimonioActual + 1) % testimonios.length;
            mostrarTestimonio(testimonioActual);
        }
        
        // función para mostrar el testimonio anterior
        function testimonioAnterior() {
            // decrementar el índice y ajustar al final si es necesario
            testimonioActual = (testimonioActual - 1 + testimonios.length) % testimonios.length;
            mostrarTestimonio(testimonioActual);
        }
        
        // asignar eventos a los botones de control
        botonSiguiente.addEventListener('click', siguienteTestimonio);
        botonAnterior.addEventListener('click', testimonioAnterior);
        
        // iniciar el carrusel
        inicializarCarrusel();
        
        // rotación automática cada 5 segundos para mejor experiencia
        const intervaloCarrusel = setInterval(siguienteTestimonio, 5000);
        
        // detener la rotación automática cuando el usuario interactúa
        [botonAnterior, botonSiguiente].forEach(boton => {
            boton.addEventListener('mouseenter', () => clearInterval(intervaloCarrusel));
        });
    }
    
    // ===========================================================
    // animaciones chidoris para las tarjetas de caracteristicas
    // ===========================================================
    
    // seleccionar todas las tarjetas con animación
    const elementosAnimados = document.querySelectorAll('[data-animacion]');
    
    // función para verificar si un elemento está en el viewport
    function estaEnViewport(elemento) {
        const rect = elemento.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 &&
            rect.bottom >= 0
        );
    }
    
    // función para animar elementos cuando aparecen en pantalla
    function animarAlDesplazar() {
        elementosAnimados.forEach(elemento => {
            if (estaEnViewport(elemento)) {
                // aplicar retraso si está especificado
                const retraso = elemento.getAttribute('data-delay') || 0;
                setTimeout(() => {
                    elemento.classList.add('visible');
                }, retraso);
            }
        });
    }
    
    // ejecutar la animación al cargar la página y al desplazarse
    window.addEventListener('load', animarAlDesplazar);
    window.addEventListener('scroll', animarAlDesplazar);
    
    // ============================================
    // efectos pequeños
    // ============================================
    
    // efecto de desplazamiento suave para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(enlace => {
        enlace.addEventListener('click', function(e) {
            const destino = document.querySelector(this.getAttribute('href'));
            if (destino) {
                e.preventDefault();
                window.scrollTo({
                    top: destino.offsetTop - 80, // ajuste para la barra de navegación
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // cambiar opacidad del encabezado al desplazarse
    const header = document.querySelector('header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.opacity = '0.95';
            } else {
                header.style.opacity = '1';
            }
        });
    }
    
    // =================================================================
    // mostrar nombre del archivo seleccionado (imagen de perfil)
    // =================================================================
    
    const inputFile = document.getElementById('foto_perfil');
    const fileLabel = document.querySelector('.input-file-trigger');
    
    if (inputFile) {
        inputFile.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                fileLabel.innerHTML = '<i class="fa-solid fa-check"></i> ' + this.files[0].name;
            }
        });
    }
});