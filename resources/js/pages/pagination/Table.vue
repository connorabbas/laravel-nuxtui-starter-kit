<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import { useTableQuerySorting } from '@/composables/useTableQuerySorting'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { formatDate } from '@/utils/date'
import { nullableArray, nullableString } from '@/utils/query'
import { route } from '@/utils/route'
import { userTableSorting, verifiedFilterItems } from '@/utils/userPagination'
import type { TableColumn } from '@nuxt/ui'
import { useDebounceFn } from '@vueuse/core'
import { computed } from 'vue'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    userFilterOptions: App.Data.FilterOptionData[]
    query: App.Data.Users.UserIndexQueryData
}>>()

const { apply, processing, query, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('pagination.table'),
    query: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-results'
})

const { setTableSorting, sortingIcon, tableSorting } = useTableQuerySorting({
    sort: () => query.sort,
    config: userTableSorting,
    apply: (sort) => apply({ sort }, { replace: true })
})

const applySearchFilter = useDebounceFn(() => {
    apply({}, { replace: true })
}, 350)

const search = computed({
    get: () => query.search ?? '',
    set: (value) => {
        query.search = nullableString(value)
        applySearchFilter()
    }
})

const userIds = computed<number[]>({
    get: () => query.userIds ?? [],
    set: (value) => {
        query.userIds = nullableArray(value)
        applyFilters()
    }
})

const createdFrom = computed({
    get: () => query.createdFrom ?? '',
    set: (value) => {
        query.createdFrom = nullableString(value)
        applyFilters()
    }
})

const createdUntil = computed({
    get: () => query.createdUntil ?? '',
    set: (value) => {
        query.createdUntil = nullableString(value)
        applyFilters()
    }
})

const columns: TableColumn<App.Data.UserData>[] = [
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'email', header: 'Email' },
    { accessorKey: 'emailVerifiedAt', header: 'Verification' },
    { accessorKey: 'createdAt', header: 'Created' }
]

function applyFilters(): void {
    apply({}, { replace: true })
}

function setVerified(value: boolean | null): void {
    query.verified = value
    applyFilters()
}

function clearFilters(): void {
    apply({
        search: null,
        userIds: null,
        verified: null,
        createdFrom: null,
        createdUntil: null
    }, { replace: true })
}

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
                <UPageCard>
                    <div class="mb-4 flex flex-col gap-3">
                        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_14rem_12rem_10rem_10rem_auto] lg:items-end">
                            <UFormField label="Search">
                                <UInput
                                    v-model="search"
                                    icon="i-lucide-search"
                                    placeholder="Search name or email..."
                                    class="w-full"
                                />
                            </UFormField>

                            <UFormField label="Specific users">
                                <USelect
                                    v-model="userIds"
                                    multiple
                                    :items="userFilterOptions"
                                    placeholder="Any user"
                                    class="w-full"
                                />
                            </UFormField>

                            <UFormField label="Verification">
                                <USelect
                                    :model-value="query.verified"
                                    :items="verifiedFilterItems"
                                    class="w-full"
                                    @update:model-value="setVerified($event as boolean | null)"
                                />
                            </UFormField>

                            <UFormField label="Created from">
                                <UInput
                                    v-model="createdFrom"
                                    type="date"
                                    class="w-full"
                                />
                            </UFormField>

                            <UFormField label="Created until">
                                <UInput
                                    v-model="createdUntil"
                                    type="date"
                                    class="w-full"
                                />
                            </UFormField>

                            <UButton
                                type="button"
                                label="Clear"
                                color="neutral"
                                variant="subtle"
                                :disabled="processing"
                                @click="clearFilters"
                            />
                        </div>
                    </div>

                    <UTable
                        :sorting="tableSorting"
                        :data="users.data"
                        :columns="columns"
                        :loading="processing"
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
                            {{ formatDate(row.original.createdAt) }}
                        </template>
                    </UTable>

                    <InertiaPagination
                        class="mt-4"
                        :paginator="users"
                        :summary="resultText"
                        :disabled="processing"
                        :per-page-options="[10, 25, 50]"
                        @page="setPage"
                        @per-page="(perPage) => setPerPage(perPage, { replace: true })"
                    />
                </UPageCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
