<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import UserFiltersSlideover from '@/components/UserFiltersSlideover.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import { useTableQuerySorting } from '@/composables/useTableQuerySorting'
import AppLayout from '@/layouts/app/Index.vue'
import type { LengthAwarePaginator } from '@/types'
import { formatDate } from '@/utils/date'
import { route } from '@/utils/route'
import { userTableSorting, verifiedFilterItems } from '@/utils/userPagination'
import type { TableColumn } from '@nuxt/ui'

const props = defineProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    userFilterOptions: App.Data.FilterOptionData[]
    query: App.Data.Users.UserIndexQueryData
}>()

const { applyQuery, processing, query, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('pagination.table'),
    serverQuery: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-results'
})

const { setTableSorting, sortingIcon, tableSorting } = useTableQuerySorting({
    sort: () => query.sort,
    config: userTableSorting,
    onSortChange: (sort) => {
        query.sort = sort
        applyQuery()
    }
})

const columns: TableColumn<App.Data.UserData>[] = [
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'email', header: 'Email' },
    { accessorKey: 'emailVerifiedAt', header: 'Verification' },
    { accessorKey: 'createdAt', header: 'Created' }
]

</script>

<template>
    <AppLayout
        title="Users"
        description="Type-safe server-side pagination, filtering, and sorting with Inertia and Nuxt UI."
    >
        <UPage>
            <UPageHeader
                title="Users"
                description="A server-side Nuxt UI table backed by typed query props, filters, and safe sorting."
                :links="[{ label: 'Cards', to: route('pagination.cards'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-layout-grid' }]"
            />

            <UPageBody id="users-results">
                <UCard
                    :ui="{ root: 'min-w-0', body: 'min-w-0 p-0!' }"
                    variant="outline"
                >
                    <template #header>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div />

                            <UserFiltersSlideover
                                v-model:search="query.search"
                                v-model:user-ids="query.userIds"
                                v-model:verified="query.verified"
                                v-model:verified-at="query.verifiedAt"
                                v-model:created-from="query.createdFrom"
                                v-model:created-until="query.createdUntil"
                                :user-filter-options="userFilterOptions"
                                :verified-filter-items="verifiedFilterItems"
                                :processing="processing"
                                @apply="applyQuery"
                            />
                        </div>
                    </template>

                    <div class="min-w-0 overflow-x-auto">
                        <UTable
                            :sorting="tableSorting"
                            :sorting-options="{ manualSorting: true, enableSortingRemoval: false }"
                            :data="users.data"
                            :columns="columns"
                            :loading="processing"
                            :ui="{ root: 'min-w-max' }"
                            empty="No users found."
                            @update:sorting="setTableSorting"
                        >
                            <template #name-header="{ column }">
                                <UButton
                                    color="neutral"
                                    variant="ghost"
                                    label="Name"
                                    :icon="sortingIcon('name')"
                                    @click="column.toggleSorting(column.getIsSorted() === 'asc')"
                                />
                            </template>

                            <template #email-header="{ column }">
                                <UButton
                                    color="neutral"
                                    variant="ghost"
                                    label="Email"
                                    :icon="sortingIcon('email')"
                                    @click="column.toggleSorting(column.getIsSorted() === 'asc')"
                                />
                            </template>

                            <template #createdAt-header="{ column }">
                                <UButton
                                    color="neutral"
                                    variant="ghost"
                                    label="Created"
                                    :icon="sortingIcon('createdAt')"
                                    @click="column.toggleSorting(column.getIsSorted() === 'asc')"
                                />
                            </template>

                            <template #emailVerifiedAt-cell="{ row }">
                                <UBadge
                                    :label="row.original.emailVerifiedAt ? 'Verified' : 'Unverified'"
                                    :color="row.original.emailVerifiedAt ? 'success' : 'warning'"
                                    variant="subtle"
                                />
                            </template>

                            <template #createdAt-cell="{ row }">
                                {{ formatDate(row.original.createdAt, $page.props.config.timezone) }}
                            </template>
                        </UTable>
                    </div>

                    <template #footer>
                        <InertiaPagination
                            :paginator="users"
                            :summary="resultText"
                            :disabled="processing"
                            :per-page-options="[10, 25, 50]"
                            @page="setPage"
                            @per-page="setPerPage"
                        />
                    </template>
                </UCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
