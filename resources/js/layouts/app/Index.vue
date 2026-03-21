<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui'
import AppLayout from './Header.vue'

const props = defineProps<{
    pageTitle?: string
    subPageNavItems?: NavigationMenuItem[]
}>()
</script>

<template>
    <UApp>
        <AppLayout
            :page-title="props.pageTitle"
            :sub-page-nav-items="props.subPageNavItems"
        >
            <slot />
        </AppLayout>
    </UApp>
</template>
