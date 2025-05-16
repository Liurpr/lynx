document.addEventListener('DOMContentLoaded', function() {
    // Animación al hacer scroll
    const versions = document.querySelectorAll('.changelog-version');
    
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -100px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    versions.forEach(version => {
        version.style.opacity = 0;
        version.style.transform = 'translateY(30px)';
        version.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(version);
    });
    
    // Sistema de filtrado
    const filterButtons = document.querySelectorAll('.filter-button');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remover clase active de todos los botones
            filterButtons.forEach(btn => btn.classList.remove('active'));
            
            // Añadir clase active al botón clicado
            button.classList.add('active');
            
            // Obtener el filtro seleccionado
            const filter = button.getAttribute('data-filter');
            
            // Filtrar las categorías
            const categories = document.querySelectorAll('.change-category');
            
            if (filter === 'all') {
                // Mostrar todas las versiones
                versions.forEach(version => {
                    version.style.display = 'block';
                });
                
                // Mostrar todas las categorías
                categories.forEach(category => {
                    category.style.display = 'block';
                });
            } else {
                // Mostrar todas las versiones primero
                versions.forEach(version => {
                    version.style.display = 'block';
                });
                
                // Ocultar categorías que no coinciden con el filtro
                categories.forEach(category => {
                    if (category.getAttribute('data-category') === filter) {
                        category.style.display = 'block';
                    } else {
                        category.style.display = 'none';
                    }
                });
                
                // Ocultar versiones que no tienen la categoría visible
                versions.forEach(version => {
                    const hasVisibleCategory = version.querySelector(`.change-category[data-category="${filter}"]`);
                    if (!hasVisibleCategory) {
                        version.style.display = 'none';
                    }
                });
            }
        });
    });
    
    // Smooth scroll para los enlaces de navegación
    document.querySelectorAll('.version-link').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                // Calcular la posición con un pequeño offset
                const yOffset = -20; 
                const y = targetElement.getBoundingClientRect().top + window.pageYOffset + yOffset;
                
                window.scrollTo({
                    top: y,
                    behavior: 'smooth'
                });
                
                // Añadir efecto de resaltado temporal
                targetElement.style.transition = 'box-shadow 0.3s ease';
                targetElement.style.boxShadow = '0 0 0 3px var(--color-primario)';
                
                setTimeout(() => {
                    targetElement.style.boxShadow = '';
                }, 1500);
            }
        });
    });
    
    // Actualizar estadísticas con contador animado
    const stats = document.querySelectorAll('.stat-number');
    
    stats.forEach(stat => {
        const target = parseInt(stat.textContent);
        const duration = 1500; // ms
        const increment = target / (duration / 16); // 60fps
        
        let current = 0;
        
        const updateCounter = () => {
            current += increment;
            
            if (current < target) {
                stat.textContent = Math.ceil(current);
                requestAnimationFrame(updateCounter);
            } else {
                stat.textContent = target;
            }
        };
        
        stat.textContent = '0';
        
        // Iniciar la animación cuando sea visible
        const statObserver = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                updateCounter();
                statObserver.disconnect();
            }
        }, { threshold: 0.5 });
        
        statObserver.observe(stat);
    });
});