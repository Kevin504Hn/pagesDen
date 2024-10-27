document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const navDiv = document.querySelector('.nav-div');

    // Solo mantener la funcionalidad del menú hamburguesa
    menuToggle.addEventListener('click', function() {
        menuToggle.classList.toggle('active');
        navDiv.classList.toggle('active');
    });

    // Cerrar menú al hacer clic en un enlace
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            menuToggle.classList.remove('active');
            navDiv.classList.remove('active');
        });
    });
});