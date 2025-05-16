<header>
        <div class="contenedor-nav">
            <!-- logo del sitio -->
            <a href="index.php" class="logo">Lynx</a>
            
            <!-- menú de navegacion principal -->
            <nav>
                <ul class="menu-nav" id="menu-nav">
                    <li><a href="#" class="enlace-nav">Glosario</a></li>
                    <li class="submenu-contenedor">
                        <a href="#" class="enlace-nav">Recursos <i class="fa-solid fa-chevron-down"></i></a>
                        <!-- submenú pa los recursos -->
                        <ul class="submenu">
                            <li><a href="#">Actividades</a></li>
                            <li><a href="#">Ejercicios</a></li>
                            <li><a href="#">Lecciones</a></li>
                            <li><a href="#">Traductor</a></li>
                        </ul>
                    </li>
                    <li><a href="comunidad.php" class="enlace-nav">Comunidad</a></li>
                    
                    <?php if (!isset($_SESSION['usuario_id'])): ?>
                    <!-- este if checa si no hay sesion iniciada y muestra el link de registro -->
                    <li><a href="registro.php" class="enlace-nav">Registro</a></li>
                    <?php endif; ?>
                    <li><a href="changelog.php" class="enlace-nav">Control de actividad</a></li>
                </ul>
            </nav>
            
            <!-- controles de navegación -->
            <div class="controles-nav">
                <!-- botón de cambio de tema -->
                <button class="boton-tema" id="boton-tema" title="Cambiar tema">
                    <i class="fa-solid fa-moon icono-luna"></i>
                    <i class="fa-solid fa-sun icono-sol"></i>
                </button>
                
                <?php if (isset($_SESSION['usuario_id'])): ?>
                <!-- este codigo muestra opciones solo si el usuario tiene sesion iniciada -->
                <div class="menu-usuario-contenedor">
                    <a href="perfil.php" class="icono-perfil" title="Perfil">
                        <i class="fa-solid fa-user"></i>
                    </a>
                    <div class="menu-usuario">
                        <ul>
                            <li><a href="perfil.php">mi perfil</a></li>
                            <li><a href="logout.php">cerrar sesion</a></li>
                        </ul>
                    </div>
                </div>
                <?php else: ?>
                <!-- si no hay sesion iniciada muestra el boton para iniciar sesion -->
                <a href="iniciosesion.php" class="icono-perfil" title="Iniciar sesión">
                    <i class="fa-solid fa-right-to-bracket"></i>
                </a>
                <?php endif; ?>
                
                <!-- botón hamburguesa para menú móvil -->
                <button class="hamburguesa" id="hamburguesa" aria-label="Menú">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>