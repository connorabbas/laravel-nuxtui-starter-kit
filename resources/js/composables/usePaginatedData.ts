import { computed, onMounted, ref, toRaw } from 'vue'
import type { ComputedRef, Ref, WritableComputedRef } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import type { SortingState } from '@tanstack/vue-table'
import type { AppPageProps } from '@/types'

export type QueryParamValue = string | number | boolean | null | QueryParamValue[] | { [key: string]: QueryParamValue }

export type FilterMatchMode =
    | 'startsWith'
    | 'contains'
    | 'notContains'
    | 'endsWith'
    | 'equals'
    | 'notEquals'
    | 'in'
    | 'lt'
    | 'lte'
    | 'gt'
    | 'gte'
    | 'between'
    | 'dateIs'
    | 'dateIsNot'
    | 'dateBefore'
    | 'dateAfter'

export type FilterValue = string | number | boolean | null | Date | Array<string | number | boolean | Date>

export interface FilterStateItem {
    op: FilterMatchMode;
    value: FilterValue;
}

export type PaginatedDataFilters = Record<string, FilterStateItem>

export interface PaginationState {
    page: number;
    perPage: number;
}

export interface PaginatedDataQueryParams {
    page?: string | number;
    perPage?: string | number;
    sort?: string;
    filter?: Record<string, {
        op?: string;
        value?: QueryParamValue;
    }>;
    [key: string]: QueryParamValue | undefined;
}

export interface InertiaRouterFetchCallbacks {
    onSuccess?: (page: unknown) => void;
    onError?: (errors: unknown) => void;
    onFinish?: () => void;
}

export interface UsePaginatedDataOptions {
    initialFilters?: PaginatedDataFilters;
    initialPerPage?: number;
    initialSorting?: SortingState;
    showProgress?: boolean;
}

export interface UsePaginatedDataState {
    processing: Ref<boolean>;
    filters: Ref<PaginatedDataFilters>;
    sorting: Ref<SortingState>;
    pagination: Ref<PaginationState>;
    filteredOrSorted: ComputedRef<boolean>;
    firstDatasetIndex: ComputedRef<number>;
    sortModel: WritableComputedRef<string>;
    textFilterModel: (field: string) => WritableComputedRef<string>;
    numberFilterModel: (field: string) => WritableComputedRef<string>;
    multiSelectFilterModel: (field: string) => WritableComputedRef<string[]>;
    dateFilterModel: (field: string) => WritableComputedRef<string>;
    dateRangeFilterModel: (field: string) => WritableComputedRef<[string, string]>;
    filterModeModel: (field: string) => WritableComputedRef<FilterMatchMode>;
    setFilterMode: (field: string, mode: FilterMatchMode, options?: { resetValue?: boolean }) => void;
    sortIcon: (field: string) => string;
    toggleSort: (field: string) => Promise<unknown>;
    fetchData: (options?: InertiaRouterFetchCallbacks) => Promise<unknown>;
    paginate: (page: number, perPage?: number) => Promise<unknown>;
    applyFilters: (options?: InertiaRouterFetchCallbacks) => Promise<unknown>;
    setSorting: (sorting: SortingState, options?: InertiaRouterFetchCallbacks) => Promise<unknown>;
    reset: (options?: InertiaRouterFetchCallbacks) => Promise<unknown>;
    hardReset: (options?: InertiaRouterFetchCallbacks) => Promise<unknown>;
    parseUrlParams: () => void;
    debounceInputFilter: (callback: () => void, wait?: number) => void;
    scrollToTop: () => void;
}

type InertiaPaginatedPageProps = AppPageProps<{ queryParams?: PaginatedDataQueryParams }>

function cloneFilters(filters: PaginatedDataFilters): PaginatedDataFilters {
    return structuredClone(toRaw(filters))
}

function isDateValue(value: unknown): value is Date {
    return value instanceof Date && !Number.isNaN(value.getTime())
}

function isArrayDateValue(value: unknown): value is Date[] {
    return Array.isArray(value) && value.every(item => isDateValue(item))
}

function formatDateForQuery(date: Date): string {
    return date.toISOString().split('T')[0]
}

function normalizeFilterValueForQuery(value: FilterValue): QueryParamValue {
    if (isDateValue(value)) {
        return formatDateForQuery(value)
    }

    if (isArrayDateValue(value)) {
        return value.map(formatDateForQuery)
    }

    if (Array.isArray(value)) {
        return value.map((entry) => {
            if (isDateValue(entry)) {
                return formatDateForQuery(entry)
            }

            return String(entry)
        })
    }

    return value
}

function hasFilterValue(value: FilterValue): boolean {
    if (value === null || value === '') {
        return false
    }

    if (Array.isArray(value)) {
        return value.length > 0
    }

    return true
}

function parseSort(sortValue: string | undefined): SortingState {
    if (!sortValue) {
        return []
    }

    return sortValue
        .split(',')
        .map(segment => segment.trim())
        .filter(Boolean)
        .map((segment) => {
            const descending = segment.startsWith('-')

            return {
                id: descending ? segment.slice(1) : segment,
                desc: descending,
            }
        })
        .filter(sortItem => sortItem.id !== '')
}

function serializeSort(sorting: SortingState): string | null {
    if (!sorting.length) {
        return null
    }

    return sorting
        .map(({ id, desc }) => (desc ? `-${id}` : id))
        .join(',')
}

function parseValueFromQuery(rawValue: QueryParamValue): FilterValue {
    if (rawValue === null) {
        return null
    }

    if (Array.isArray(rawValue)) {
        return rawValue.map(value => {
            if (typeof value === 'number' || typeof value === 'boolean') {
                return value
            }

            return String(value)
        })
    }

    if (typeof rawValue === 'number' || typeof rawValue === 'boolean') {
        return rawValue
    }

    if (typeof rawValue === 'string') {
        if (rawValue === '') {
            return ''
        }

        const numeric = Number(rawValue)
        if (!Number.isNaN(numeric) && rawValue.trim() !== '') {
            return numeric
        }

        return rawValue
    }

    return String(rawValue)
}

export function usePaginatedData(
    propDataToFetch: string | string[],
    options: UsePaginatedDataOptions = {}
): UsePaginatedDataState {
    const page = usePage<InertiaPaginatedPageProps>()

    const initialFilters = options.initialFilters ?? {}
    const initialPerPage = options.initialPerPage ?? 20
    const initialSorting = options.initialSorting ?? []
    const showProgress = options.showProgress ?? true

    const processing = ref(false)
    const filters = ref<PaginatedDataFilters>(cloneFilters(initialFilters))
    const sorting = ref<SortingState>(structuredClone(initialSorting))
    const pagination = ref({
        page: 1,
        perPage: initialPerPage,
    })
    const debounceTimeout = ref<ReturnType<typeof setTimeout> | null>(null)

    const firstDatasetIndex = computed(() => {
        return (pagination.value.page - 1) * pagination.value.perPage
    })

    const filteredOrSorted = computed(() => {
        const hasActiveFilters = Object.values(filters.value).some((filter) => {
            return hasFilterValue(filter.value)
        })

        return hasActiveFilters || sorting.value.length > 0
    })

    const sortModel = computed({
        get: () => {
            const firstSort = sorting.value[0]

            if (!firstSort) {
                return ''
            }

            return firstSort.desc ? `-${firstSort.id}` : firstSort.id
        },
        set: (value: string) => {
            if (!value) {
                sorting.value = []
                return
            }

            sorting.value = [{
                id: value.startsWith('-') ? value.slice(1) : value,
                desc: value.startsWith('-'),
            }]
        },
    })

    function getFilter(field: string): FilterStateItem {
        const filter = filters.value[field]

        if (!filter) {
            throw new Error(`Unknown filter field: ${field}`)
        }

        return filter
    }

    function textFilterModel(field: string): WritableComputedRef<string> {
        return computed({
            get: () => {
                const value = getFilter(field).value

                return value === null ? '' : String(value)
            },
            set: (value: string) => {
                getFilter(field).value = value === '' ? null : value
            },
        })
    }

    function numberFilterModel(field: string): WritableComputedRef<string> {
        return computed({
            get: () => {
                const value = getFilter(field).value

                if (value === null || value === '') {
                    return ''
                }

                return String(value)
            },
            set: (value: string) => {
                if (value === '') {
                    getFilter(field).value = null
                    return
                }

                getFilter(field).value = Number(value)
            },
        })
    }

    function multiSelectFilterModel(field: string): WritableComputedRef<string[]> {
        return computed({
            get: () => {
                const value = getFilter(field).value

                if (!Array.isArray(value)) {
                    return []
                }

                return value.map(item => String(item))
            },
            set: (value: string[]) => {
                if (value.length === 0) {
                    getFilter(field).value = []

                    return
                }

                getFilter(field).value = value
            },
        })
    }

    function dateFilterModel(field: string): WritableComputedRef<string> {
        return computed({
            get: () => {
                const value = getFilter(field).value

                if (Array.isArray(value) || value === null) {
                    return ''
                }

                return String(value)
            },
            set: (value: string) => {
                getFilter(field).value = value === '' ? null : value
            },
        })
    }

    function dateRangeFilterModel(field: string): WritableComputedRef<[string, string]> {
        return computed({
            get: () => {
                const value = getFilter(field).value

                if (!Array.isArray(value)) {
                    return ['', ''] as [string, string]
                }

                return [
                    value[0] ? String(value[0]) : '',
                    value[1] ? String(value[1]) : '',
                ] as [string, string]
            },
            set: (value: [string, string]) => {
                if (!value[0] && !value[1]) {
                    getFilter(field).value = null
                    return
                }

                getFilter(field).value = [value[0], value[1]]
            },
        })
    }

    function filterModeModel(field: string): WritableComputedRef<FilterMatchMode> {
        return computed({
            get: () => getFilter(field).op,
            set: (mode: FilterMatchMode) => {
                getFilter(field).op = mode
            },
        })
    }

    function setFilterMode(field: string, mode: FilterMatchMode, options: { resetValue?: boolean } = {}): void {
        const { resetValue = false } = options

        getFilter(field).op = mode

        if (resetValue) {
            getFilter(field).value = null
        }
    }

    function sortIcon(field: string): string {
        const sortEntry = sorting.value.find(entry => entry.id === field)

        if (!sortEntry) {
            return 'i-lucide-arrow-up-down'
        }

        return sortEntry.desc
            ? 'i-lucide-arrow-down-wide-narrow'
            : 'i-lucide-arrow-up-narrow-wide'
    }

    function scrollToTop(): void {
        window.scrollTo({
            top: 0,
            behavior: 'smooth',
        })
    }

    function buildFilterQueryData(): Record<string, { op: string; value: QueryParamValue }> {
        const queryFilters: Record<string, { op: string; value: QueryParamValue }> = {}

        Object.entries(filters.value).forEach(([field, filter]) => {
            if (!hasFilterValue(filter.value)) {
                return
            }

            queryFilters[field] = {
                op: filter.op,
                value: normalizeFilterValueForQuery(filter.value),
            }
        })

        return queryFilters
    }

    function buildQueryData(): PaginatedDataQueryParams {
        const nextSort = serializeSort(sorting.value)

        const queryData: PaginatedDataQueryParams = {
            page: pagination.value.page,
            perPage: pagination.value.perPage,
            filter: buildFilterQueryData(),
        }

        if (nextSort) {
            queryData.sort = nextSort
        }

        return queryData
    }

    function getOnlyProps(): string[] {
        const base = Array.isArray(propDataToFetch)
            ? propDataToFetch
            : [propDataToFetch]

        return [...base, 'queryParams']
    }

    function fetchData(options: InertiaRouterFetchCallbacks = {}): Promise<unknown> {
        const { onSuccess, onError, onFinish } = options

        return new Promise((resolve, reject) => {
            processing.value = true

            router.visit(window.location.pathname, {
                method: 'get',
                data: buildQueryData() as any,
                preserveState: true,
                preserveUrl: false,
                replace: true,
                showProgress,
                only: getOnlyProps(),
                onSuccess: (visitPage) => {
                    onSuccess?.(visitPage)
                    resolve(visitPage)
                },
                onError: (errors) => {
                    onError?.(errors)
                    reject(errors)
                },
                onFinish: () => {
                    processing.value = false
                    onFinish?.()
                },
            })
        })
    }

    function paginate(pageNumber: number, perPage?: number): Promise<unknown> {
        pagination.value.page = pageNumber

        if (typeof perPage === 'number' && perPage > 0) {
            if (pagination.value.perPage !== perPage) {
                pagination.value.page = 1
            }

            pagination.value.perPage = perPage
        }

        return fetchData({
            onFinish: () => {
                scrollToTop()
            },
        })
    }

    function applyFilters(options: InertiaRouterFetchCallbacks = {}): Promise<unknown> {
        const { onSuccess, onError, onFinish } = options

        pagination.value.page = 1

        return fetchData({
            onSuccess,
            onError,
            onFinish: () => {
                scrollToTop()
                onFinish?.()
            },
        })
    }

    function setSorting(nextSorting: SortingState, options: InertiaRouterFetchCallbacks = {}): Promise<unknown> {
        sorting.value = structuredClone(nextSorting)
        pagination.value.page = 1

        return fetchData(options)
    }

    function toggleSort(field: string): Promise<unknown> {
        const current = sorting.value.find(entry => entry.id === field)
        let nextSorting: SortingState = []

        if (!current) {
            nextSorting = [{ id: field, desc: false }]
        } else if (!current.desc) {
            nextSorting = [{ id: field, desc: true }]
        }

        return setSorting(nextSorting)
    }

    function reset(options: InertiaRouterFetchCallbacks = {}): Promise<unknown> {
        filters.value = cloneFilters(initialFilters)
        sorting.value = structuredClone(initialSorting)
        pagination.value = {
            page: 1,
            perPage: initialPerPage,
        }

        return fetchData(options)
    }

    function hardReset(options: InertiaRouterFetchCallbacks = {}): Promise<unknown> {
        const { onSuccess, onError, onFinish } = options

        return new Promise((resolve, reject) => {
            processing.value = true

            router.visit(window.location.pathname, {
                method: 'get',
                preserveUrl: false,
                replace: true,
                showProgress,
                only: getOnlyProps(),
                onSuccess: (visitPage) => {
                    onSuccess?.(visitPage)
                    resolve(visitPage)
                },
                onError: (errors) => {
                    onError?.(errors)
                    reject(errors)
                },
                onFinish: () => {
                    processing.value = false
                    onFinish?.()
                },
            })
        })
    }

    function parseUrlParams(): void {
        const queryParams = page.props.queryParams ?? {}
        const nextFilters = cloneFilters(initialFilters)

        if (queryParams.filter && typeof queryParams.filter === 'object' && !Array.isArray(queryParams.filter)) {
            Object.entries(queryParams.filter).forEach(([field, queryFilter]) => {
                if (!queryFilter || typeof queryFilter !== 'object' || Array.isArray(queryFilter)) {
                    return
                }

                if (!(field in nextFilters)) {
                    return
                }

                const existingFilter = nextFilters[field]
                const op = typeof queryFilter.op === 'string'
                    ? queryFilter.op
                    : existingFilter.op
                const value = queryFilter.value !== undefined
                    ? parseValueFromQuery(queryFilter.value)
                    : existingFilter.value

                nextFilters[field] = {
                    op,
                    value,
                } as FilterStateItem
            })
        }

        filters.value = nextFilters
        sorting.value = parseSort(typeof queryParams.sort === 'string' ? queryParams.sort : undefined)

        const nextPage = Number(queryParams.page ?? 1)
        const nextPerPage = Number(queryParams.perPage ?? initialPerPage)

        pagination.value.page = Number.isNaN(nextPage) || nextPage < 1 ? 1 : nextPage
        pagination.value.perPage = Number.isNaN(nextPerPage) || nextPerPage < 1 ? initialPerPage : nextPerPage
    }

    function debounceInputFilter(callback: () => void, wait = 300): void {
        if (debounceTimeout.value) {
            clearTimeout(debounceTimeout.value)
        }

        debounceTimeout.value = setTimeout(() => {
            callback()
        }, wait)
    }

    onMounted(() => {
        parseUrlParams()
    })

    return {
        processing,
        filters,
        sorting,
        pagination,
        filteredOrSorted,
        firstDatasetIndex,
        sortModel,
        textFilterModel,
        numberFilterModel,
        multiSelectFilterModel,
        dateFilterModel,
        dateRangeFilterModel,
        filterModeModel,
        setFilterMode,
        sortIcon,
        toggleSort,
        fetchData,
        paginate,
        applyFilters,
        setSorting,
        reset,
        hardReset,
        parseUrlParams,
        debounceInputFilter,
        scrollToTop,
    }
}
