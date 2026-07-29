import type { LengthAwarePaginator } from '@/types'
import { router } from '@inertiajs/vue3'
import { computed, reactive, ref, type MaybeRefOrGetter, toValue } from 'vue'

type QueryPrimitive = string | number | null | undefined
type QueryRecord = Record<string, QueryPrimitive>

type UpdateOptions = {
    resetPage?: boolean
    replace?: boolean
}

type UsePaginatedQueryOptions<TQuery extends object, TItem> = {
    route: string
    initialQuery: TQuery
    paginator: MaybeRefOrGetter<LengthAwarePaginator<TItem>>
    only: string[]
    scrollTo?: string | HTMLElement | null
    replace?: boolean
}

export function usePaginatedQuery<TQuery extends object, TItem>(options: UsePaginatedQueryOptions<TQuery, TItem>) {
    const processing = ref(false)
    const query = reactive({ ...options.initialQuery }) as TQuery

    const paginator = computed(() => toValue(options.paginator))

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

    function visit(nextQuery: TQuery, updateOptions: UpdateOptions = {}): void {
        processing.value = true

        router.get(options.route, cleanQuery(nextQuery), {
            only: options.only,
            preserveState: true,
            preserveScroll: false,
            replace: updateOptions.replace ?? options.replace ?? false,
            onFinish: () => {
                processing.value = false
            },
            onSuccess: () => {
                scrollToTarget(options.scrollTo)
            }
        })
    }

    function update(patch: Partial<TQuery>, updateOptions: UpdateOptions = {}): void {
        const nextQuery = {
            ...query,
            ...patch,
            ...(updateOptions.resetPage ? { page: 1 } : {})
        } as TQuery

        Object.assign(query, nextQuery)
        visit(nextQuery, updateOptions)
    }

    function setPage(page: number, updateOptions: UpdateOptions = {}): void {
        update({ page } as unknown as Partial<TQuery>, updateOptions)
    }

    function setPerPage(perPage: number, updateOptions: UpdateOptions = {}): void {
        update({ perPage } as unknown as Partial<TQuery>, {
            resetPage: true,
            replace: updateOptions.replace ?? true
        })
    }

    return {
        paginator,
        processing,
        query,
        resultSummary,
        resultText,
        setPage,
        setPerPage,
        update
    }
}

function cleanQuery<TQuery extends object>(query: TQuery): QueryRecord {
    return Object.fromEntries(
        Object.entries(query as Record<string, unknown>)
            .map(([key, value]) => [key, typeof value === 'boolean' ? (value ? '1' : '0') : value])
            .filter(([, value]) => typeof value === 'string' || typeof value === 'number')
            .filter(([, value]) => value !== '')
    ) as QueryRecord
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
