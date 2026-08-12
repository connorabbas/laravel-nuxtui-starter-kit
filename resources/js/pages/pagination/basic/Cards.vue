<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import UserCard from '@/components/UserCard.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { LengthAwarePaginator } from '@/types'
import { route } from '@/utils/route'

const props = defineProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    query: App.Data.PaginatedData
}>()

const { processing, resetQuery, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.PaginatedData, App.Data.UserData>({
    route: route('pagination.basic.cards'),
    serverQuery: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
})
</script>

<template>
    <AppLayout
        title="Basic User Cards"
        description="Pagination and per-page URL sync without filters or sorting."
    >
        <UPage>
            <UPageHeader
                title="Basic User Cards"
                description="A minimal card-list example using only page and per-page query parameters."
                :links="[
                    { label: 'Basic table', to: route('pagination.basic.table'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-table' },
                    { label: 'Advanced cards', to: route('pagination.cards'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-sliders-horizontal' }
                ]"
            />

            <UPageBody id="basic-users-cards-results">
                <UCard
                    :ui="{ root: 'min-w-0', body: 'min-w-0' }"
                    variant="outline"
                >
                    <div
                        v-if="users.data.length"
                        class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3"
                    >
                        <UserCard
                            v-for="user in users.data"
                            :key="user.id"
                            :user="user"
                            :time-zone="$page.props.config.timezone"
                        />
                    </div>

                    <UEmpty
                        v-else
                        variant="subtle"
                        icon="i-lucide-search-x"
                        title="No users found"
                        description="Reset the pagination query or add users to see results."
                        :actions="[{
                            label: 'Reset query',
                            icon: 'i-lucide-rotate-ccw',
                            color: 'neutral',
                            variant: 'subtle',
                            disabled: processing,
                            onClick: resetQuery
                        }]"
                    />

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
