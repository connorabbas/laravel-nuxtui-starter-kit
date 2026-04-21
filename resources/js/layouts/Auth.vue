<script setup lang="ts">
import AppHead from '@/components/AppHead.vue'
import FlashAlerts from '@/components/FlashAlerts.vue'

const props = defineProps<{
    title: string
    description?: string
}>()
</script>

<template>
    <div>
        <AppHead
            :title="props.title"
            :description="props.description"
        />

        <div class="flex h-screen items-center justify-center px-4">
            <UButton
                icon="i-lucide-chevron-left"
                to="/"
                size="xl"
                color="neutral"
                variant="subtle"
                class="absolute top-8 left-8 z-10 rounded-full"
            />
            <UPageCard
                class="w-full max-w-md"
            >
                <FlashAlerts class="mb-4" />

                <slot />
            </UPageCard>
        </div>
    </div>
</template>
