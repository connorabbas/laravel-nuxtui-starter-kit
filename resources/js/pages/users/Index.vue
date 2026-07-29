<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { route } from '@/utils/route'
import type { TableColumn } from '@nuxt/ui'
import type { SortingState } from '@tanstack/vue-table'
import { useDebounceFn } from '@vueuse/core'
import { ref, watch } from 'vue'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    query: App.Data.Users.UserIndexQueryData
}>>()

const { processing, query, resultText, setPage, setPerPage, update } = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('users.index'),
    initialQuery: props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-results'
})

const search = ref(props.query.search ?? '')
const verified = ref<boolean | null>(props.query.verified)
const createdFrom = ref(props.query.createdFrom ?? '')
const createdUntil = ref(props.query.createdUntil ?? '')
let suppressFilterWatch = false

const applySearchFilters = useDebounceFn(() => {
    applyFilters()
}, 350)

const sortToTableSorting = {
    newest: [{ id: 'createdAt', desc: true }],
    oldest: [{ id: 'createdAt', desc: false }],
    name_asc: [{ id: 'name', desc: false }],
    name_desc: [{ id: 'name', desc: true }],
    email_asc: [{ id: 'email', desc: false }],
    email_desc: [{ id: 'email', desc: true }]
} satisfies Record<App.Enums.UserSort, SortingState>

const tableSorting = ref<SortingState>(sortToTableSorting[props.query.sort])

watch(() => query.sort, (sort) => {
    tableSorting.value = sortToTableSorting[sort]
})

watch(search, () => {
    if (suppressFilterWatch) {
        return
    }

    applySearchFilters()
})

watch([verified, createdFrom, createdUntil], () => {
    if (suppressFilterWatch) {
        return
    }

    applyFilters()
})

const columns: TableColumn<App.Data.UserData>[] = [
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'email', header: 'Email' },
    { accessorKey: 'emailVerifiedAt', header: 'Verification' },
    { accessorKey: 'createdAt', header: 'Created' }
]

function applyFilters(): void {
    update({
        search: search.value || null,
        verified: verified.value,
        createdFrom: createdFrom.value || null,
        createdUntil: createdUntil.value || null
    }, { resetPage: true, replace: true })
}

function clearFilters(): void {
    suppressFilterWatch = true

    search.value = ''
    verified.value = null
    createdFrom.value = ''
    createdUntil.value = ''

    update({
        search: null,
        verified: null,
        createdFrom: null,
        createdUntil: null
    }, { resetPage: true, replace: true })

    setTimeout(() => {
        suppressFilterWatch = false
    }, 0)
}

function applyTableSorting(sorting: SortingState | undefined): void {
    tableSorting.value = sorting ?? []

    const firstSort = tableSorting.value[0]

    const nextSort = tableSortingToQuerySort(firstSort?.id, firstSort?.desc ?? false)

    update({ sort: nextSort }, { resetPage: true, replace: true })
}

function tableSortingToQuerySort(columnId?: string, desc = false): App.Enums.UserSort {
    if (!columnId) {
        return 'newest'
    }

    if (columnId === 'name') {
        return desc ? 'name_desc' : 'name_asc'
    }

    if (columnId === 'email') {
        return desc ? 'email_desc' : 'email_asc'
    }

    return desc ? 'newest' : 'oldest'
}

function sortingIcon(columnId: string): string {
    const currentSort = tableSorting.value[0]

    if (currentSort?.id !== columnId) {
        return 'i-lucide-arrow-up-down'
    }

    return currentSort.desc ? 'i-lucide-arrow-down' : 'i-lucide-arrow-up'
}

function formatDate(date: string): string {
    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(date))
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
                :links="[{ label: 'Card directory', to: route('users.directory'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-id-card' }]"
            />

            <UPageBody id="users-results">
                <UPageCard>
                    <div class="mb-4 flex flex-col gap-3">
                        <div class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_12rem_10rem_10rem_auto] lg:items-end">
                            <UFormField label="Search">
                                <UInput
                                    v-model="search"
                                    icon="i-lucide-search"
                                    placeholder="Search name or email..."
                                    class="w-full"
                                />
                            </UFormField>

                            <UFormField label="Verification">
                                <USelect
                                    v-model="verified"
                                    :items="[
                                        { label: 'Any', value: null },
                                        { label: 'Verified', value: true },
                                        { label: 'Unverified', value: false }
                                    ]"
                                    class="w-full"
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
                        @update:sorting="applyTableSorting"
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
