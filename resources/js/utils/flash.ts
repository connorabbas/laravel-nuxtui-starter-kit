export type FlashTone = 'success' | 'info' | 'warning' | 'error' | 'neutral'

interface FlashPresentation {
    color: FlashTone
    icon: string
    title: string
}

export const flashPresentationByTone: Record<FlashTone, FlashPresentation> = {
    success: {
        color: 'success',
        icon: 'i-lucide-circle-check',
        title: 'Success'
    },
    info: {
        color: 'info',
        icon: 'i-lucide-info',
        title: 'Info'
    },
    warning: {
        color: 'warning',
        icon: 'i-lucide-triangle-alert',
        title: 'Warning'
    },
    error: {
        color: 'error',
        icon: 'i-lucide-circle-x',
        title: 'Error'
    },
    neutral: {
        color: 'neutral',
        icon: 'i-lucide-megaphone',
        title: 'Notice'
    }
}

export function resolveFlashTone(key: string): FlashTone {
    const [prefix] = key.split('_')

    if (prefix === 'success') {
        return 'success'
    }
    if (prefix === 'info') {
        return 'info'
    }
    if (prefix === 'warning' || prefix === 'warn') {
        return 'warning'
    }
    if (prefix === 'error') {
        return 'error'
    }

    return 'neutral'
}
