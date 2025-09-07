import './bootstrap';

// Importar Alpine para uso con React/Inertia
import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';

// Register Alpine plugins
Alpine.plugin(Collapse);

// Configurar Alpine.js para la aplicación React + Inertia.js
window.Alpine = Alpine;

// Inicializar Alpine después del DOM
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
    console.log('✅ Alpine.js inicializado correctamente para React + Inertia.js');
});
