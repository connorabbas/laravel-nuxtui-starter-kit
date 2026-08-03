<script setup lang="ts">
import { DateFormatter, getLocalTimeZone, parseDate } from '@internationalized/date'
import { computed } from 'vue'
import type { DateValue } from '@internationalized/date'

defineProps<{
    minValue?: DateValue
    maxValue?: DateValue
}>()

const model = defineModel<string | null>({ required: true })

const dateFormatter = new DateFormatter('en-US', { dateStyle: 'medium' })
const timeZone = getLocalTimeZone()

const date = computed<DateValue | undefined>({
    get: () => parseDateValue(model.value),
    set: (value) => {
        model.value = value?.toString() ?? null
    },
})

const label = computed(() => {
    if (!date.value) {
        return 'Select a date'
    }

    return dateFormatter.format(date.value.toDate(timeZone))
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
</script>

<template>
    <UPopover>
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
                v-model="date"
                :min-value="minValue"
                :max-value="maxValue"
                class="p-2"
            />
        </template>
    </UPopover>
</template>
