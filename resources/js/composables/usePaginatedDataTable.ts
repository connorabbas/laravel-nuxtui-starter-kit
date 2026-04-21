import { computed } from 'vue'
import type { Page, PageProps } from '@inertiajs/core'
import type { SortingState } from '@tanstack/vue-table'
import {
    usePaginatedData,
    type UsePaginatedDataOptions,
    type FilterMatchMode,
    type InertiaRouterFetchCallbacks,
    type PaginatedDataVisitPayload,
} from './usePaginatedData'

export function usePaginatedDataTable(
    propDataToFetch: string | string[],
    options: UsePaginatedDataOptions = {}
) {
    const paginatedData = usePaginatedData(propDataToFetch, options)

    const filterModes = computed<Record<string, FilterMatchMode>>(() => {
        return Object.fromEntries(
            Object.entries(paginatedData.filters.value).map(([field, filter]) => [field, filter.op])
        ) as Record<string, FilterMatchMode>
    })

    function filter(options: InertiaRouterFetchCallbacks<PaginatedDataVisitPayload> = {}): Promise<Page<PageProps>> {
        return paginatedData.applyFilters(options)
    }

    function sort(nextSorting: SortingState, options: InertiaRouterFetchCallbacks<PaginatedDataVisitPayload> = {}): Promise<Page<PageProps>> {
        return paginatedData.setSorting(nextSorting, options)
    }

    function reset(options: InertiaRouterFetchCallbacks<PaginatedDataVisitPayload> = {}): Promise<Page<PageProps>> {
        return paginatedData.reset(options)
    }

    function hardReset(options: InertiaRouterFetchCallbacks<PaginatedDataVisitPayload> = {}): Promise<Page<PageProps>> {
        return paginatedData.hardReset(options)
    }

    return {
        ...paginatedData,
        filterModes,
        filter,
        sort,
        reset,
        hardReset,
    }
}
