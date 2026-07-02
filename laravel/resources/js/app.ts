import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import type { DefineComponent } from 'vue';

const appName = import.meta.env.VITE_APP_NAME || 'PESAT';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob<DefineComponent>('./pages/**/*.vue', {
            eager: true,
        });

        return pages[`./pages/${name}.vue` as string];
    },
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) })
            .use(plugin);

        const broadcastConnection = document.head.querySelector('meta[name="broadcast-connection"]')?.getAttribute('content');
        if (broadcastConnection === 'reverb') {
            import('./echo').then(({ default: setupEcho }) => setupEcho(vueApp));
        }

        vueApp.mount(el);
    },
    progress: {
        color: '#2563eb',
    },
});
