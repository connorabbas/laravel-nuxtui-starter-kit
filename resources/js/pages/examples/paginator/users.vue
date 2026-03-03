<script setup lang="ts">
import { computed } from 'vue'
import AppLayout from '@/layouts/app/index.vue'
import UserFiltersSlideover from '@/components/examples/UserFiltersSlideover.vue'
import { Head as IHead } from '@inertiajs/vue3'
import { usePaginatedData } from '@/composables/usePaginatedData'
import type { LengthAwarePaginator } from '@/types/pagination'
import type { User } from '@/types'
import type { UserDirectoryFilterDefinition } from '@/types/examples/user-directory'

const props = defineProps<{
    users: LengthAwarePaginator<User>
    filterDefinitions: Record<string, UserDirectoryFilterDefinition>
    accountStatusOptions: Array<{ label: string; value: string }>
    accountProviderOptions: Array<{ label: string; value: string }>
}>()

const paginated = usePaginatedData('users', {
    initialPerPage: props.users.per_page,
    initialSorting: [{ id: 'created_at', desc: true }],
    initialFilters: {
        id: { op: 'equals', value: null },
        name: { op: 'contains', value: null },
        email: { op: 'contains', value: null },
        created_at: { op: 'dateIs', value: null },
        accounts_status: { op: 'equals', value: null },
        accounts_provider: { op: 'in', value: [] },
        accounts_opened_at: { op: 'dateIs', value: null },
    },
})

const perPageOptions = [10, 20, 50, 100]
const sortOptions = [
    { label: 'Name (A-Z)', value: 'name' },
    { label: 'Name (Z-A)', value: '-name' },
    { label: 'Email (A-Z)', value: 'email' },
    { label: 'Email (Z-A)', value: '-email' },
    { label: 'Created (Newest)', value: '-created_at' },
    { label: 'Created (Oldest)', value: 'created_at' },
    { label: 'Accounts Count (Asc)', value: 'accounts_count' },
    { label: 'Accounts Count (Desc)', value: '-accounts_count' },
    { label: 'Account Balance Sum (Asc)', value: 'accounts_sum_balance' },
    { label: 'Account Balance Sum (Desc)', value: '-accounts_sum_balance' },
    { label: 'Latest Account Opened (Asc)', value: 'accounts_max_opened_at' },
    { label: 'Latest Account Opened (Desc)', value: '-accounts_max_opened_at' },
]

const resultsSummary = computed(() => {
    const first = props.users.total === 0 ? 0 : paginated.firstDatasetIndex.value + 1
    const last = Math.min(paginated.firstDatasetIndex.value + paginated.pagination.value.perPage, props.users.total)

    return `Showing ${first} to ${last} of ${props.users.total} users`
})
</script>

<template>
    <AppLayout page-title="User Paginator Example">
        <IHead title="User Paginator Example" />

        <UPage>
            <UPageHeader
                title="User Directory (Paginator)"
                description="Server-side filtering, sorting, and pagination with URL sync."
            />

            <UPageBody>
                <UCard>
                    <template #header>
                        <div class="flex justify-end">
                            <UserFiltersSlideover
                                :paginated="paginated"
                                :filter-definitions="props.filterDefinitions"
                                :account-status-options="props.accountStatusOptions"
                                :account-provider-options="props.accountProviderOptions"
                                :sort-options="sortOptions"
                                :per-page-options="perPageOptions"
                                title="User filters"
                                description="Adjust filters, sort, and rows per page."
                            />
                        </div>
                    </template>

                    <div class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                            <UCard
                                v-for="user in props.users.data"
                                :key="user.id"
                                :ui="{ body: 'space-y-3' }"
                            >
                                <template #header>
                                    <p class="text-sm text-muted">
                                        #{{ user.id }}
                                    </p>
                                    <p class="text-lg font-semibold text-highlighted">
                                        {{ user.name }}
                                    </p>
                                </template>

                                <p class="text-sm text-muted">
                                    {{ user.email }}
                                </p>
                                <div class="flex flex-wrap gap-1">
                                    <UBadge
                                        v-for="account in user.accounts ?? []"
                                        :key="account.id"
                                        variant="subtle"
                                        color="neutral"
                                    >
                                        {{ account.provider ?? account.name }} | {{ account.status }} | {{ new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(account.balance)) }}
                                    </UBadge>
                                </div>
                                <p class="text-xs text-muted">
                                    Created {{ new Date(user.created_at).toLocaleDateString() }}
                                </p>
                            </UCard>
                        </div>

                        <div class="flex flex-col gap-3 border-t border-default pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-sm text-muted">
                                {{ resultsSummary }}
                            </p>
                            <UPagination
                                :page="props.users.current_page"
                                :items-per-page="props.users.per_page"
                                :total="props.users.total"
                                show-edges
                                @update:page="(page: number) => { void paginated.paginate(page) }"
                            />
                        </div>
                    </div>
                </UCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
