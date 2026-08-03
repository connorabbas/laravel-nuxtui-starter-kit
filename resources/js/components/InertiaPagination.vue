<script setup generic="T" lang="ts">
import type { LengthAwarePaginator } from '@/types'
import { useId } from 'vue'

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
    bordered: false
})

const emit = defineEmits<{
    page: [page: number]
    perPage: [perPage: number]
}>()

const perPageId = useId()
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
            <UFieldGroup class="w-full sm:w-auto">
                <UButton
                    as="label"
                    :for="perPageId"
                    color="neutral"
                    variant="subtle"
                    label="Per page"
                    class="shrink-0 cursor-pointer"
                />

                <USelect
                    :id="perPageId"
                    :model-value="paginator.per_page"
                    :items="props.perPageOptions"
                    :disabled="disabled"
                    class="min-w-0 flex-1 sm:flex-none"
                    @update:model-value="emit('perPage', Number($event))"
                />
            </UFieldGroup>

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
