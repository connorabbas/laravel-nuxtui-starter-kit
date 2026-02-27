<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3'
import type { AlertProps } from '@nuxt/ui'
import { computed, onMounted, onUnmounted, reactive } from 'vue'

interface FlashAlertConfig {
    icon: string
    title: string
}

const flashConfig = {
    success: {
        icon: 'i-lucide-circle-check',
        title: 'Success'
    },
    info: {
        icon: 'i-lucide-info',
        title: 'Info'
    },
    warning: {
        icon: 'i-lucide-triangle-alert',
        title: 'Warning'
    },
    error: {
        icon: 'i-lucide-circle-x',
        title: 'Error'
    },
    neutral: {
        icon: 'i-lucide-megaphone',
        title: 'Notice'
    }
} as const satisfies Partial<Record<NonNullable<AlertProps['color']>, FlashAlertConfig>>

type FlashColor = keyof typeof flashConfig

interface FlashAlertItem extends FlashAlertConfig {
    key: FlashColor
    message: string
}

const flashKeys = Object.keys(flashConfig) as FlashColor[]

const page = usePage()

const dismissed = reactive<Record<FlashColor, boolean>>({
    success: false,
    info: false,
    warning: false,
    error: false,
    neutral: false
})

const flash = computed(() => page.props.flash)

const visibleAlerts = computed<FlashAlertItem[]>(() => {
    return flashKeys.reduce<FlashAlertItem[]>((alerts, key) => {
        const message = flash.value?.[key]

        if (!message || dismissed[key]) {
            return alerts
        }

        alerts.push({
            key,
            message,
            ...flashConfig[key]
        })

        return alerts
    }, [])
})

const handleAlertOpenChange = (key: FlashColor, isOpen: boolean): void => {
    if (!isOpen) {
        dismissed[key] = true
    }
}

let removeSuccessListener: (() => void) | null = null

onMounted(() => {
    removeSuccessListener = router.on('success', () => {
        flashKeys.forEach((key) => {
            if (page.props.flash?.[key]) {
                dismissed[key] = false
            }
        })
    })
})

onUnmounted(() => {
    removeSuccessListener?.()
    removeSuccessListener = null
})
</script>

<template>
    <div
        v-if="visibleAlerts.length > 0"
        class="space-y-3"
    >
        <UAlert
            v-for="alert in visibleAlerts"
            :key="alert.key"
            :title="alert.title"
            :description="alert.message"
            :color="alert.key"
            :icon="alert.icon"
            variant="subtle"
            close
            @update:open="(isOpen) => handleAlertOpenChange(alert.key, isOpen)"
        />
    </div>
</template>
