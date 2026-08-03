import type { LengthAwarePaginator } from '@/types'
import { router } from '@inertiajs/vue3'
import { computed, reactive, ref, type MaybeRefOrGetter, toRaw, toValue, watch } from 'vue'

type QueryScalar = string | number | boolean | null | undefined
type QueryValue = QueryScalar | QueryScalar[]
type QueryRecord = Record<string, QueryValue>
type SerializedQueryValue = string | number | Array<string | number> | null | undefined
type SerializedQueryRecord = Record<string, SerializedQueryValue>

type PaginatedQuery = {
    page: number
    perPage: number
}

type UsePaginatedQueryOptions<TQuery extends PaginatedQuery, TItem> = {
    route: string
    serverQuery: MaybeRefOrGetter<TQuery>
    paginator: MaybeRefOrGetter<LengthAwarePaginator<TItem>>
    only: string[]
    scrollTo?: string | HTMLElement | null
}

export function usePaginatedQuery<TQuery extends PaginatedQuery, TItem>(options: UsePaginatedQueryOptions<TQuery, TItem>) {
    const processing = ref(false)
    const query = reactive(cloneQuery(toValue(options.serverQuery))) as TQuery
    const paginator = computed(() => toValue(options.paginator))
    let visitSequence = 0

    const resultSummary = computed(() => ({
        currentPage: paginator.value.current_page,
        firstItem: paginator.value.from,
        lastItem: paginator.value.to,
        lastPage: paginator.value.last_page,
        perPage: paginator.value.per_page,
        total: paginator.value.total
    }))

    const resultText = computed(() => {
        const summary = resultSummary.value

        if (summary.total === 0 || summary.firstItem === null || summary.lastItem === null) {
            return 'No results'
        }

        return `Showing ${summary.firstItem}-${summary.lastItem} of ${summary.total} results · Page ${summary.currentPage} of ${summary.lastPage}`
    })

    watch(() => toValue(options.serverQuery), (nextQuery) => {
        replaceQuery(query, nextQuery)
    }, { deep: true })

    function applyQuery(): void {
        query.page = 1
        visitCurrentQuery({ replace: true })
    }

    function setPage(page: number): void {
        query.page = page
        visitCurrentQuery({ replace: false })
    }

    function setPerPage(perPage: number): void {
        query.perPage = perPage
        query.page = 1
        visitCurrentQuery({ replace: true })
    }

    function visitCurrentQuery({ replace }: { replace: boolean }): void {
        const visitId = ++visitSequence
        const nextQuery = cloneQuery(query)

        processing.value = true

        router.get(options.route, serializeQuery(nextQuery), {
            only: options.only,
            preserveState: true,
            preserveScroll: false,
            replace,
            onFinish: () => {
                if (visitId === visitSequence) {
                    processing.value = false
                }
            },
            onSuccess: () => {
                if (visitId === visitSequence) {
                    replaceQuery(query, toValue(options.serverQuery))
                    scrollToTarget(options.scrollTo)
                }
            },
            onError: () => {
                restoreQuery(visitId)
            },
            onHttpException: () => {
                restoreQuery(visitId)
            },
            onNetworkError: () => {
                restoreQuery(visitId)
            },
            onCancel: () => {
                restoreQuery(visitId)
            }
        })
    }

    function restoreQuery(visitId: number): void {
        if (visitId === visitSequence) {
            replaceQuery(query, toValue(options.serverQuery))
        }
    }

    return {
        applyQuery,
        processing,
        query,
        resultText,
        setPage,
        setPerPage,
    }
}

function cloneQuery<TQuery extends object>(query: TQuery): TQuery {
    return Object.fromEntries(
        Object.entries(toRaw(query)).map(([key, value]) => [key, Array.isArray(value) ? [...value] : value])
    ) as TQuery
}

function replaceQuery<TQuery extends object>(target: TQuery, source: TQuery): void {
    for (const key of Object.keys(target)) {
        delete (target as Record<string, unknown>)[key]
    }

    Object.assign(target, cloneQuery(source))
}

function serializeQuery<TQuery extends object>(query: TQuery): SerializedQueryRecord {
    return Object.fromEntries(
        Object.entries(query as QueryRecord)
            .map(([key, value]) => [key, serializeQueryValue(value)])
            .filter(([, value]) => typeof value === 'string' || typeof value === 'number' || Array.isArray(value))
            .filter(([, value]) => value !== '')
            .filter(([, value]) => !Array.isArray(value) || value.length > 0)
    ) as SerializedQueryRecord
}

function serializeQueryValue(value: QueryValue): SerializedQueryValue {
    if (typeof value === 'boolean') {
        return value ? '1' : '0'
    }

    if (Array.isArray(value)) {
        return value
            .map((item) => typeof item === 'boolean' ? (item ? '1' : '0') : item)
            .filter((item) => typeof item === 'string' || typeof item === 'number')
            .filter((item) => item !== '')
    }

    if (typeof value === 'string' || typeof value === 'number') {
        return value
    }

    return null
}

function scrollToTarget(target?: string | HTMLElement | null): void {
    if (typeof window === 'undefined') {
        return
    }

    if (!target) {
        window.scrollTo({ top: 0, behavior: 'smooth' })

        return
    }

    const element = typeof target === 'string' ? document.querySelector(target) : target

    element?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}
