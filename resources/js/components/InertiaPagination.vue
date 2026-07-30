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
    showEdges?: boolean
    siblingCount?: number
    bordered?: boolean
}>(), {
    perPageOptions: () => [10, 25, 50],
    summary: undefined,
    disabled: false,
    showEdges: false,
    siblingCount: 1,
    bordered: true
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
    <div
        class="flex min-w-0 flex-col gap-3 md:flex-row md:items-center md:justify-between"
        :class="bordered ? 'border-t border-default pt-4' : undefined"
    >
        <p
            v-if="summary"
            class="min-w-0 text-sm text-muted"
        >
            {{ summary }}
        </p>

        <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-center md:ml-auto">
            <USelect
                :model-value="paginator.per_page"
                :items="perPageItems"
                :disabled="disabled"
                class="w-full shrink-0 sm:w-40"
                @update:model-value="emit('perPage', Number($event))"
            />

            <div class="min-w-0 overflow-x-auto pb-1">
                <UPagination
                    :page="paginator.current_page"
                    :items-per-page="paginator.per_page"
                    :total="paginator.total"
                    :disabled="disabled"
                    :show-edges="showEdges"
                    :sibling-count="siblingCount"
                    class="w-max"
                    @update:page="emit('page', $event)"
                />
            </div>
        </div>
    </div>
</template>
