import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { i18n, setLocale } from './i18n';
import Vue3Toastify from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const appName = import.meta.env.VITE_APP_NAME || 'ContiStock';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(Vue3Toastify, {
                position: 'top-right',
                autoClose: 3000,
                closeOnClick: true,
            })
            .use(ZiggyVue);

        const page = props.initialPage.props;
        setLocale(page.locale || 'en');

        return app.mount(el);
    },
    progress: {
        color: '#0F2A44',
    },
});
