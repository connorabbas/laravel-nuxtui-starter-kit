import type { SortingState } from '@tanstack/vue-table'
import { ref, type MaybeRefOrGetter, toValue, watch } from 'vue'

type QuerySortValue = string | number | boolean | null | undefined

type TableQuerySortingColumn<TSort extends QuerySortValue> = {
    asc: TSort
    desc: TSort
}

export type TableQuerySortingConfig<TSort extends QuerySortValue> = {
    default: TSort
    columns: Record<string, TableQuerySortingColumn<TSort>>
}

type UseTableQuerySortingOptions<TSort extends QuerySortValue> = {
    sort: MaybeRefOrGetter<TSort>
    config: TableQuerySortingConfig<TSort>
    apply: (sort: TSort) => void
}

export function useTableQuerySorting<TSort extends QuerySortValue>(options: UseTableQuerySortingOptions<TSort>) {
    const tableSorting = ref<SortingState>(resolveTableSorting(toValue(options.sort)))

    watch(() => toValue(options.sort), (sort) => {
        tableSorting.value = resolveTableSorting(sort)
    })

    function setTableSorting(sorting: SortingState | undefined): void {
        tableSorting.value = sorting ?? []

        const firstSort = tableSorting.value[0]

        options.apply(tableSortingToQuerySort(firstSort?.id, firstSort?.desc ?? false))
    }

    function sortingIcon(columnId: string): string {
        const currentSort = tableSorting.value[0]

        if (currentSort?.id !== columnId) {
            return 'i-lucide-arrow-up-down'
        }

        return currentSort.desc ? 'i-lucide-arrow-down' : 'i-lucide-arrow-up'
    }

    function tableSortingToQuerySort(columnId?: string, desc = false): TSort {
        if (!columnId) {
            return options.config.default
        }

        const columnSort = options.config.columns[columnId]

        if (!columnSort) {
            return options.config.default
        }

        return desc ? columnSort.desc : columnSort.asc
    }

    function resolveTableSorting(sort: TSort): SortingState {
        for (const [columnId, columnSort] of Object.entries(options.config.columns)) {
            if (columnSort.asc === sort) {
                return [{ id: columnId, desc: false }]
            }

            if (columnSort.desc === sort) {
                return [{ id: columnId, desc: true }]
            }
        }

        return []
    }

    return {
        setTableSorting,
        sortingIcon,
        tableSorting
    }
}
