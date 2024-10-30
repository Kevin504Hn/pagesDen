document.addEventListener('DOMContentLoaded', function() {
    const touchElements = document.querySelectorAll('.card, .value-item');
    let activeElement = null;

    // Manejador para clicks/toques fuera de las tarjetas
    document.addEventListener('touchstart', function(e) {
        if (!e.target.closest('.card, .value-item')) {
            // Si hay un elemento activo, remover su clase
            if (activeElement) {
                activeElement.classList.remove('touch-active');
                activeElement = null;
            }
        }
    });

    touchElements.forEach(element => {
        element.addEventListener('touchstart', function(e) {
            // Ya no prevenimos el comportamiento por defecto
            // e.preventDefault();
            
            // Remover clase activa del elemento anterior si existe
            if (activeElement && activeElement !== this) {
                activeElement.classList.remove('touch-active');
            }
            
            this.classList.add('touch-active');
            activeElement = this;
        });

        element.addEventListener('touchend', function(e) {
            // No removemos la clase aquí para mantener el estado activo
            // hasta que se toque en otro lugar
        });

        element.addEventListener('touchcancel', function() {
            this.classList.remove('touch-active');
            activeElement = null;
        });

        element.addEventListener('touchmove', function(e) {
            const touch = e.touches[0];
            const elementRect = this.getBoundingClientRect();
            
            if (touch.clientX < elementRect.left || 
                touch.clientX > elementRect.right || 
                touch.clientY < elementRect.top || 
                touch.clientY > elementRect.bottom) {
                this.classList.remove('touch-active');
                activeElement = null;
            }
        });
    });

    // Eliminamos el preventDefault del touchmove para permitir el scroll
    // document.addEventListener('touchmove', function(e) {
    //     if (e.target.closest('.card, .value-item')) {
    //         e.preventDefault();
    //     }
    // }, { passive: false });
});