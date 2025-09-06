// SmartKet Fast Navigation System
class SmartKetRouter {
    constructor() {
        this.currentModule = null;
        this.cache = new Map();
        this.loading = false;
        this.initializeNavigation();
    }

    initializeNavigation() {
        // Interceptar clicks en el sidebar
        document.addEventListener('click', (e) => {
            const link = e.target.closest('[data-fast-nav]');
            if (link && !this.loading) {
                e.preventDefault();
                this.navigateTo(link.href, link.dataset.module);
            }
        });

        // Precargar módulos frecuentes
        this.preloadFrequentModules();
    }

    async navigateTo(url, moduleName) {
        if (this.currentModule === moduleName) return;

        this.showLoadingIndicator();
        this.loading = true;

        try {
            let content;
            
            // Verificar cache primero
            if (this.cache.has(url)) {
                content = this.cache.get(url);
            } else {
                // Fetch con timeout rápido
                const response = await this.fetchWithTimeout(url, 2000);
                content = await response.text();
                
                // Cachear solo módulos principales
                if (this.isMainModule(moduleName)) {
                    this.cache.set(url, content);
                }
            }

            // Actualizar DOM
            this.updateMainContent(content);
            this.updateURL(url);
            this.updateActiveNav(moduleName);
            this.currentModule = moduleName;

        } catch (error) {
            console.error('Navigation error:', error);
            // Fallback a navegación tradicional
            window.location.href = url;
        } finally {
            this.hideLoadingIndicator();
            this.loading = false;
        }
    }

    async fetchWithTimeout(url, timeout) {
        return Promise.race([
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-Fast-Nav': 'true'
                }
            }),
            new Promise((_, reject) => 
                setTimeout(() => reject(new Error('Timeout')), timeout)
            )
        ]);
    }

    updateMainContent(html) {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector('main') || doc.querySelector('[data-main-content]');
        
        if (newContent) {
            const mainContainer = document.querySelector('main') || document.querySelector('[data-main-content]');
            if (mainContainer) {
                // Animación suave
                mainContainer.style.opacity = '0.5';
                
                setTimeout(() => {
                    mainContainer.innerHTML = newContent.innerHTML;
                    mainContainer.style.opacity = '1';
                    
                    // Reinicializar Alpine.js si está presente
                    if (window.Alpine) {
                        Alpine.initTree(mainContainer);
                    }
                }, 100);
            }
        }
    }

    updateURL(url) {
        history.pushState({ fastNav: true }, '', url);
    }

    updateActiveNav(moduleName) {
        // Remover active de todos
        document.querySelectorAll('[data-fast-nav]').forEach(link => {
            link.classList.remove('bg-blue-700', 'text-white');
            link.classList.add('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
        });

        // Agregar active al actual
        const activeLink = document.querySelector(`[data-module="${moduleName}"]`);
        if (activeLink) {
            activeLink.classList.remove('text-gray-300', 'hover:bg-gray-700', 'hover:text-white');
            activeLink.classList.add('bg-blue-700', 'text-white');
        }
    }

    showLoadingIndicator() {
        let loader = document.getElementById('fast-nav-loader');
        if (!loader) {
            loader = document.createElement('div');
            loader.id = 'fast-nav-loader';
            loader.innerHTML = `
                <div class="fixed top-0 left-0 w-full h-1 bg-blue-600 animate-pulse z-50"></div>
            `;
            document.body.appendChild(loader);
        }
        loader.style.display = 'block';
    }

    hideLoadingIndicator() {
        const loader = document.getElementById('fast-nav-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    preloadFrequentModules() {
        const frequentUrls = [
            '/dashboard',
            '/productos', 
            '/pos',
            '/clientes',
            '/inventario'
        ];

        // Precargar después de 2 segundos
        setTimeout(() => {
            frequentUrls.forEach(url => {
                if (!this.cache.has(url)) {
                    this.preloadModule(url);
                }
            });
        }, 2000);
    }

    async preloadModule(url) {
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const content = await response.text();
            this.cache.set(url, content);
        } catch (error) {
            // Silenciar errores de precarga
        }
    }

    isMainModule(moduleName) {
        const mainModules = ['dashboard', 'productos', 'pos', 'clientes', 'inventario', 'reportes'];
        return mainModules.includes(moduleName);
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    window.smartKetRouter = new SmartKetRouter();
});

// Manejar botón atrás
window.addEventListener('popstate', (e) => {
    if (e.state && e.state.fastNav) {
        location.reload(); // Por simplicidad, recargar en navegación atrás
    }
});
