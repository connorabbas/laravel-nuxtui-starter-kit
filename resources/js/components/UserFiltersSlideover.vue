<script setup lang="ts">
import { nullableArray, nullableString } from '@/utils/query'
import { computed, reactive, ref, watch } from 'vue'

type UserFiltersDraft = {
    search: string
    userIds: string[]
    verified: boolean | null
    createdFrom: string
    createdUntil: string
}

type UserFilterPatch = Pick<App.Data.Users.UserIndexQueryData, 'search' | 'userIds' | 'verified' | 'createdFrom' | 'createdUntil'>

const props = withDefaults(defineProps<{
    query: App.Data.Users.UserIndexQueryData
    userFilterOptions?: App.Data.FilterOptionData[]
    verifiedFilterItems: App.Data.FilterOptionData[]
    processing?: boolean
}>(), {
    processing: false
})

const emit = defineEmits<{
    apply: [filters: UserFilterPatch]
}>()

const isOpen = ref(false)
const draft = reactive<UserFiltersDraft>(draftFromQuery())

const activeFilterCount = computed(() => {
    let count = 0

    if (props.query.search) {
        count += 1
    }

    if (props.query.userIds && props.query.userIds.length > 0) {
        count += 1
    }

    if (props.query.verified !== null) {
        count += 1
    }

    if (props.query.createdFrom || props.query.createdUntil) {
        count += 1
    }

    return count
})

const userFilterOptions = computed(() => props.userFilterOptions ?? [])
const isLoadingUserFilterOptions = computed(() => props.userFilterOptions === undefined)

watch(isOpen, (open) => {
    if (open) {
        resetDraft()
    }
})

watch(() => props.query, () => {
    if (!isOpen.value) {
        resetDraft()
    }
}, { deep: true })

function draftFromQuery(): UserFiltersDraft {
    return {
        search: props.query.search ?? '',
        userIds: props.query.userIds ? [...props.query.userIds] : [],
        verified: props.query.verified,
        createdFrom: props.query.createdFrom ?? '',
        createdUntil: props.query.createdUntil ?? ''
    }
}

function resetDraft(): void {
    Object.assign(draft, draftFromQuery())
}

function filterPatchFromDraft(): UserFilterPatch {
    return {
        search: nullableString(draft.search),
        userIds: nullableArray(draft.userIds),
        verified: draft.verified,
        createdFrom: nullableString(draft.createdFrom),
        createdUntil: nullableString(draft.createdUntil)
    }
}

function applyFilters(): void {
    emit('apply', filterPatchFromDraft())
    isOpen.value = false
}

function clearFilters(): void {
    emit('apply', {
        search: null,
        userIds: null,
        verified: null,
        createdFrom: null,
        createdUntil: null
    })

    isOpen.value = false
}
</script>

<template>
    <USlideover
        v-model:open="isOpen"
        title="Filter users"
        description="Choose filters, then apply them to refresh the results."
        :ui="{ footer: 'justify-end' }"
    >
        <UButton
            type="button"
            label="Filters"
            color="neutral"
            variant="subtle"
            icon="i-lucide-sliders-horizontal"
            :disabled="processing"
        >
            <template
                v-if="activeFilterCount > 0"
                #trailing
            >
                <UBadge
                    color="primary"
                    variant="subtle"
                    size="xs"
                    :label="String(activeFilterCount)"
                />
            </template>
        </UButton>

        <template #body>
            <form
                id="user-filters"
                class="space-y-5"
                @submit.prevent="applyFilters"
            >
                <UFormField label="Search">
                    <UInput
                        v-model="draft.search"
                        icon="i-lucide-search"
                        placeholder="Search name or email..."
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Specific users">
                    <USelect
                        v-model="draft.userIds"
                        multiple
                        :items="userFilterOptions"
                        placeholder="Any user"
                        :loading="isLoadingUserFilterOptions"
                        :disabled="isLoadingUserFilterOptions"
                        class="w-full"
                    />

                    <p class="mt-2 text-xs text-muted">
                        This loads users as an illustrative multi-select filter. Large production datasets should use a remote-search selector.
                    </p>
                </UFormField>

                <UFormField label="Verification">
                    <USelect
                        v-model="draft.verified"
                        :items="verifiedFilterItems"
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Created from">
                    <UInput
                        v-model="draft.createdFrom"
                        type="date"
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Created until">
                    <UInput
                        v-model="draft.createdUntil"
                        type="date"
                        class="w-full"
                    />
                </UFormField>
            </form>
        </template>

        <template #footer>
            <UButton
                type="button"
                label="Clear"
                color="neutral"
                variant="outline"
                icon="i-lucide-rotate-ccw"
                :disabled="processing"
                @click="clearFilters"
            />

            <UButton
                type="submit"
                form="user-filters"
                label="Apply filters"
                icon="i-lucide-search"
                :loading="processing"
            />
        </template>
    </USlideover>
</template>
