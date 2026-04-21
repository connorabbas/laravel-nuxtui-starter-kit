<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import type { AlertProps } from '@nuxt/ui'
import { computed, ref, watch } from 'vue'
import type { FlashProps } from '@/types/app'
import { flashPresentationByTone, resolveFlashTone } from '@/utils/flash'

interface FlashAlertItem {
    key: string
    color: NonNullable<AlertProps['color']>
    icon: string
    title: string
    message: string
}

const page = usePage()
const dismissedAlertKeys = ref<string[]>([])

const visibleAlerts = computed<FlashAlertItem[]>(() => {
    const flashEntries = Object.entries(page.flash as FlashProps)

    return flashEntries.reduce<FlashAlertItem[]>((alerts, [key, value]) => {
        if (
            key.endsWith('_alert')
            && typeof value === 'string'
            && value.trim() !== ''
            && !dismissedAlertKeys.value.includes(key)
        ) {
            const tone = resolveFlashTone(key)
            const presentation = flashPresentationByTone[tone]

            alerts.push({
                key,
                color: presentation.color,
                icon: presentation.icon,
                title: presentation.title,
                message: value.trim()
            })
        }

        return alerts
    }, [])
})

function handleAlertOpenChange(key: string, isOpen: boolean): void {
    if (!isOpen && !dismissedAlertKeys.value.includes(key)) {
        dismissedAlertKeys.value.push(key)
    }
}

watch(
    () => page.flash,
    () => {
        dismissedAlertKeys.value = []
    },
    { deep: true }
)
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
            :color="alert.color"
            :icon="alert.icon"
            variant="subtle"
            close
            @update:open="handleAlertOpenChange(alert.key, $event)"
        />
    </div>
</template>
