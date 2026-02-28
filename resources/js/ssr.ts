import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import ui from '@nuxt/ui/vue-plugin'
import { createHead, renderSSRHead } from '@unhead/vue/server'
import { renderToString } from '@vue/server-renderer'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createSSRApp, DefineComponent, h } from 'vue'
import { Config } from 'ziggy-js'
import { createZiggyRoute, installZiggyRoute } from '@/integrations/ziggy-route-compat'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel Starter Template'

createServer((page) => {
    const head = createHead()
    return createInertiaApp({
        page,
        render: renderToString,
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
        setup({ App, props, plugin }) {
            const app = createSSRApp({ render: () => h(App, props) })

            // Configure Ziggy for SSR...
            const ziggyConfig = {
                ...(page.props.ziggy as Config),
                location: new URL((page.props.ziggy as { location: string }).location)
            }

            const route = createZiggyRoute(ziggyConfig)
            installZiggyRoute(app, route)

            app.use(head)
            app.use(plugin)
            app.use(ui)

            return app
        }
    }).then(async (app) => {
        const payload = await renderSSRHead(head)
        app.head.push(payload.headTags)
        return app
    })
})
