<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui'
import { computed } from 'vue'
import AppLayout from '@/layouts/app/Index.vue'
import { useAppLayout } from '@/composables/useAppLayout'

const props = defineProps<{
    pageTitle?: string
    title: string
    description: string
}>()

const { currentRoute } = useAppLayout()

const settingsPageTitle = computed(() => `Settings - ${props.pageTitle}`)

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
        to: route('settings.password.edit'),
        active: currentRoute.value === 'settings.password.edit'
    },
    {
        label: 'Two-Factor Auth',
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
    <AppLayout :page-title="props.pageTitle">
        <UPageHeader
            :title="settingsPageTitle"
            :description="props.description"
        />

        <UPage>
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
                    class="w-full mt-6"
                />
            </div>

            <UPageBody>
                <slot />
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
