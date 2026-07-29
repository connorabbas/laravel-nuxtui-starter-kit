<script setup generic="T" lang="ts">
import type { LengthAwarePaginator } from '@/types'
import { computed } from 'vue'

type PerPageSelectItem = {
    label: string
    value: number
}

const props = withDefaults(defineProps<{
    paginator: LengthAwarePaginator<T>
    perPageOptions?: number[]
    summary?: string
    disabled?: boolean
}>(), {
    perPageOptions: () => [10, 25, 50],
    summary: undefined,
    disabled: false
})

const emit = defineEmits<{
    page: [page: number]
    perPage: [perPage: number]
}>()

const perPageItems = computed<PerPageSelectItem[]>(() => props.perPageOptions.map((option) => ({
    label: `${option} per page`,
    value: option
})))
</script>

<template>
    <div class="flex flex-col gap-3 border-t border-default pt-4 md:flex-row md:items-center md:justify-between">
        <p
            v-if="summary"
            class="text-sm text-muted"
        >
            {{ summary }}
        </p>

        <div class="flex flex-col gap-3 sm:flex-row sm:items-center md:ml-auto">
            <USelect
                :model-value="paginator.per_page"
                :items="perPageItems"
                :disabled="disabled"
                class="w-full sm:w-40"
                @update:model-value="emit('perPage', Number($event))"
            />

            <UPagination
                :page="paginator.current_page"
                :items-per-page="paginator.per_page"
                :total="paginator.total"
                :disabled="disabled"
                show-edges
                @update:page="emit('page', $event)"
            />
        </div>
    </div>
</template>
