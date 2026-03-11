import '../css/tailwind.css'
import '../css/app.css'

import { createInertiaApp, router } from '@inertiajs/vue3'
import { useToast } from '@nuxt/ui/composables'
import ui from '@nuxt/ui/vue-plugin'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createSSRApp, h } from 'vue'
import { Config, ZiggyVue } from 'ziggy-js'

import { createZiggyRoute, installZiggyRoute } from '@/integrations/ziggy-route-compat'
import type { AppPageProps, ErrorResponsePayload } from '@/types'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel Starter Template'

createInertiaApp({
    progress: { color: 'var(--ui-primary)' },
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const pageProps = props.initialPage.props as unknown as AppPageProps

        // Ziggy config
        const ziggyConfig = {
            ...(pageProps.ziggy as Config),
            location: new URL(pageProps.ziggy.location)
        }
        const route = createZiggyRoute(ziggyConfig)

        // Global Toast Error handling
        const toast = useToast()
        router.on('invalid', (event) => {
            const responseBody = event.detail.response?.data as Partial<ErrorResponsePayload> | undefined

            if (
                responseBody?.error_title
                && responseBody?.error_summary
                && responseBody?.error_detail
                && responseBody?.error_icon
                && responseBody?.error_color
                && responseBody?.status
            ) {
                event.preventDefault()

                toast.add({
                    color: responseBody.error_color,
                    title: responseBody.error_summary,
                    description: responseBody.error_detail,
                    icon: responseBody.error_icon
                })
            }
        })
        router.on('exception', (event) => {
            event.preventDefault()

            toast.add({
                color: 'error',
                title: 'Connection Error',
                description: 'An unexpected error occurred while loading this page. Please try again.',
                icon: 'i-lucide-wifi-off'
            })
        })

        const app = createSSRApp({ render: () => h(App, props) })
        app.use(plugin)
        app.use(ui)
        app.use(ZiggyVue, ziggyConfig)

        installZiggyRoute(app, route)

        app.mount(el)
    }
})
