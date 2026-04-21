import { router } from '@inertiajs/vue3'
import { useToast } from '@nuxt/ui/composables'
import type { FlashProps } from '@/types/app'
import { flashPresentationByTone, resolveFlashTone } from '@/utils/flash'

/**
 * @see https://inertiajs.com/docs/v3/advanced/events
 */
export function useInertiaRouterEvents() {
    const toast = useToast()

    router.on('flash', (event) => {
        const flashEntries = Object.entries(event.detail.flash as FlashProps)

        flashEntries.forEach(([key, value]) => {
            if (!key.endsWith('_toast') || typeof value !== 'string' || value.trim() === '') {
                return
            }

            const tone = resolveFlashTone(key)
            const presentation = flashPresentationByTone[tone]

            toast.add({
                color: presentation.color,
                title: presentation.title,
                description: value.trim(),
                icon: presentation.icon
            })
        })
    })

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
