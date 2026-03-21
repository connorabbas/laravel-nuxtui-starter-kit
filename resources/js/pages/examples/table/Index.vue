<script setup lang="ts">
import { computed, h, resolveComponent } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import AppLayout from '@/layouts/app/Index.vue'
import UserFiltersSlideover from '@/components/examples/UserFiltersSlideover.vue'
import { Head as IHead } from '@inertiajs/vue3'
import { usePaginatedDataTable } from '@/composables/usePaginatedDataTable'
import type { LengthAwarePaginator } from '@/types/pagination'
import type { User } from '@/types'
import type { UserDirectoryFilterDefinition } from '@/types/examples/user-directory'

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

const props = defineProps<{
    users: LengthAwarePaginator<User>
    filterDefinitions: Record<string, UserDirectoryFilterDefinition>
    accountStatusOptions: Array<{ label: string; value: string }>
    accountProviderOptions: Array<{ label: string; value: string }>
}>()

const paginated = usePaginatedDataTable('users', {
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

const resultsSummary = computed(() => {
    const first = props.users.total === 0
        ? 0
        : (props.users.current_page - 1) * props.users.per_page + 1
    const last = Math.min(props.users.current_page * props.users.per_page, props.users.total)

    return `Showing ${first} to ${last} of ${props.users.total} users`
})

function sortableHeader(id: string, label: string): () => unknown {
    return () => h(UButton, {
        color: 'neutral',
        variant: 'ghost',
        label,
        icon: paginated.sortIcon(id),
        class: '-mx-2.5',
        onClick: () => {
            void paginated.toggleSort(id)
        },
    })
}

const columns: TableColumn<User>[] = [
    {
        accessorKey: 'id',
        header: sortableHeader('id', 'ID'),
    },
    {
        accessorKey: 'name',
        header: sortableHeader('name', 'Name'),
    },
    {
        accessorKey: 'email',
        header: sortableHeader('email', 'Email'),
    },
    {
        accessorKey: 'created_at',
        header: sortableHeader('created_at', 'Created'),
        cell: ({ row }) => {
            return new Date(row.original.created_at).toLocaleDateString()
        },
    },
    {
        id: 'accounts',
        header: 'Accounts',
        cell: ({ row }) => {
            return h('div', { class: 'flex flex-wrap gap-1' },
                (row.original.accounts ?? []).map(account => h(UBadge, {
                    key: account.id,
                    variant: 'subtle',
                    color: 'neutral',
                }, () => `${account.provider ?? account.name} | ${account.status} | ${new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Number(account.balance))}`))
            )
        },
    },
    {
        accessorKey: 'accounts_count',
        header: sortableHeader('accounts_count', 'Accounts'),
        cell: ({ row }) => {
            return row.original.accounts_count ?? 0
        },
    },
    {
        accessorKey: 'accounts_sum_balance',
        header: sortableHeader('accounts_sum_balance', 'Total Balance'),
        cell: ({ row }) => {
            const amount = Number(row.original.accounts_sum_balance ?? 0)

            return new Intl.NumberFormat('en-US', {
                style: 'currency',
                currency: 'USD',
            }).format(amount)
        },
    },
    {
        accessorKey: 'accounts_max_opened_at',
        header: sortableHeader('accounts_max_opened_at', 'Latest Account'),
        cell: ({ row }) => {
            const value = row.original.accounts_max_opened_at

            if (!value) {
                return '-'
            }

            return new Date(value).toLocaleDateString()
        },
    },
]
</script>

<template>
    <AppLayout page-title="User Table Example">
        <IHead title="User Table Example" />

        <UPage>
            <UPageHeader
                title="User Directory (Table)"
                description="Nuxt UI UTable + server-side filters/sorting/pagination with URL sync."
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
                                :per-page-options="perPageOptions"
                                title="Table filters"
                                description="Adjust table filters and rows per page."
                            />
                        </div>
                    </template>

                    <UTable
                        :loading="paginated.processing.value"
                        :data="props.users.data"
                        :columns="columns"
                    />

                    <div class="mt-4 flex flex-col gap-3 border-t border-default pt-4 sm:flex-row sm:items-center sm:justify-between">
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
                </UCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
