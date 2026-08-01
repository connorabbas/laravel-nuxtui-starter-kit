<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { formatDate } from '@/utils/date'
import { route } from '@/utils/route'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    query: App.Data.PaginatedData
}>>()

const { processing, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.PaginatedData, App.Data.UserData>({
    route: route('pagination.basic.cards'),
    query: () => props.query,
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
                <UCard :ui="{ root: 'min-w-0', body: 'min-w-0' }">
                    <div
                        v-if="users.data.length"
                        class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3"
                    >
                        <UPageCard
                            v-for="user in users.data"
                            :key="user.id"
                            :title="user.name"
                            :description="user.email"
                            variant="outline"
                            class="min-w-0"
                            :ui="{
                                container: 'min-w-0',
                                wrapper: 'min-w-0',
                                body: 'min-w-0',
                                title: 'break-words',
                                description: 'break-all'
                            }"
                        >
                            <UBadge
                                color="neutral"
                                variant="subtle"
                                :label="`Joined ${formatDate(user.createdAt)}`"
                            />
                        </UPageCard>
                    </div>

                    <UAlert
                        v-else
                        color="neutral"
                        variant="subtle"
                        icon="i-lucide-search-x"
                        title="No users found"
                        description="There are no users to show yet."
                    />

                    <template #footer>
                        <InertiaPagination
                            :paginator="users"
                            :summary="resultText"
                            :disabled="processing"
                            :bordered="false"
                            @page="setPage"
                            @per-page="(perPage) => setPerPage(perPage, { replace: true })"
                        />
                    </template>
                </UCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
