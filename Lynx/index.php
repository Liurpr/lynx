// index para que tiene cuenta
<?php
session_start();
$usuario = $_SESSION['usuario'] ?? 'Invitado'; // Nombre del usuario o "Invitado"
?>

<!DOCTYPE html>
<html lang="es" data-tema="oscuro">
<head>
    <!-- metadatos básicos o simples que van por defecto alv -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Lynx: Plataforma interactiva para aprender Lenguaje de Señas Mexicano (LSM)">
    <title>Lynx - Aprende Lenguaje de Señas Mexicano</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- header del sitio (menu y botones adicionales, para optimizar el trabajo) -->
    <?php include("zheader.php"); ?>

    <main>
        <!-- sección hero con video de fondo para que quede chidoris-->
        <section class="hero">
            <div class="contenedor-video">
                <video autoplay muted loop id="video-fondo">
                    <source src="img/masturbadores-de-aire.mp4" type="video/mp4"> <!-- el src para el videito -->
                    <!-- texto alternativo si el video no carga -->
                    Tu navegador no soporto 💅🏻.
                </video>
                
                <!-- overlay con contenido sobre el video -->
                <div class="overlay-video">
                    <div class="contenido-hero">
                        <h1 class="titulo-hero">Aprende Lenguaje de Señas Mexicano</h1>
                        <p class="subtitulo-hero">Descubre un mundo de comunicación sin barreras</p>
                        <?php if (isset($_SESSION['usuario_id'])): ?>
                        <a href="perfil.php" class="boton-primario boton-animado">ir a mi perfil</a>
                        <?php else: ?>
                        <a href="registro.php" class="boton-primario boton-animado">comenzar ahora</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- sección de introducción para nuevos -->
        <section class="seccion-intro">
            <div class="contenedor">
                <div class="texto-intro">
                    <h2 class="titulo-seccion">Lynx: Aprendizaje Interactivo de LSM</h2>
                    <p>Bienvenido a Lynx, una plataforma educativa diseñada para hacer el aprendizaje del Lenguaje de Señas Mexicano accesible, divertido y efectivo para todos.</p>
                    
                    <h3 class="subtitulo-seccion">¿Por qué aprender LSM?</h3>
                    <p>El Lenguaje de Señas Mexicano es el idioma principal de la comunidad sorda en México, utilizado por más de 87,000 personas. Aprender LSM te permite:</p>
                    <ul class="lista-beneficios">
                        <li><i class="fa-solid fa-check"></i> Comunicarte con personas sordas</li>
                        <li><i class="fa-solid fa-check"></i> Contribuir a una sociedad más inclusiva</li>
                        <li><i class="fa-solid fa-check"></i> Desarrollar nuevas habilidades cognitivas</li>
                        <li><i class="fa-solid fa-check"></i> Abrir oportunidades laborales</li>
                    </ul>
                </div>
                <div class="imagen-intro">
                    <!-- imagen ilustrativa por ahora -->
                    <img src="img/gato.jpg" class="imagen-decorativa" id="gato1">
                </div>
            </div>
        </section>

        <!-- sección de metodología pronto la gay-mer XD -->
        <section class="seccion-metodologia">
            <div class="contenedor">
                <div class="imagen-metodologia">
                    <img src="img/img-prueba.jpg" class="imagen-decorativa">
                </div>
                <div class="texto-metodologia">
                    <h2 class="titulo-seccion">Nuestra Metodología</h2>
                    <p>En Lynx creemos que el aprendizaje debe ser una experiencia interactiva y enriquecedora. Nuestra metodología se basa en tres pilares fundamentales:</p>
                    
                    <div class="pilar">
                        <h3><i class="fa-solid fa-gamepad"></i> Aprendizaje Lúdico</h3>
                        <p>Utilizamos juegos, actividades y desafíos que hacen del aprendizaje una experiencia divertida y motivadora.</p>
                    </div>
                    
                    <div class="pilar">
                        <h3><i class="fa-solid fa-hands"></i> Práctica Contextual</h3>
                        <p>Aprendemos haciendo. Nuestros ejercicios están diseñados para practicar el LSM en situaciones reales y cotidianas.</p>
                    </div>
                    
                    <div class="pilar">
                        <h3><i class="fa-solid fa-users"></i> Comunidad Participativa</h3>
                        <p>Creemos en el poder de la comunidad. Conectamos a estudiantes con personas sordas para una inmersión lingüística completa.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- sección de características del sitio -->
        <section class="seccion-caracteristicas">
            <div class="contenedor">
                <h2 class="titulo-seccion centrado">¿Qué nos hace diferentes?</h2>
                <p class="descripcion-seccion centrado">Lynx ofrece herramientas innovadoras para hacer tu aprendizaje efectivo y adaptado a tus necesidades.</p>
                
                <div class="contenedor-tarjetas">
                    <div class="tarjeta-caracteristica" data-animacion="fadeIn">
                        <div class="icono-caracteristica">🎮</div>
                        <h3>Aprendizaje Gamificado</h3>
                        <p>Aprende a través de juegos interactivos que te mantienen motivado y hacen que el proceso sea divertido y efectivo.</p>
                    </div>
                    
                    <div class="tarjeta-caracteristica" data-animacion="fadeIn" data-delay="200">
                        <div class="icono-caracteristica">🤖</div>
                        <h3>Traductor Inteligente</h3>
                        <p>Próximamente: tecnología de reconocimiento de gestos para practicar y recibir retroalimentación en tiempo real.</p>
                    </div>
                    
                    <div class="tarjeta-caracteristica" data-animacion="fadeIn" data-delay="400">
                        <div class="icono-caracteristica">👥</div>
                        <h3>Comunidad Activa</h3>
                        <p>Conéctate con otros estudiantes y miembros de la comunidad sorda para practicar y compartir experiencias.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- sección de testimonios (100% real no faik)-->
        <section class="seccion-testimonios">
            <div class="contenedor">
                <h2 class="titulo-seccion centrado">Lo que dicen nuestros usuarios</h2>
                
                <div class="carrusel-testimonios" id="carrusel-testimonios">
                    <div class="testimonio">
                        <p class="texto-testimonio">"Lorem ipsum dolor, sit amet consectetur adipisicing elit. Alias ad tenetur, adipisci sed quisquam ipsum saepe iste nostrum dignissimos deleniti, cumque, molestiae veniam maiores facilis aspernatur sint tempore."</p>
                        <p class="autor-testimonio">- Jesús Ramírez, Estudiante</p>
                    </div>
                    
                    <div class="testimonio">
                        <p class="texto-testimonio">"Lorem ipsum dolor sit, amet consectetur adipisicing elit. Repellendus consequuntur vel est nam possimus quidem modi voluptatibus odio amet ratione!"</p>
                        <p class="autor-testimonio">- Mariana López, Miembro de la comunidad sorda</p>
                    </div>
                    
                    <div class="testimonio">
                        <p class="texto-testimonio">"Lorem ipsum dolor sit amet consectetur adipisicing elit. Nobis mollitia nulla, quisquam perspiciatis rem minus laborum, reiciendis soluta voluptatibus quis quia ex obcaecati nihil optio ad, dolor saepe ipsam dolorem omnis tenetur. Saepe eaque sit fugit velit natus laborum corporis."</p>
                        <p class="autor-testimonio">- Daniel Ortiz, Profesor de LSM</p>
                    </div>
                </div>
                
                <!-- controles del carrusel -->
                <div class="controles-carrusel">
                    <button class="control-carrusel" id="anterior-testimonio" aria-label="Testimonio anterior">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="control-carrusel" id="siguiente-testimonio" aria-label="Testimonio siguiente">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- sección de llamado a la acción (cta, lo que significa que sirve para 
        que el usuario haga una accion como lo es registrarse)-->
        <section class="seccion-cta">
            <div class="contenedor">
                <h2 class="titulo-cta">Comienza tu viaje hacia la comunicación sin barreras</h2>
                <p class="descripcion-cta">Únete a nuestra comunidad de más de (indefinido) que están transformando la forma en que se comunican.</p>
                <a href="#" class="boton-primario boton-animado">Crear cuenta gratuita</a>
            </div>
        </section>
    </main>

    <!-- pie de página (footer) -->
    <?php include("zfooter.php"); ?>

    <!-- enlace al archivo JavaScript (Js pq Jason no quiero tocar (me da miedo ;-;)) -->
     <script src="js/script.js"></script>
</body>
</html>