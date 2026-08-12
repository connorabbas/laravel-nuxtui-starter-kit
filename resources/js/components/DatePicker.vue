<script setup lang="ts">
import { DateFormatter, parseDate } from '@internationalized/date'
import { computed } from 'vue'
import type { DateValue } from '@internationalized/date'

const props = defineProps<{
    minValue?: DateValue
    maxValue?: DateValue
    timeZone: string
}>()

const emit = defineEmits<{
    change: []
}>()

const model = defineModel<string | null>({ required: true })

const dateFormatter = computed(() => new DateFormatter('en-US', { dateStyle: 'medium', timeZone: props.timeZone }))

const date = computed<DateValue | undefined>({
    get: () => parseDateValue(model.value),
    set: (value) => {
        const nextValue = value?.toString() ?? null

        if (model.value !== nextValue) {
            model.value = nextValue
            emit('change')
        }
    },
})

const label = computed(() => {
    if (!date.value) {
        return 'Select a date'
    }

    return dateFormatter.value.format(date.value.toDate(props.timeZone))
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
