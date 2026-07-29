<script setup lang="ts">
import InertiaPagination from '@/components/InertiaPagination.vue'
import { usePaginatedQuery } from '@/composables/usePaginatedQuery'
import AppLayout from '@/layouts/app/Index.vue'
import type { AppPageProps, LengthAwarePaginator } from '@/types'
import { route } from '@/utils/route'
import { ref, watch } from 'vue'

const props = defineProps<AppPageProps<{
    users: LengthAwarePaginator<App.Data.UserData>
    query: App.Data.Users.UserIndexQueryData
}>>()

const pagination = usePaginatedQuery<App.Data.Users.UserIndexQueryData, App.Data.UserData>({
    route: route('users.directory'),
    initialQuery: props.query,
    paginator: () => props.users,
    only: ['users', 'query'],
    //scrollTo: '#users-directory-results'
})

const { processing, query, resultText, setPage, setPerPage, update } = pagination

const search = ref(props.query.search ?? '')
const verified = ref<boolean | null>(props.query.verified)
const createdFrom = ref(props.query.createdFrom ?? '')
const createdUntil = ref(props.query.createdUntil ?? '')
let searchTimeout: ReturnType<typeof setTimeout> | undefined
let suppressFilterWatch = false

const sortItems = [
    { label: 'Newest', value: 'newest' },
    { label: 'Oldest', value: 'oldest' },
    { label: 'Name - A to Z', value: 'name_asc' },
    { label: 'Name - Z to A', value: 'name_desc' },
    { label: 'Email - A to Z', value: 'email_asc' },
    { label: 'Email - Z to A', value: 'email_desc' }
]

watch(search, () => {
    if (suppressFilterWatch) {
        return
    }

    clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 350)
})

watch([verified, createdFrom, createdUntil], () => {
    if (suppressFilterWatch) {
        return
    }

    clearTimeout(searchTimeout)
    applyFilters()
})

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
    clearTimeout(searchTimeout)

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

function formatDate(date: string): string {
    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(date))
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
                :links="[{ label: 'Table view', to: route('users.index'), color: 'neutral', variant: 'subtle', icon: 'i-lucide-table' }]"
            />

            <UPageBody id="users-directory-results">
                <UPageCard>
                    <div
                        class="mb-6 grid gap-3 lg:grid-cols-[minmax(0,1fr)_12rem_12rem_10rem_10rem_auto] lg:items-end"
                    >
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
                                :items="sortItems"
                                :disabled="processing"
                                class="w-full"
                                @update:model-value="update({ sort: $event as App.Enums.UserSort }, { resetPage: true, replace: true })"
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
