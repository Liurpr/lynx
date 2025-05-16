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
            
            if (!formValido) {
                e.preventDefault();
            }
        });
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
    
    // ============================================
    // contador de caracteres para descripcion
    // ============================================
    
    const descripcion = document.getElementById('descripcion');
    const contador = document.getElementById('contador');
    
    if (descripcion && contador) {
        // actualizar contador inicial
        contador.textContent = descripcion.value.length;
        
        // actualizar al escribir
        descripcion.addEventListener('input', function() {
            contador.textContent = this.value.length;
            
            // cambiar color si se acerca al límite
            if (this.value.length > 450) {
                contador.style.color = '#ff6b6b';
            } else {
                contador.style.color = '';
            }
        });
    }
    
    // ============================================
    // mostrar nombre de archivo seleccionado
    // ============================================
    
    const inputFilePerfil = document.getElementById('foto_perfil');
    const fileLabel2 = document.getElementById('archivo-seleccionado');
    
    if (inputFilePerfil && fileLabel2) {
        inputFilePerfil.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileName = this.files[0].name;
                const fileSize = Math.round(this.files[0].size / 1024); // kb
                
                fileLabel2.textContent = `${fileName} (${fileSize} KB)`;
                
                // validar tamaño
                if (fileSize > 2048) {
                    alert('la imagen no debe superar los 2MB');
                    this.value = '';
                    fileLabel2.textContent = 'ningún archivo seleccionado';
                }
            } else {
                fileLabel2.textContent = 'ningún archivo seleccionado';
            }
        });
    }
    
    // ============================================
    // confirmacion para eliminar foto
    // ============================================
    
    const formEliminarFoto = document.querySelector('.form-eliminar-foto');
    
    if (formEliminarFoto) {
        formEliminarFoto.addEventListener('submit', function(e) {
            if (!confirm('¿estás seguro de que deseas eliminar tu foto de perfil?')) {
                e.preventDefault();
            }
        });
    }
    
    // ============================================
    // efecto hover para mostrar botones de foto
    // ============================================
    
    const contenedorFoto = document.querySelector('.contenedor-foto-perfil');
    const overlayFoto = document.querySelector('.overlay-foto');
    
    if (contenedorFoto && overlayFoto) {
        contenedorFoto.addEventListener('mouseenter', function() {
            overlayFoto.classList.add('visible');
        });
        
        contenedorFoto.addEventListener('mouseleave', function() {
            overlayFoto.classList.remove('visible');
        });
    }

    // ================================================================
    // funciones del perfil (a veces hay que arreglarse para la foto)
    // ================================================================
    
    // resetear el formulario de perfil
    const resetBtn = document.querySelector('button[type="reset"]');
    const formPerfil = document.getElementById('form-perfil');
    
    if (resetBtn && formPerfil) {
        resetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            formPerfil.reset();
            if (fileLabel2) {
                fileLabel2.textContent = 'ningún archivo seleccionado';
            }
            if (descripcion && contador) {
                contador.textContent = descripcion.value.length;
            }
        });
    }

    // ================================================================
    // cambiar roles con id o nombre
    // ================================================================

    // script para alternar entre campos de busqueda
    const radioBotones = document.querySelectorAll('input[name="tipo_busqueda"]');
    const campoId = document.querySelector('.campo-id');
    const campoNombre = document.querySelector('.campo-nombre');
        
    radioBotones.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'id') {
                campoId.style.display = 'block';
                campoNombre.style.display = 'none';
                document.getElementById('usuario_id').setAttribute('required', '');
                document.getElementById('nombre_usuario').removeAttribute('required');
            } else {
                campoId.style.display = 'none';
                campoNombre.style.display = 'block';
                document.getElementById('nombre_usuario').setAttribute('required', '');
                document.getElementById('usuario_id').removeAttribute('required');
            }
        });
    });
});