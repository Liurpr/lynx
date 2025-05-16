<?php
// iniciamos la sesion para poder usar variables de sesion
session_start();
// si el usuario esta logueado guardamos su nombre, si no ponemos "invitado"
$usuario = $_SESSION['usuario'] ?? 'Invitado';
?>

<!DOCTYPE html>
<html lang="es" data-tema="oscuro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Historial de cambios y actualizaciones de Lynx - Plataforma para aprender Lenguaje de Señas Mexicano">
    <title>Changelog - Lynx</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- header del sitio -->
    <?php include("zheader.php"); ?>

    <main>
        <section class="pagina-cabecera">
            <div class="contenedor">
                <h1>Historial de Cambios</h1>
                <p>Seguimiento de todas las actualizaciones y mejoras de Lynx</p>
            </div>
        </section>

        <section class="changelog-container">
            <div class="changelog-header">
                <p>Aquí puedes encontrar un registro detallado de todas las actualizaciones y nuevas características que vamos implementando en Lynx para mejorar tu experiencia de aprendizaje de LSM.</p>
                
                <div class="changelog-stats">
                    <div class="stat-card">
                        <div class="stat-number">4</div>
                        <div class="stat-label">Versiones lanzadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">9</div>
                        <div class="stat-label">Mejoras implementadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">90</div>
                        <div class="stat-label">Días de desarrollo</div>
                    </div>
                </div>
                
                <div class="version-navigation" id="version-nav">
                    <div class="version-nav-title">
                        <i class="fas fa-th-list"></i>
                        Saltar a versión
                    </div>
                    <div class="version-links">
                        <a href="#v0-4-0" class="version-link">v0.4.0</a>
                        <a href="#v0-3-0" class="version-link">v0.3.0</a>
                        <a href="#v0-2-0" class="version-link">v0.2.0</a>
                        <a href="#v0-1-0" class="version-link">v0.1.0</a>
                        <a href="#v0-1-0" class="version-link">v0.0.0</a>
                    </div>
                </div>
                
                <div class="filters">
                    <button class="filter-button active" data-filter="all">
                        <i class="fas fa-layer-group"></i>
                        Todos los cambios
                    </button>
                    <button class="filter-button" data-filter="feature">
                        <i class="fas fa-star"></i>
                        Nuevas características
                    </button>
                    <button class="filter-button" data-filter="improvement">
                        <i class="fas fa-arrow-up"></i>
                        Mejoras
                    </button>
                    <button class="filter-button" data-filter="fix">
                        <i class="fas fa-wrench"></i>
                        Correcciones
                    </button>
                    <button class="filter-button" data-filter="security">
                        <i class="fas fa-shield-alt"></i>
                        Seguridad
                    </button>
                </div>
            </div>

            <div class="changelog-timeline">
                <!-- Versión 0.4.0 -->
                <div class="changelog-version" id="v0-4-0" data-version="0.4.0">
                    <div class="version-dot">
                        <i class="fas fa-bolt"></i>
                    </div>
                    
                    <div class="version-header">
                        <div class="version-title">
                            <h2 class="version-number">v0.4.0</h2>
                            <span class="version-date"><i class="far fa-calendar-alt"></i> 15/05/2025 </span>
                        </div>
                        <div class="version-type">
                            <span class="version-badge"><i class="fas fa-tachometer-alt"></i> Rendimiento</span>
                        </div>
                    </div>
                    
                    <div class="change-categories">
                        <div class="change-category category-improvement" data-category="improvement">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-arrow-up"></i></div>
                                <h3 class="category-title">Mejoras</h3>
                            </div>
                            <ul class="category-list">
                                <li>Mejora en el archivo css para optimización </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Versión 0.3.0 -->
                <div class="changelog-version" id="v0-3-0" data-version="0.3.0">
                    <div class="version-dot">
                        <i class="fas fa-rocket"></i>
                    </div>
                    
                    <div class="version-header">
                        <div class="version-title">
                            <h2 class="version-number">v0.3.0</h2>
                            <span class="version-date"><i class="far fa-calendar-alt"></i> 10/02/2025 - 09/05/2025 </span>
                        </div>
                        <div class="version-type">
                            <span class="version-badge"><i class="fas fa-flag"></i> Bitacora </span>
                        </div>
                    </div>
                    
                    <div class="change-categories">
                        <div class="change-category category-feature" data-category="feature">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-plus"></i></div>
                                <h3 class="category-title">Nuevas Características</h3>
                            </div>
                            <ul class="category-list">
                                <li>Creacion de contenido en las politicas de privacidad</li>
                                <li>Sistema completo de registro y autenticación de usuarios</li>
                                <li>Implementación de perfiles de usuario personalizables con foto, biografía <span class="highlight-tag"><i class="fas fa-fire"></i> Destacado</span></li>
                                <li>Módulos iniciales de aprendizaje de LSM con evaluación interactiva</li>
                            </ul>
                        </div>
                        
                        <div class="change-category category-improvement" data-category="improvement">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-arrow-up"></i></div>
                                <h3 class="category-title">Mejoras</h3>
                            </div>
                            <ul class="category-list">
                                <li>Correccion en el Registro con mensajes de error</li>
                                <li>Soporte completo para modo claro y oscuro con transiciones suaves</li>
                                <li>Optimización para todos los dispositivos y navegadores principales</li>
                            </ul>
                        </div>
                        
                        <div class="change-category category-security" data-category="security">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-shield-alt"></i></div>
                                <h3 class="category-title">Seguridad</h3>
                            </div>
                            <ul class="category-list">
                                <li>Protección contra ataques SQL en el registro</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Versión 0.2.0 -->
                <div class="changelog-version" id="v0-2-0" data-version="0.2.0">
                    <div class="version-dot">
                        <i class="fas fa-bolt"></i>
                    </div>
                    
                    <div class="version-header">
                        <div class="version-title">
                            <h2 class="version-number">v0.2.0</h2>
                            <span class="version-date"><i class="far fa-calendar-alt"></i> 10/02/2025 - 09/05/2025 </span>
                        </div>
                        <div class="version-type">
                            <span class="version-badge"><i class="fas fa-tachometer-alt"></i> Rendimiento</span>
                        </div>
                    </div>
                    
                    <div class="change-categories">
                        <div class="change-category category-improvement" data-category="improvement">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-arrow-up"></i></div>
                                <h3 class="category-title">Mejoras</h3>
                            </div>
                            <ul class="category-list">
                                <li>Optimización significativa de la velocidad de carga en todas las páginas <span class="highlight-tag"><i class="fas fa-fire"></i> Destacado</span></li>
                                <li>Reducción del tamaño de los archivos multimedia sin pérdida de calidad</li>
                                <li>Mejora en la experiencia de navegación en dispositivos de gama baja</li>
                                <li>Minificación y compresión de archivos CSS y JavaScript</li>
                            </ul>
                        </div>
                        
                        <div class="change-category category-fix" data-category="fix">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-wrench"></i></div>
                                <h3 class="category-title">Correcciones</h3>
                            </div>
                            <ul class="category-list">
                                <li>Solución al problema de visualización en el carrusel de testimonios</li>
                                <li>Ajuste del contraste en los botones para mejor accesibilidad</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Versión 0.1.0 -->
                <div class="changelog-version" id="v0-1-0" data-version="0.1.0">
                    <div class="version-dot">
                        <i class="fas fa-flask"></i>
                    </div>
                    
                    <div class="version-header">
                        <div class="version-title">
                            <h2 class="version-number">v0.1.0</h2>
                            <span class="version-date"><i class="far fa-calendar-alt"></i> 10/02/2025 - 09/05/2025 </span>
                        </div>
                        <div class="version-type">
                            <span class="version-badge"><i class="fas fa-users"></i> Beta Pública</span>
                        </div>
                    </div>
                    
                    <div class="change-categories">
                        <div class="change-category category-feature" data-category="feature">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-plus"></i></div>
                                <h3 class="category-title">Nuevas Características</h3>
                            </div>
                            <ul class="category-list">
                                <li>Implementación del sistema de perfiles de usuario</li>
                                <li>Nueva sección "Nuestra Metodología" con los tres pilares fundamentales</li>
                                <li>Sistema de carrusel de testimonios interactivo</li>
                                <li>Primera versión de las tarjetas de características</li>
                            </ul>
                        </div>
                        
                        <div class="change-category category-improvement" data-category="improvement">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-arrow-up"></i></div>
                                <h3 class="category-title">Mejoras</h3>
                            </div>
                            <ul class="category-list">
                                <li>Lorem ipsum dolor sit amet, consectetur adipisicing.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Versión 0.0.0 -->
                <div class="changelog-version" id="v0-0-0" data-version="0.0.0">
                    <div class="version-dot">
                        <i class="fas fa-flask"></i>
                    </div>
                    
                    <div class="version-header">
                        <div class="version-title">
                            <h2 class="version-number">v0.0.0</h2>
                            <span class="version-date"><i class="far fa-calendar-alt"></i> 10/02/2025 - 09/05/2025</span>
                        </div>
                        <div class="version-type">
                            <span class="version-badge"><i class="fas fa-users"></i> Beta Pública</span>
                        </div>
                    </div>
                    
                    <div class="change-categories">
                        <div class="change-category category-feature" data-category="feature">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-plus"></i></div>
                                <h3 class="category-title">Nuevas Características</h3>
                            </div>
                            <ul class="category-list">
                                <li>Elaboracion del index, perfil, registro, inicio de sesion y comunidad <span class="highlight-tag"><i class="fas fa-fire"></i> Destacado</span></li>
                            </ul>
                        </div>
                        
                        <div class="change-category category-improvement" data-category="improvement">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-arrow-up"></i></div>
                                <h3 class="category-title">Mejoras</h3>
                            </div>
                            <ul class="category-list">
                                <li>Optimización de la navegación responsiva para dispositivos móviles</li>
                                <li>Mejora en el contraste de colores para mayor legibilidad</li>
                            </ul>
                        </div>

                        <div class="change-category category-security" data-category="security">
                            <div class="category-header">
                                <div class="category-icon"><i class="fas fa-shield-alt"></i></div>
                                <h3 class="category-title">Seguridad</h3>
                            </div>
                            <ul class="category-list">
                                <li>Cambio en el registro para evitar inyeccion SQL</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- pie de página (footer) -->
    <?php include("zfooter.php"); ?>

    <script src="js/script.js"></script>
    <script src="js/changelog.js"></script>
</body>
</html>