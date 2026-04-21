import '../css/tailwind.css'
import '../css/app.css'

import { createInertiaApp } from '@inertiajs/vue3'
import ui from '@nuxt/ui/vue-plugin'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createSSRApp, h } from 'vue'
import { useInertiaRouterEvents } from '@/composables/useInertiaRouterEvents'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        useInertiaRouterEvents()
        createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ui)
            .mount(el)
    },
    progress: { color: 'var(--ui-primary)' }
})
