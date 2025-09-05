import './bootstrap';

// Importar Alpine pero NO iniciarlo automáticamente
import Alpine from 'alpinejs';
import Collapse from '@alpinejs/collapse';

// Register Alpine plugins
Alpine.plugin(Collapse);

// NO iniciar Alpine aquí - Livewire lo hará
window.Alpine = Alpine;

// Debug logs
document.addEventListener('DOMContentLoaded', function() {
    console.log('✅ DOM cargado');
    
    // Verificar si Livewire está disponible
    if (typeof window.Livewire !== 'undefined') {
        console.log('✅ Livewire ya está disponible');
        console.log('Livewire version:', window.Livewire?.version || 'No version info');
    } else {
        console.log('⚠️ Livewire NO está disponible aún');
        
        // Intentar detectar cuando se cargue
        const checkLivewire = setInterval(() => {
            if (typeof window.Livewire !== 'undefined') {
                console.log('✅ Livewire detectado:', window.Livewire);
                clearInterval(checkLivewire);
            }
        }, 100);
        
        // Limpiar después de 5 segundos
        setTimeout(() => {
            clearInterval(checkLivewire);
            if (typeof window.Livewire === 'undefined') {
                console.error('❌ Livewire NO se cargó después de 5 segundos');
            }
        }, 5000);
    }
});

document.addEventListener('livewire:init', () => {
    console.log('✅ Livewire inicializado correctamente');
    console.log('✅ Alpine disponible:', typeof window.Alpine !== 'undefined');
});

// Detectar eventos de Livewire
document.addEventListener('livewire:before', (e) => {
    console.log('📤 Livewire enviando:', e.detail);
});

document.addEventListener('livewire:after', (e) => {
    console.log('📥 Livewire recibido:', e.detail);
});
