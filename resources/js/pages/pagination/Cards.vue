<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { formatDate } from '@/utils/date'
import { nullableArray, nullableString } from '@/utils/query'
import { route } from '@/utils/route'
import { userSortItems, verifiedFilterItems } from '@/utils/userPagination'
import { useDebounceFn } from '@vueuse/core'
import { computed } from 'vue'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    userFilterOptions: App.Data.FilterOptionData[]
    query: App.Data.Users.UserIndexQueryData
}>>()

const { apply, processing, query, resultText, setPage, setPerPage } = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('pagination.cards'),
    query: () => props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-directory-results'
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

function applyFilters(): void {
    apply({}, { replace: true })
}

function setVerified(value: boolean | null): void {
    query.verified = value
    applyFilters()
}

function setSort(value: App.Enums.UserSort): void {
    apply({ sort: value }, { replace: true })
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
                <UPageCard>
                    <div class="mb-6 grid gap-3 lg:grid-cols-[minmax(0,1fr)_12rem_14rem_12rem_10rem_10rem_auto] lg:items-end">
                        <UFormField label="Search">
                            <UInput
                                v-model="search"
                                icon="i-lucide-search"
                                placeholder="Search name or email..."
                                class="w-full"
                            />
                        </UFormField>

                        <UFormField label="Sort">
                            <USelect
                                :model-value="query.sort"
                                :items="userSortItems"
                                :disabled="processing"
                                class="w-full"
                                @update:model-value="setSort($event as App.Enums.UserSort)"
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

                    <div
                        v-if="users.data.length"
                        class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                    >
                        <UPageCard
                            v-for="user in users.data"
                            :key="user.id"
                            :title="user.name"
                            :description="user.email"
                            variant="outline"
                        >
                            <div class="space-y-4">
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

                                <p class="text-sm text-muted">
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

                    <InertiaPagination
                        class="mt-6"
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
