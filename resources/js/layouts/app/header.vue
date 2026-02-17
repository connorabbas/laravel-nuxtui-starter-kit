<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import { useAppLayout } from '@/composables/useAppLayout'

const { appName, navMenuItems, subPageNavItems, userMenuItems, user } = useAppLayout()
</script>

<template>
    <div>
        <UHeader>
            <template #left>
                <Link
                    :href="route('index')"
                    class="font-semibold"
                >
                    {{ appName }}
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
            v-if="subPageNavItems"
            class="border-default bg-default/75 sticky top-(--ui-header-height) z-40 w-full border-b backdrop-blur"
        >
            <UContainer>
                <UNavigationMenu
                    :items="subPageNavItems"
                    class="-mx-2.5 w-full"
                    variant="pill"
                    highlight
                    :ui="{ list: 'min-w-auto overflow-auto', item: 'min-w-auto' }"
                />
            </UContainer>
        </div>

        <UMain>
            <div class="py-4 sm:py-6">
                <slot />
            </div>
        </UMain>
    </div>
</template>
