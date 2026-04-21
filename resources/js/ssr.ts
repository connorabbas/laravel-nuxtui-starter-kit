import { createInertiaApp } from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import ui from '@nuxt/ui/vue-plugin'
import { createHead, renderSSRHead } from '@unhead/vue/server'
import { renderToString } from '@vue/server-renderer'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { createSSRApp, DefineComponent, h } from 'vue'
import AppRoot from '@/components/AppRoot.vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel Starter Template'

createServer((page) => {
    const head = createHead()
    return createInertiaApp({
        page,
        render: renderToString,
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
        setup({ App, props, plugin }) {
            return createSSRApp({
                render: () => h(AppRoot, {}, {
                    default: () => h(App, props)
                })
            })
                .use(plugin)
                .use(head)
                .use(ui)
        }
    }).then(async (app) => {
        const payload = await renderSSRHead(head)
        app.head.push(payload.headTags)
        return app
    })
})
