document.addEventListener('DOMContentLoaded', () => {
    let currentIndex = 0;

    function moveSlide(step) {
        const container = document.querySelector('.carousel-container');
        const items = document.querySelectorAll('.carousel-item');
        currentIndex = (currentIndex + step + items.length) % items.length;
        container.style.transform = `translateX(-${currentIndex * 100}%)`;
    }

    // Añadir eventos a los botones después de que el DOM esté listo
    document.querySelector('.prev').addEventListener('click', () => moveSlide(-1));
    document.querySelector('.next').addEventListener('click', () => moveSlide(1));
});
