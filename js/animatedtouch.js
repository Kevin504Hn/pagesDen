document.addEventListener('DOMContentLoaded', function() {
    // Seleccionar todos los elementos interactivos
    const touchElements = document.querySelectorAll('.card, .value-item');
    
    touchElements.forEach(element => {
        // Manejar inicio del touch
        element.addEventListener('touchstart', function(e) {
            // Prevenir comportamiento por defecto si es necesario
            e.preventDefault();
            this.classList.add('touch-active');
        }, { passive: false });

        // Manejar fin del touch
        element.addEventListener('touchend', function() {
            this.classList.remove('touch-active');
        });

        // Manejar cancelación del touch
        element.addEventListener('touchcancel', function() {
            this.classList.remove('touch-active');
        });

        // Manejar cuando el touch sale del elemento
        element.addEventListener('touchmove', function(e) {
            const touch = e.touches[0];
            const elementRect = this.getBoundingClientRect();
            
            // Verificar si el touch está fuera del elemento
            if (touch.clientX < elementRect.left || 
                touch.clientX > elementRect.right || 
                touch.clientY < elementRect.top || 
                touch.clientY > elementRect.bottom) {
                this.classList.remove('touch-active');
            }
        });
    });

    // Prevenir el scroll mientras se mantiene el touch en las cards
    document.addEventListener('touchmove', function(e) {
        if (e.target.closest('.card, .value-item')) {
            e.preventDefault();
        }
    }, { passive: false });
});