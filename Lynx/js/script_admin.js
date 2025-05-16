document.addEventListener('DOMContentLoaded', function() {
    // botón para mostrar/ocultar opciones de borrado
    const toggleBorrarBtn = document.getElementById('toggleBorrar');
    const opcionesBorrado = document.querySelectorAll('.opciones-desarrollador-mensaje');
    
    toggleBorrarBtn.addEventListener('click', function() {
        const estanVisibles = opcionesBorrado[0].style.display !== 'none';
        
        opcionesBorrado.forEach(opcion => {
            opcion.style.display = estanVisibles ? 'none' : 'flex';
        });
        
        toggleBorrarBtn.innerHTML = estanVisibles ? 
            '<i class="fas fa-trash-alt"></i> mostrar opciones de borrado' : 
            '<i class="fas fa-eye-slash"></i> ocultar opciones de borrado';
            
        toggleBorrarBtn.classList.toggle('activo');
    });
    
    // añadir funcionalidad para copiar el id del mensaje al hacer clic en el icono de info
    const infoSpans = document.querySelectorAll('.dev-info');
    infoSpans.forEach(span => {
        span.addEventListener('click', function() {
            const mensajeId = this.closest('.tarjeta-mensaje').getAttribute('data-mensaje-id');
            
            // crear un input temporal para copiar al portapapeles
            const tempInput = document.createElement('input');
            tempInput.value = mensajeId;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            // mostrar confirmación
            const confirmarCopia = document.createElement('div');
            confirmarCopia.className = 'mensaje-alerta-flotante exito';
            confirmarCopia.innerHTML = '<i class="fas fa-check-circle"></i> ID copiado al portapapeles';
            document.body.appendChild(confirmarCopia);
            
            // eliminar el mensaje después de 2 segundos
            setTimeout(() => {
                document.body.removeChild(confirmarCopia);
            }, 2000);
        });
    });
});