document.addEventListener('DOMContentLoaded', function() {
    // Agregar confirmación a todos los botones de eliminar
    const deleteButtons = document.querySelectorAll('button[onclick^="return confirm"]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            if (!confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
                event.preventDefault();
            }
        });
    });

    // Validación de formularios
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    event.preventDefault();
                    alert('Por favor, completa todos los campos requeridos.');
                }
            });
        });
    });

    // Resaltar la opción de menú activa
    const currentPage = window.location.pathname.split('/').pop();
    const menuItems = document.querySelectorAll('nav a');
    menuItems.forEach(item => {
        if (item.getAttribute('href') === currentPage) {
            item.classList.add('active');
        }
    });
});