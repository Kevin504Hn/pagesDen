document.addEventListener('DOMContentLoaded', () => {
    let currentIndex = 0;
    const container = document.querySelector('.carousel-container');
    const items = document.querySelectorAll('.carousel-item');
    const indicators = document.querySelectorAll('.indicator');
    const intervalTime = 3000; // Cambia el tiempo aquí (en milisegundos)

    function moveSlide(step) {
        currentIndex = (currentIndex + step + items.length) % items.length;
        container.style.transform = `translateX(-${currentIndex * 100}%)`;
        updateIndicators();
    }

    function updateIndicators() {
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === currentIndex);
        });
    }

    // Cambiar automáticamente la imagen después de un intervalo
    function autoSlide() {
        moveSlide(1); // Mueve a la siguiente imagen
    }

    // Configurar el intervalo para cambiar automáticamente
    let autoSlideInterval = setInterval(autoSlide, intervalTime);

    // Añadir eventos a los botones después de que el DOM esté listo
    document.querySelector('.prev').addEventListener('click', () => {
        clearInterval(autoSlideInterval); // Detener el cambio automático al hacer clic
        moveSlide(-1);
        // Reiniciar el intervalo
        autoSlideInterval = setInterval(autoSlide, intervalTime);
    });

    document.querySelector('.next').addEventListener('click', () => {
        clearInterval(autoSlideInterval); // Detener el cambio automático al hacer clic
        moveSlide(1);
        // Reiniciar el intervalo
        autoSlideInterval = setInterval(autoSlide, intervalTime);
    });

    // Configurar los eventos de clic en los indicadores
    indicators.forEach((indicator, index) => {
        indicator.addEventListener('click', () => {
            clearInterval(autoSlideInterval); // Detener el cambio automático al hacer clic
            currentIndex = index;
            container.style.transform = `translateX(-${currentIndex * 100}%)`;
            updateIndicators();
            // Reiniciar el intervalo
            autoSlideInterval = setInterval(autoSlide, intervalTime);
        });
    });

    // Inicializar los indicadores al cargar
    updateIndicators();
});
