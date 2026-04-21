import { router } from '@inertiajs/vue3'
import { useToast } from '@nuxt/ui/composables'

/**
 * @see https://inertiajs.com/docs/v3/advanced/events
 */
export function useInertiaRouterEvents() {
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
}
