<script setup lang="ts">
import { today } from '@internationalized/date'
import { usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import DatePicker from '@/components/DatePicker.vue'
import DateRangePicker from '@/components/DateRangePicker.vue'

const props = withDefaults(defineProps<{
    userFilterOptions: App.Data.FilterOptionData[]
    verifiedFilterItems: App.Data.FilterOptionData[]
    processing?: boolean
}>(), {
    processing: false
})

const emit = defineEmits<{
    apply: []
}>()

const search = defineModel<string | null>('search', { required: true })
const userIds = defineModel<string[] | null, string, string[], string[]>('userIds', {
    required: true,
    get: (value) => value ?? [],
    set: (value) => value.length > 0 ? value : null,
})
const verified = defineModel<boolean | null>('verified', { required: true })
const verifiedAt = defineModel<string | null>('verifiedAt', { required: true })
const createdFrom = defineModel<string | null>('createdFrom', { required: true })
const createdUntil = defineModel<string | null>('createdUntil', { required: true })

const page = usePage()
const isOpen = ref(false)
const timeZone = computed(() => page.props.config.timezone)
const maxFilterDate = computed(() => today(timeZone.value))
const draftSearch = ref<string | null>(null)
const draftUserIds = ref<string[]>([])
const draftVerified = ref<boolean | null>(null)
const draftVerifiedAt = ref<string | null>(null)
const draftCreatedFrom = ref<string | null>(null)
const draftCreatedUntil = ref<string | null>(null)

const activeFilterCount = computed(() => {
    let count = 0

    if (search.value) {
        count += 1
    }

    if (userIds.value.length > 0) {
        count += 1
    }

    if (verified.value !== null) {
        count += 1
    }

    if (verifiedAt.value) {
        count += 1
    }

    if (createdFrom.value || createdUntil.value) {
        count += 1
    }

    return count
})

function applyFilters(): void {
    commitDraft()
    emit('apply')
    isOpen.value = false
}

function clearFilters(): void {
    draftSearch.value = null
    draftUserIds.value = []
    draftVerified.value = null
    draftVerifiedAt.value = null
    draftCreatedFrom.value = null
    draftCreatedUntil.value = null

    commitDraft()

    emit('apply')

    isOpen.value = false
}

function updateOpen(open: boolean): void {
    if (open) {
        copyAppliedFiltersToDraft()
    }

    isOpen.value = open
}

function copyAppliedFiltersToDraft(): void {
    draftSearch.value = search.value
    draftUserIds.value = [...userIds.value]
    draftVerified.value = verified.value
    draftVerifiedAt.value = verifiedAt.value
    draftCreatedFrom.value = createdFrom.value
    draftCreatedUntil.value = createdUntil.value
}

function commitDraft(): void {
    search.value = draftSearch.value
    userIds.value = [...draftUserIds.value]
    verified.value = draftVerified.value
    verifiedAt.value = draftVerifiedAt.value
    createdFrom.value = draftCreatedFrom.value
    createdUntil.value = draftCreatedUntil.value
}
</script>

<template>
    <USlideover
        :open="isOpen"
        title="Filter users"
        description="Choose filters, then apply them to refresh the results."
        :ui="{ footer: 'justify-end' }"
        @update:open="updateOpen"
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
                        v-model.trim.nullable="draftSearch"
                        icon="i-lucide-search"
                        placeholder="Search name or email..."
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    label="Specific users"
                    description="This loads users as an illustrative multi-select filter. Large production datasets should use a remote-search selector."
                >
                    <USelect
                        v-model="draftUserIds"
                        multiple
                        :items="props.userFilterOptions"
                        placeholder="Any user"
                        class="w-full"
                    />
                </UFormField>

                <UFormField label="Verification status">
                    <USelect
                        v-model="draftVerified"
                        :items="verifiedFilterItems"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    label="Verified on"
                    description="Matches users verified on this exact day."
                >
                    <DatePicker
                        v-model="draftVerifiedAt"
                        :max-value="maxFilterDate"
                        :time-zone="timeZone"
                    />
                </UFormField>

                <UFormField label="Created">
                    <DateRangePicker
                        v-model:start="draftCreatedFrom"
                        v-model:end="draftCreatedUntil"
                        :max-value="maxFilterDate"
                        :time-zone="timeZone"
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
