<script setup lang="ts">
import { computed, ref } from 'vue'
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'
import type { FilterMatchMode, UsePaginatedDataState } from '@/composables/usePaginatedData'
import type { UserDirectoryFilterDefinition } from '@/types'

const props = withDefaults(defineProps<{
    paginated: Pick<UsePaginatedDataState,
        | 'processing'
        | 'pagination'
        | 'sortModel'
        | 'filteredOrSorted'
        | 'textFilterModel'
        | 'numberFilterModel'
        | 'multiSelectFilterModel'
        | 'dateFilterModel'
        | 'dateRangeFilterModel'
        | 'filterModeModel'
        | 'setFilterMode'
        | 'setSorting'
        | 'paginate'
        | 'applyFilters'
        | 'hardReset'
    >
    filterDefinitions: Record<string, UserDirectoryFilterDefinition>
    accountStatusOptions: Array<{ label: string; value: string }>
    accountProviderOptions: Array<{ label: string; value: string }>
    perPageOptions: number[]
    sortOptions?: Array<{
        label: string
        value: string
    }>
    title?: string
    description?: string
}>(), {
    title: 'Filters',
    description: 'Refine server-side results.',
    sortOptions: undefined,
})

const open = ref(false)

const sortModel = computed(() => props.paginated.sortModel.value)
const perPageModel = computed(() => props.paginated.pagination.value.perPage)

const isProcessing = computed(() => props.paginated.processing.value)
const hasActiveFilters = computed(() => props.paginated.filteredOrSorted.value)

const idFilter = props.paginated.numberFilterModel('id')
const nameFilter = props.paginated.textFilterModel('name')
const emailFilter = props.paginated.textFilterModel('email')
const createdAtDateFilter = props.paginated.dateFilterModel('created_at')
const createdAtRangeFilter = props.paginated.dateRangeFilterModel('created_at')
const accountStatusFilter = props.paginated.textFilterModel('accounts_status')
const accountProviderFilter = props.paginated.multiSelectFilterModel('accounts_provider')
const accountsOpenedAtDateFilter = props.paginated.dateFilterModel('accounts_opened_at')
const accountsOpenedAtRangeFilter = props.paginated.dateRangeFilterModel('accounts_opened_at')

const idFilterMode = props.paginated.filterModeModel('id')
const nameFilterMode = props.paginated.filterModeModel('name')
const emailFilterMode = props.paginated.filterModeModel('email')
const createdAtFilterMode = props.paginated.filterModeModel('created_at')
const accountsOpenedAtFilterMode = props.paginated.filterModeModel('accounts_opened_at')

const hasDateRangeFilter = computed(() => createdAtFilterMode.value === 'between')
const hasAccountsOpenedAtRangeFilter = computed(() => accountsOpenedAtFilterMode.value === 'between')
const dateFormatter = new DateFormatter('en-US', {
    dateStyle: 'medium',
})

function parseCalendarDate(value: string): CalendarDate | undefined {
    if (!value) {
        return undefined
    }

    const [year, month, day] = value.split('-').map(Number)
    if (!year || !month || !day) {
        return undefined
    }

    return new CalendarDate(year, month, day)
}

function formatCalendarDate(value: CalendarDate | undefined): string {
    if (!value) {
        return ''
    }

    const year = String(value.year)
    const month = String(value.month).padStart(2, '0')
    const day = String(value.day).padStart(2, '0')

    return `${year}-${month}-${day}`
}

function formatCalendarButtonLabel(value: CalendarDate | undefined, placeholder: string): string {
    if (!value) {
        return placeholder
    }

    return dateFormatter.format(value.toDate(getLocalTimeZone()))
}

const createdAtCalendarValue = computed({
    get: () => parseCalendarDate(createdAtDateFilter.value),
    set: (value: CalendarDate | undefined) => {
        createdAtDateFilter.value = formatCalendarDate(value)
    },
})

const createdAtRangeStartCalendar = computed({
    get: () => parseCalendarDate(createdAtRangeFilter.value[0]),
    set: (value: CalendarDate | undefined) => {
        createdAtRangeFilter.value = [
            formatCalendarDate(value),
            createdAtRangeFilter.value[1],
        ]
    },
})

const createdAtRangeEndCalendar = computed({
    get: () => parseCalendarDate(createdAtRangeFilter.value[1]),
    set: (value: CalendarDate | undefined) => {
        createdAtRangeFilter.value = [
            createdAtRangeFilter.value[0],
            formatCalendarDate(value),
        ]
    },
})

const accountsOpenedAtCalendarValue = computed({
    get: () => parseCalendarDate(accountsOpenedAtDateFilter.value),
    set: (value: CalendarDate | undefined) => {
        accountsOpenedAtDateFilter.value = formatCalendarDate(value)
    },
})

const accountsOpenedAtRangeStartCalendar = computed({
    get: () => parseCalendarDate(accountsOpenedAtRangeFilter.value[0]),
    set: (value: CalendarDate | undefined) => {
        accountsOpenedAtRangeFilter.value = [
            formatCalendarDate(value),
            accountsOpenedAtRangeFilter.value[1],
        ]
    },
})

const accountsOpenedAtRangeEndCalendar = computed({
    get: () => parseCalendarDate(accountsOpenedAtRangeFilter.value[1]),
    set: (value: CalendarDate | undefined) => {
        accountsOpenedAtRangeFilter.value = [
            accountsOpenedAtRangeFilter.value[0],
            formatCalendarDate(value),
        ]
    },
})

async function applyFilters(): Promise<void> {
    await props.paginated.applyFilters()
    open.value = false
}

async function resetFilters(): Promise<void> {
    await props.paginated.hardReset()
    open.value = false
}

async function onPerPageChange(value: unknown): Promise<void> {
    await props.paginated.paginate(1, Number(value))
}

async function onSortChange(value: string): Promise<void> {
    if (!value) {
        await props.paginated.setSorting([])

        return
    }

    await props.paginated.setSorting([{
        id: value.startsWith('-') ? value.slice(1) : value,
        desc: value.startsWith('-'),
    }])
}

function onCreatedAtModeChange(mode: FilterMatchMode): void {
    props.paginated.setFilterMode('created_at', mode, { resetValue: true })
}

function onAccountsOpenedAtModeChange(mode: FilterMatchMode): void {
    props.paginated.setFilterMode('accounts_opened_at', mode, { resetValue: true })
}

function onAccountStatusChange(value: string): void {
    props.paginated.setFilterMode('accounts_status', 'equals')
    accountStatusFilter.value = value
}

function onAccountProviderChange(values: string[]): void {
    props.paginated.setFilterMode('accounts_provider', 'in')
    accountProviderFilter.value = values
}
</script>

<template>
    <USlideover
        v-model:open="open"
        side="right"
        :title="title"
        :description="description"
        :ui="{
            content: 'w-full sm:max-w-2xl'
        }"
    >
        <slot name="trigger">
            <UButton
                icon="i-lucide-sliders-horizontal"
                color="neutral"
                variant="outline"
                label="Filters"
            />
        </slot>

        <template #body>
            <div class="space-y-4">
                <UFormField
                    v-if="sortOptions"
                    label="Sort by"
                >
                    <USelect
                        :model-value="sortModel"
                        :items="sortOptions"
                        value-key="value"
                        label-key="label"
                        placeholder="Sort"
                        class="w-full"
                        @update:model-value="(value: string) => { void onSortChange(value) }"
                    />
                </UFormField>

                <UFormField label="ID">
                    <UFieldGroup class="w-full">
                        <USelect
                            v-model="idFilterMode"
                            :items="filterDefinitions.id.modes"
                            value-key="value"
                            label-key="label"
                            class="w-48"
                        />
                        <UInput
                            v-model="idFilter"
                            class="grow"
                            type="number"
                            placeholder="Filter by ID"
                        />
                    </UFieldGroup>
                </UFormField>

                <UFormField label="Name">
                    <UFieldGroup class="w-full">
                        <USelect
                            v-model="nameFilterMode"
                            :items="filterDefinitions.name.modes"
                            value-key="value"
                            label-key="label"
                            class="w-48"
                        />
                        <UInput
                            v-model="nameFilter"
                            class="grow"
                            placeholder="Filter by name"
                        />
                    </UFieldGroup>
                </UFormField>

                <UFormField label="Email">
                    <UFieldGroup class="w-full">
                        <USelect
                            v-model="emailFilterMode"
                            :items="filterDefinitions.email.modes"
                            value-key="value"
                            label-key="label"
                            class="w-48"
                        />
                        <UInput
                            v-model="emailFilter"
                            class="grow"
                            placeholder="Filter by email"
                        />
                    </UFieldGroup>
                </UFormField>

                <UFormField label="Account status">
                    <UFieldGroup class="w-full">
                        <USelect
                            :model-value="accountStatusFilter"
                            :items="accountStatusOptions"
                            value-key="value"
                            label-key="label"
                            class="w-full"
                            placeholder="Select status"
                            @update:model-value="(value: string) => onAccountStatusChange(value)"
                        />
                    </UFieldGroup>
                </UFormField>

                <UFormField label="Account provider">
                    <UFieldGroup class="w-full">
                        <USelect
                            :model-value="accountProviderFilter"
                            :items="accountProviderOptions"
                            value-key="value"
                            label-key="label"
                            class="w-full"
                            multiple
                            placeholder="Select providers"
                            @update:model-value="(values: string[]) => onAccountProviderChange(values)"
                        />
                    </UFieldGroup>
                </UFormField>

                <UFormField label="Created date">
                    <UFieldGroup
                        v-if="hasDateRangeFilter"
                        class="w-full"
                    >
                        <USelect
                            :model-value="createdAtFilterMode"
                            :items="filterDefinitions.created_at.modes"
                            value-key="value"
                            label-key="label"
                            class="w-32"
                            @update:model-value="(mode: FilterMatchMode) => onCreatedAtModeChange(mode)"
                        />
                        <UPopover class="grow">
                            <UButton
                                color="neutral"
                                variant="subtle"
                                icon="i-lucide-calendar"
                            >
                                {{ formatCalendarButtonLabel(createdAtRangeStartCalendar, 'Start date') }}
                            </UButton>

                            <template #content>
                                <UCalendar
                                    v-model="createdAtRangeStartCalendar"
                                    class="p-2"
                                />
                            </template>
                        </UPopover>
                        <UPopover class="grow">
                            <UButton
                                color="neutral"
                                variant="subtle"
                                icon="i-lucide-calendar"
                            >
                                {{ formatCalendarButtonLabel(createdAtRangeEndCalendar, 'End date') }}
                            </UButton>

                            <template #content>
                                <UCalendar
                                    v-model="createdAtRangeEndCalendar"
                                    class="p-2"
                                />
                            </template>
                        </UPopover>
                    </UFieldGroup>
                    <UFieldGroup
                        v-else
                        class="w-full"
                    >
                        <USelect
                            :model-value="createdAtFilterMode"
                            :items="filterDefinitions.created_at.modes"
                            value-key="value"
                            label-key="label"
                            class="w-32"
                            @update:model-value="(mode: FilterMatchMode) => onCreatedAtModeChange(mode)"
                        />
                        <UPopover class="grow">
                            <UButton
                                color="neutral"
                                variant="subtle"
                                icon="i-lucide-calendar"
                            >
                                {{ formatCalendarButtonLabel(createdAtCalendarValue, 'Select a date') }}
                            </UButton>

                            <template #content>
                                <UCalendar
                                    v-model="createdAtCalendarValue"
                                    class="p-2"
                                />
                            </template>
                        </UPopover>
                    </UFieldGroup>
                </UFormField>

                <UFormField label="Account opened date">
                    <UFieldGroup
                        v-if="hasAccountsOpenedAtRangeFilter"
                        class="w-full"
                    >
                        <USelect
                            :model-value="accountsOpenedAtFilterMode"
                            :items="filterDefinitions.accounts_opened_at.modes"
                            value-key="value"
                            label-key="label"
                            class="w-32"
                            @update:model-value="(mode: FilterMatchMode) => onAccountsOpenedAtModeChange(mode)"
                        />
                        <UPopover class="grow">
                            <UButton
                                color="neutral"
                                variant="subtle"
                                icon="i-lucide-calendar"
                            >
                                {{ formatCalendarButtonLabel(accountsOpenedAtRangeStartCalendar, 'Start date') }}
                            </UButton>

                            <template #content>
                                <UCalendar
                                    v-model="accountsOpenedAtRangeStartCalendar"
                                    class="p-2"
                                />
                            </template>
                        </UPopover>
                        <UPopover class="grow">
                            <UButton
                                color="neutral"
                                variant="subtle"
                                icon="i-lucide-calendar"
                            >
                                {{ formatCalendarButtonLabel(accountsOpenedAtRangeEndCalendar, 'End date') }}
                            </UButton>

                            <template #content>
                                <UCalendar
                                    v-model="accountsOpenedAtRangeEndCalendar"
                                    class="p-2"
                                />
                            </template>
                        </UPopover>
                    </UFieldGroup>
                    <UFieldGroup
                        v-else
                        class="w-full"
                    >
                        <USelect
                            :model-value="accountsOpenedAtFilterMode"
                            :items="filterDefinitions.accounts_opened_at.modes"
                            value-key="value"
                            label-key="label"
                            class="w-32"
                            @update:model-value="(mode: FilterMatchMode) => onAccountsOpenedAtModeChange(mode)"
                        />
                        <UPopover class="grow">
                            <UButton
                                color="neutral"
                                variant="subtle"
                                icon="i-lucide-calendar"
                            >
                                {{ formatCalendarButtonLabel(accountsOpenedAtCalendarValue, 'Select a date') }}
                            </UButton>

                            <template #content>
                                <UCalendar
                                    v-model="accountsOpenedAtCalendarValue"
                                    class="p-2"
                                />
                            </template>
                        </UPopover>
                    </UFieldGroup>
                </UFormField>
            </div>
        </template>

        <template #footer>
            <div class="flex w-full items-center gap-3">
                <USelect
                    :model-value="perPageModel"
                    :items="perPageOptions"
                    @update:model-value="(value: unknown) => { void onPerPageChange(value) }"
                />
                <div class="ml-auto flex items-center gap-2">
                    <UButton
                        icon="i-lucide-rotate-ccw"
                        color="neutral"
                        variant="outline"
                        :disabled="!hasActiveFilters || isProcessing"
                        @click="() => { void resetFilters() }"
                    >
                        Reset
                    </UButton>
                    <UButton
                        icon="i-lucide-search"
                        :loading="isProcessing"
                        @click="() => { void applyFilters() }"
                    >
                        Apply
                    </UButton>
                </div>
            </div>
        </template>
    </USlideover>
</template>
