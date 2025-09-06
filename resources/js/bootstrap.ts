/**
 * Bootstrap file for SmartKet v4
 * Configuración global de la aplicación
 */

// Configuración de Axios para requests HTTP
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Token CSRF para formularios
const token = document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Configuración global de tipos para Window
declare global {
  interface Window {
    axios: typeof axios;
  }
}
