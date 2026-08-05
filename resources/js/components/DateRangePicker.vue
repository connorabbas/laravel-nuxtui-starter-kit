<script setup lang="ts">
import { DateFormatter, parseDate } from '@internationalized/date'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import { computed } from 'vue'
import type { DateValue } from '@internationalized/date'

type DateRangeValue = {
    start: DateValue | undefined
    end: DateValue | undefined
}

const props = defineProps<{
    minValue?: DateValue
    maxValue?: DateValue
    timeZone: string
}>()

const start = defineModel<string | null>('start', { required: true })
const end = defineModel<string | null>('end', { required: true })

const dateFormatter = computed(() => new DateFormatter('en-US', { dateStyle: 'medium', timeZone: props.timeZone }))
const breakpoints = useBreakpoints(breakpointsTailwind)
const isDesktop = breakpoints.greaterOrEqual('sm')
const numberOfMonths = computed(() => isDesktop.value ? 2 : 1)

const dateRange = computed<DateRangeValue | null>({
    get: () => {
        const startDate = parseDateValue(start.value)
        const endDate = parseDateValue(end.value)

        if (!startDate && !endDate) {
            return null
        }

        return {
            start: startDate,
            end: endDate,
        }
    },
    set: (value) => {
        start.value = stringifyDateValue(value?.start)
        end.value = stringifyDateValue(value?.end)
    },
})

const label = computed(() => {
    const value = dateRange.value

    if (!value?.start && !value?.end) {
        return 'Pick a Date'
    }

    if (value.start && value.end) {
        return `${formatDateValue(value.start)} - ${formatDateValue(value.end)}`
    }

    if (value.start) {
        return `From ${formatDateValue(value.start)}`
    }

    if (value.end) {
        return `Until ${formatDateValue(value.end)}`
    }

    return 'Pick a Date'
})

function parseDateValue(value: string | null): DateValue | undefined {
    if (!value) {
        return undefined
    }

    try {
        return parseDate(value)
    } catch {
        return undefined
    }
}

function stringifyDateValue(value: DateValue | undefined): string | null {
    return value?.toString() ?? null
}

function formatDateValue(value: DateValue): string {
    return dateFormatter.value.format(value.toDate(props.timeZone))
}
</script>

<template>
    <UPopover
        :content="{
            align: 'center',
        }"
    >
        <UButton
            type="button"
            color="neutral"
            variant="subtle"
            icon="i-lucide-calendar"
            class="w-full justify-start"
        >
            {{ label }}
        </UButton>

        <template #content>
            <UCalendar
                v-model="dateRange"
                range
                :min-value="minValue"
                :max-value="maxValue"
                :number-of-months="numberOfMonths"
                class="p-2"
            />
        </template>
    </UPopover>
</template>
