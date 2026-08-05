<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import UserCard from '@/components/UserCard.vue'
import UserFiltersSlideover from '@/components/UserFiltersSlideover.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { route } from '@/utils/route'
import { userSortItems, verifiedFilterItems } from '@/utils/userPagination'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    userFilterOptions: App.Data.FilterOptionData[]
    query: App.Data.Users.UserIndexQueryData
}>>()

const { applyQuery, processing, query, resetQuery, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('pagination.cards'),
    serverQuery: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-directory-results'
})

function setSort(value: App.Enums.UserSort): void {
    query.sort = value
    applyQuery()
}
</script>

<template>
    <AppLayout
        title="User Directory"
        description="A card-based paginated listing using the same typed query and pagination primitives."
    >
        <UPage>
            <UPageHeader
                title="User Directory"
                description="A regular paginated dataset rendered manually with cards, filters, and combined sort options."
                :links="[{ label: 'Table', to: route('pagination.table'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-table' }]"
            />

            <UPageBody id="users-directory-results">
                <UCard :ui="{ root: 'min-w-0', body: 'min-w-0' }">
                    <template #header>
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div />

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                <UFieldGroup class="w-full sm:w-auto">
                                    <UButton
                                        as="label"
                                        for="user-sort"
                                        color="neutral"
                                        variant="subtle"
                                        label="Sort by"
                                        class="shrink-0 cursor-pointer"
                                    />

                                    <USelect
                                        id="user-sort"
                                        :model-value="query.sort"
                                        :items="userSortItems"
                                        :disabled="processing"
                                        class="min-w-0 flex-1 sm:w-40 sm:flex-none"
                                        @update:model-value="setSort($event as App.Enums.UserSort)"
                                    />
                                </UFieldGroup>

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
                        </div>
                    </template>

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
                        description="Try changing the search, filters, or date range."
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
