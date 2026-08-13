<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui'
import AppHead from '@/components/AppHead.vue'
import AppLayout from './Header.vue'

const props = defineProps<{
    title: string
    description?: string
    subPageNavItems?: NavigationMenuItem[]
}>()
</script>

<template>
    <div>
        <AppHead
            :title="props.title"
            :description="props.description"
        />

        <AppLayout
            :page-title="props.title"
            :sub-page-nav-items="props.subPageNavItems"
        >
            <!-- passthrough actions slot for sidebar layout -->
            <template #actions>
                <slot name="actions" />
            </template>
            <slot />
        </AppLayout>
    </div>
</template>
