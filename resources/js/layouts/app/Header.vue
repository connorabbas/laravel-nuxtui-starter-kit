<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'

import { useAppLayout } from '@/composables/useAppLayout'
import AppLogo from '@/components/AppLogo.vue'
import FlashMessages from '@/components/FlashMessages.vue'

const props = defineProps<{
    pageTitle?: string
    subPageNavItems?: NavigationMenuItem[]
}>()

const { navMenuItems, subPageNavItems: defaultSubPageNavItems, userMenuItems, user } = useAppLayout()

const resolvedSubPageNavItems = computed(() => props.subPageNavItems ?? defaultSubPageNavItems.value)
</script>

<template>
    <div>
        <UHeader>
            <template #left>
                <Link
                    :href="route('index')"
                    aria-label="Application logo"
                >
                    <AppLogo class="h-6 w-auto shrink-0" />
                </Link>
            </template>

            <UNavigationMenu
                :items="navMenuItems.flat()"
                variant="link"
            />

            <template #right>
                <UDropdownMenu
                    v-if="user"
                    :items="userMenuItems"
                    :content="{ align: 'end', side: 'bottom' }"
                    :ui="{ content: 'w-48' }"
                >
                    <UButton
                        trailing-icon="i-lucide-chevron-down"
                        color="neutral"
                        variant="ghost"
                        :label="user.name"
                    />
                </UDropdownMenu>
            </template>

            <template #body>
                <UNavigationMenu
                    :items="navMenuItems.flat()"
                    orientation="vertical"
                    class="-mx-2.5"
                />
            </template>
        </UHeader>

        <div
            v-if="resolvedSubPageNavItems"
            class="border-default bg-default/75 sticky top-(--ui-header-height) z-40 w-full border-b backdrop-blur"
        >
            <UContainer>
                <UNavigationMenu
                    v-if="resolvedSubPageNavItems"
                    :items="resolvedSubPageNavItems"
                    class="-mx-2.5 w-full"
                    variant="pill"
                    highlight
                    :ui="{ list: 'min-w-auto overflow-auto', item: 'min-w-auto' }"
                />
            </UContainer>
        </div>

        <UMain>
            <UContainer class="w-full">
                <FlashMessages class="mt-6" />

                <slot />
            </UContainer>
        </UMain>
    </div>
</template>
