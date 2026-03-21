<script setup lang="ts">
import FlashMessages from '@/components/FlashMessages.vue'
</script>

<template>
    <UApp>
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
                <FlashMessages class="mb-4" />

                <slot />
            </UPageCard>
        </div>
    </UApp>
</template>
