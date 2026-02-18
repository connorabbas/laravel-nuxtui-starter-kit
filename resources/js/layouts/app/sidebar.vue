<script setup lang="ts">
import type { NavigationMenuItem } from '@nuxt/ui'
import { Link } from '@inertiajs/vue3'
import { computed } from 'vue'
import { useAppLayout } from '@/composables/useAppLayout'
import { useSsrStorage } from '@/composables/useSsrStorage'

const props = defineProps<{
    pageTitle?: string
    subPageNavItems?: NavigationMenuItem[]
}>()

const { appName, subPageNavItems: defaultSubPageNavItems, navMenuItems, userMenuItems, user } = useAppLayout()

const pageTitle = computed(() => props.pageTitle ?? 'Application')
const resolvedSubPageNavItems = computed(() => props.subPageNavItems ?? defaultSubPageNavItems.value)

const sidebarCollapsed = useSsrStorage('sidebar-collapsed', false)

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
            v-model:collapsed="sidebarCollapsed"
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
                <UDashboardNavbar :title="pageTitle">
                    <template #leading>
                        <UDashboardSidebarCollapse />
                    </template>
                </UDashboardNavbar>

                <UDashboardToolbar v-if="resolvedSubPageNavItems">
                    <div class="flex w-full flex-col gap-3 md:flex-row md:items-center">
                        <UNavigationMenu
                            v-if="resolvedSubPageNavItems"
                            :items="resolvedSubPageNavItems"
                            highlight
                            class="-mx-1 flex-1"
                        />
                    </div>
                </UDashboardToolbar>
            </template>

            <template #body>
                <slot />
            </template>
        </UDashboardPanel>
    </UDashboardGroup>
</template>
