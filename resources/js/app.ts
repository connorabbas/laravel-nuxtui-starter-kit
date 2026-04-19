import '../css/tailwind.css'
import '../css/app.css'

import { createInertiaApp, router } from '@inertiajs/vue3'
import { useToast } from '@nuxt/ui/composables'
import ui from '@nuxt/ui/vue-plugin'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import type { DefineComponent } from 'vue'
import { createSSRApp, h } from 'vue'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Global Toast Error handling
        const toast = useToast()
        router.on('httpException', (event) => {
            const responseBody = event.detail.response?.data as Partial<App.Data.ErrorToastResponseData> | undefined

            if (
                responseBody?.status
                && responseBody?.errorSummary
                && responseBody?.errorDetail
                && responseBody?.errorIcon
            ) {
                event.preventDefault()

                toast.add({
                    color: responseBody.status >= 500 ? 'error' : 'warning',
                    title: responseBody.errorSummary,
                    description: responseBody.errorDetail,
                    icon: responseBody.errorIcon
                })
            }
        })
        router.on('networkError', (event) => {
            event.preventDefault()

            toast.add({
                color: 'error',
                title: 'Connection Error',
                description: 'An unexpected error occurred while loading this page. Please try again.',
                icon: 'i-lucide-wifi-off'
            })
        })

        createSSRApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ui)
            .mount(el)
    },
    progress: { color: 'var(--ui-primary)' }
})
