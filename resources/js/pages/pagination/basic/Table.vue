<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { SharedPageProps, LengthAwarePaginator } from '@/types'
import { formatDate } from '@/utils/date'
import { route } from '@/utils/route'
import type { TableColumn } from '@nuxt/ui'

const props = defineProps<SharedPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    query: App.Data.PaginatedData
}>>()

const { processing, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.PaginatedData, App.Data.UserData>({
    route: route('pagination.basic.table'),
    serverQuery: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
})

const columns: TableColumn<App.Data.UserData>[] = [
    { accessorKey: 'name', header: 'Name' },
    { accessorKey: 'email', header: 'Email' },
    { accessorKey: 'createdAt', header: 'Created' },
]
</script>

<template>
    <AppLayout
        title="Basic User Table"
        description="Pagination and per-page URL sync without filters or sorting."
    >
        <UPage>
            <UPageHeader
                title="Basic User Table"
                description="A minimal Nuxt UI table example using only page and per-page query parameters."
                :links="[
                    { label: 'Basic cards', to: route('pagination.basic.cards'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-layout-grid' },
                    { label: 'Advanced table', to: route('pagination.table'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-sliders-horizontal' }
                ]"
            />

            <UPageBody id="basic-users-table-results">
                <UCard :ui="{ root: 'min-w-0', body: 'min-w-0 p-0!' }">
                    <div class="min-w-0 overflow-x-auto">
                        <UTable
                            :data="users.data"
                            :columns="columns"
                            :loading="processing"
                            :ui="{ root: 'min-w-max' }"
                            empty="No users found."
                        >
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
                            @page="setPage"
                            @per-page="setPerPage"
                        />
                    </template>
                </UCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
