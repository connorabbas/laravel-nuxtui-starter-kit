<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import { useAppLayout } from '@/composables/useAppLayout'

const { appName, subPageNavItems, navMenuItems, userMenuItems, user } = useAppLayout()

const pageTitle = 'TEST'

const sidebarOpen = ref(false)

const groups = computed(() => [
    {
        id: 'links',
        label: 'Go to',
        items: navMenuItems.value
            .flat()
            .filter((item) => item.label && item.to)
            .map((item) => ({
                label: item.label,
                icon: item.icon,
                to: item.to,
                target: item.target
            }))
    }
])
</script>

<template>
    <UDashboardGroup unit="rem">
        <UDashboardSidebar
            id="default"
            v-model:open="sidebarOpen"
            collapsible
            resizable
            class="bg-elevated/25"
            :ui="{ footer: 'lg:border-t lg:border-default' }"
        >
            <template #header="{ collapsed }">
                <div class="flex w-full justify-center">
                    <Link
                        :href="route('index')"
                        class="flex items-center gap-3"
                    >
                        <span
                            class="bg-primary/15 text-primary inline-flex h-8 w-8 items-center justify-center rounded-md font-semibold"
                        >A</span>
                        <span
                            v-if="!collapsed"
                            class="font-medium"
                        >{{ appName }}</span>
                    </Link>
                </div>
            </template>

            <template #default="{ collapsed }">
                <UDashboardSearchButton
                    :collapsed="collapsed"
                    class="ring-default bg-transparent"
                />

                <UNavigationMenu
                    :collapsed="collapsed"
                    :items="navMenuItems[0]"
                    orientation="vertical"
                    tooltip
                    popover
                />

                <UNavigationMenu
                    :collapsed="collapsed"
                    :items="navMenuItems[1]"
                    orientation="vertical"
                    tooltip
                    class="mt-auto"
                />

                <UColorModeButton v-if="collapsed" />
                <UColorModeSelect v-else />
            </template>

            <template #footer="{ collapsed }">
                <UDropdownMenu
                    v-if="user"
                    :items="userMenuItems"
                    :content="{ align: 'center', collisionPadding: 12 }"
                    :ui="{ content: collapsed ? 'w-48' : 'w-(--reka-dropdown-menu-trigger-width)' }"
                >
                    <UButton
                        :label="collapsed ? undefined : user.name"
                        class="data-[state=open]:bg-elevated"
                        color="neutral"
                        variant="ghost"
                        block
                        :icon="collapsed ? 'i-lucide-user' : undefined"
                        :square="collapsed"
                        trailing-icon="i-lucide-chevrons-up-down"
                        :ui="{ trailingIcon: 'text-dimmed' }"
                    />
                </UDropdownMenu>
            </template>
        </UDashboardSidebar>

        <UDashboardSearch :groups="groups" />

        <UDashboardPanel>
            <template #header>
                <!-- TODO: Get page title from meta tag? -->
                <UDashboardNavbar :title="pageTitle">
                    <template #leading>
                        <UDashboardSidebarCollapse />
                    </template>
                </UDashboardNavbar>

                <UDashboardToolbar v-if="subPageNavItems">
                    <UNavigationMenu
                        :items="subPageNavItems"
                        highlight
                        class="-mx-1 flex-1"
                    />
                </UDashboardToolbar>
            </template>

            <template #body>
                <slot />
            </template>
        </UDashboardPanel>
    </UDashboardGroup>
</template>
