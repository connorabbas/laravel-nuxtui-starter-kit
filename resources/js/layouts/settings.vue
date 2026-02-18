<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui'
import { computed } from 'vue'
import AppLayout from '@/layouts/app/index.vue'
import { useAppLayout } from '@/composables/useAppLayout'

const props = defineProps<{
    pageTitle?: string
    title: string
    description: string
}>()

const { currentRoute } = useAppLayout()

const items = computed<NavigationMenuItem[]>(() => [
    {
        label: 'Profile',
        icon: 'i-lucide-user-round',
        to: route('profile.edit'),
        active: currentRoute.value === 'profile.edit'
    },
    {
        label: 'Password',
        icon: 'i-lucide-key-round',
        to: route('user-password.edit'),
        active: currentRoute.value === 'user-password.edit'
    },
    {
        label: 'Two-Factor',
        icon: 'i-lucide-shield-check',
        to: route('two-factor.show'),
        active: currentRoute.value === 'two-factor.show'
    },
    {
        label: 'Appearance',
        icon: 'i-lucide-palette',
        to: route('appearance.edit'),
        active: currentRoute.value === 'appearance.edit'
    }
])
</script>

<template>
    <AppLayout
        :page-title="props.pageTitle"
    >
        <UPage>
            <!-- TODO: different layout where page header is on top -->
            <template #left>
                <UPageAside>
                    <UNavigationMenu
                        :items="items"
                        orientation="vertical"
                        variant="pill"
                    />
                </UPageAside>
            </template>

            <div class="lg:hidden">
                <UNavigationMenu
                    :items="items"
                    orientation="vertical"
                    variant="pill"
                    class="w-full"
                />
            </div>

            <UPageHeader
                :title="props.title"
                :description="props.description"
            />

            <UPageBody>
                <slot />
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
