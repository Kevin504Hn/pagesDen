// Archivo: script.js
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.contact-form form');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            // Recolectar datos del formulario
            const formData = new FormData(form);
            
            // Enviar datos al servidor
            const response = await fetch('../php/formulario.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Mostrar popup de éxito
                showPopup('Gracias, hemos recibido tus datos, nos pondremos en contacto contigo pronto.');
                // Limpiar formulario
                form.reset();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            showPopup('Hubo un error al procesar tu solicitud. Por favor, intenta nuevamente.');
            console.error('Error:', error);
        }
    });
});

// Función para mostrar el popup
function showPopup(message) {
    // Crear elementos del popup
    const popup = document.createElement('div');
    popup.className = 'popup';
    
    const content = document.createElement('div');
    content.className = 'popup-content';
    
    const text = document.createElement('p');
    text.textContent = message;
    
    const button = document.createElement('button');
    button.textContent = 'Aceptar';
    button.onclick = () => popup.remove();
    
    // Ensamblar y mostrar el popup
    content.appendChild(text);
    content.appendChild(button);
    popup.appendChild(content);
    document.body.appendChild(popup);
}