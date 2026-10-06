<script setup lang="ts">
import { Head as IHead } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = withDefaults(defineProps<{
    title: string,
    description?: string,
}>(), {
    description: 'A Laravel starter kit with Inertia, Vue, TypeScript and Nuxt UI.'
})

const appName = import.meta.env.VITE_APP_NAME || 'Laravel Nuxt UI Starter'

const openGraphTitle = computed(() => props.title || appName)
</script>

<template>
    <IHead :title="props.title">
        <meta
            head-key="og:title"
            property="og:title"
            :content="openGraphTitle"
        >
        <meta
            head-key="description"
            name="description"
            :content="props.description"
        >
        <meta
            head-key="og:description"
            property="og:description"
            :content="props.description"
        >
        <slot />
    </IHead>
</template>
