<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import UserFiltersSlideover from '@/components/UserFiltersSlideover.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { formatDate } from '@/utils/date'
import { route } from '@/utils/route'
import { userSortItems, verifiedFilterItems } from '@/utils/userPagination'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    userFilterOptions?: App.Data.FilterOptionData[]
    query: App.Data.Users.UserIndexQueryData
}>>()

const { apply, processing, query, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('pagination.cards'),
    query: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-directory-results'
})

function setSort(value: App.Enums.UserSort): void {
    apply({ sort: value }, { replace: true })
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
                                <UFieldGroup>
                                    <UButton
                                        color="neutral"
                                        variant="subtle"
                                        label="Sort by"
                                        disabled
                                    />
                                    <USelect
                                        :model-value="query.sort"
                                        :items="userSortItems"
                                        :disabled="processing"
                                        class="w-full"
                                        @update:model-value="setSort($event as App.Enums.UserSort)"
                                    />
                                </UFieldGroup>

                                <UserFiltersSlideover
                                    v-model:search="query.search"
                                    v-model:user-ids="query.userIds"
                                    v-model:verified="query.verified"
                                    v-model:created-from="query.createdFrom"
                                    v-model:created-until="query.createdUntil"
                                    :user-filter-options="userFilterOptions"
                                    :verified-filter-items="verifiedFilterItems"
                                    :processing="processing"
                                    @apply="apply({}, { replace: true })"
                                />
                            </div>
                        </div>
                    </template>

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
                            <div class="min-w-0 space-y-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <UBadge
                                        :label="user.emailVerifiedAt ? 'Verified' : 'Unverified'"
                                        :color="user.emailVerifiedAt ? 'success' : 'warning'"
                                        variant="subtle"
                                    />

                                    <UBadge
                                        color="neutral"
                                        variant="subtle"
                                        :label="`Joined ${formatDate(user.createdAt)}`"
                                    />
                                </div>

                                <p class="break-words text-sm text-muted">
                                    User #{{ user.id }} was last updated {{ formatDate(user.updatedAt) }}.
                                </p>
                            </div>
                        </UPageCard>
                    </div>

                    <UAlert
                        v-else
                        color="neutral"
                        variant="subtle"
                        icon="i-lucide-search-x"
                        title="No users found"
                        description="Try changing the search, filters, or date range."
                    />

                    <template #footer>
                        <InertiaPagination
                            :paginator="users"
                            :summary="resultText"
                            :disabled="processing"
                            :per-page-options="[10, 25, 50]"
                            @page="setPage"
                            @per-page="(perPage) => setPerPage(perPage, { replace: true })"
                        />
                    </template>
                </UCard>
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
