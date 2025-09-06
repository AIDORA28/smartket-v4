import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot, hydrateRoot } from 'react-dom/client';
import { StrictMode } from 'react';

const appName = import.meta.env.VITE_APP_NAME || 'SmartKet v4';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.tsx`,
      import.meta.glob('./Pages/**/*.tsx'),
    ),
  setup({ el, App, props }) {
    if (import.meta.env.SSR) {
      hydrateRoot(el, <App {...props} />);
      return;
    }

    const root = createRoot(el);
    
    root.render(
      <StrictMode>
        <App {...props} />
      </StrictMode>
    );
  },
  progress: {
    color: '#4F46E5',
    showSpinner: true,
  },
});
