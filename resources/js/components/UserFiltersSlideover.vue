<script setup lang="ts">
import { stringOrNull } from '@/utils'
import { computed, ref } from 'vue'

const props = withDefaults(defineProps<{
    userFilterOptions?: App.Data.FilterOptionData[]
    verifiedFilterItems: App.Data.FilterOptionData[]
    processing?: boolean
}>(), {
    processing: false
})

const emit = defineEmits<{
    apply: []
}>()

const search = defineModel<string | null>('search', { required: true })
const userIds = defineModel<string[] | null>('userIds', { required: true })
const verified = defineModel<boolean | null>('verified', { required: true })
const createdFrom = defineModel<string | null>('createdFrom', { required: true })
const createdUntil = defineModel<string | null>('createdUntil', { required: true })

const isOpen = ref(false)

const searchInput = computed({
    get: () => search.value ?? '',
    set: (value: string) => {
        search.value = stringOrNull(value)
    }
})

const selectedUserIds = computed({
    get: () => userIds.value ?? [],
    set: (value: string[]) => {
        userIds.value = value.length > 0 ? value : null
    }
})

const createdFromInput = computed({
    get: () => createdFrom.value ?? '',
    set: (value: string) => {
        createdFrom.value = stringOrNull(value)
    }
})

const createdUntilInput = computed({
    get: () => createdUntil.value ?? '',
    set: (value: string) => {
        createdUntil.value = stringOrNull(value)
    }
})

const activeFilterCount = computed(() => {
    let count = 0

    if (search.value) {
        count += 1
    }

    if (userIds.value && userIds.value.length > 0) {
        count += 1
    }

    if (verified.value !== null) {
        count += 1
    }

    if (createdFrom.value || createdUntil.value) {
        count += 1
    }

    return count
})

const userFilterOptions = computed(() => props.userFilterOptions ?? [])
const isLoadingUserFilterOptions = computed(() => props.userFilterOptions === undefined)

function applyFilters(): void {
    emit('apply')
    isOpen.value = false
}

function clearFilters(): void {
    search.value = null
    userIds.value = null
    verified.value = null
    createdFrom.value = null
    createdUntil.value = null

    emit('apply')

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
                        v-model="searchInput"
                        icon="i-lucide-search"
                        placeholder="Search name or email..."
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Specific users">
                    <USelect
                        v-model="selectedUserIds"
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
                        v-model="verified"
                        :items="verifiedFilterItems"
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Created from">
                    <UInput
                        v-model="createdFromInput"
                        type="date"
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Created until">
                    <UInput
                        v-model="createdUntilInput"
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
