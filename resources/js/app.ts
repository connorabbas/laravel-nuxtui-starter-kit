import '../css/tailwind.css'
import '../css/app.css'

import { createInertiaApp } from '@inertiajs/vue3'
import ui from '@nuxt/ui/vue-plugin'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createApp, h } from 'vue'
import { Config, ZiggyVue } from 'ziggy-js'

import { createZiggyRoute, installZiggyRoute } from '@/integrations/ziggy-route-compat'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel Starter Template'

createInertiaApp({
    progress: { color: 'var(--ui-primary)' },
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const ziggyConfig = {
            ...(props.initialPage.props.ziggy as Config),
            location: new URL((props.initialPage.props.ziggy as { location: string }).location)
        }
        const route = createZiggyRoute(ziggyConfig)

        const app = createApp({ render: () => h(App, props) })

        app.use(plugin)
        app.use(ui)
        app.use(ZiggyVue, ziggyConfig)
        installZiggyRoute(app, route)

        app.mount(el)
    }
})
