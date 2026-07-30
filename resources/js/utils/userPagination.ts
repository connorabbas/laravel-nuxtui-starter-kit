import type { TableQuerySortingConfig } from '@/composables/useTableQuerySorting'

export const userSortItems = [
    { label: 'Newest', value: 'newest' },
    { label: 'Oldest', value: 'oldest' },
    { label: 'Name - A to Z', value: 'name_asc' },
    { label: 'Name - Z to A', value: 'name_desc' },
    { label: 'Email - A to Z', value: 'email_asc' },
    { label: 'Email - Z to A', value: 'email_desc' }
] satisfies Array<{ label: string, value: App.Enums.UserSort }>

export const verifiedFilterItems = [
    { label: 'Any', value: null },
    { label: 'Verified', value: true },
    { label: 'Unverified', value: false }
]

export const userTableSorting: TableQuerySortingConfig<App.Enums.UserSort> = {
    default: 'newest',
    columns: {
        createdAt: { asc: 'oldest', desc: 'newest' },
        name: { asc: 'name_asc', desc: 'name_desc' },
        email: { asc: 'email_asc', desc: 'email_desc' }
    }
}
