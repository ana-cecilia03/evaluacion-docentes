// ====== Importar estilos globales ======
import '../css/global.css';

// ====== Iconos ======
import '@fortawesome/fontawesome-free/css/all.min.css';
// Si usas funciones JS de Font Awesome, descomenta la siguiente línea
// import '@fortawesome/fontawesome-free/js/all.js'

// ====== Dependencias de Inertia y Vue ======
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

// ====== Configuración general ======
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue')
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// ====== Inicializar tema (claro/oscuro) ======
initializeTheme();
