import type { DropdownMenuItem, NavigationMenuItem } from '@nuxt/ui'
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from '@/utils/route'

export function useAppLayout() {
    const page = usePage()

    const currentPath = computed(() => page.url.split('?')[0])

    const currentRoute = computed(() => page.props.currentRouteName)

    const subPageNavItems = computed<NavigationMenuItem[] | undefined>(() => {
        return undefined
    })

    const appName = computed(() => page.props.name)

    const navMenuItems = computed<NavigationMenuItem[][]>(() => {
        return [
            [
                {
                    label: 'Paginator',
                    to: route('examples.paginator.users'),
                    active: currentRoute.value === 'examples.paginator.users'
                },
                {
                    label: 'Table',
                    to: route('examples.table.users'),
                    active: currentRoute.value === 'examples.table.users'
                },
                {
                    label: 'Home',
                    icon: 'i-lucide-house',
                    to: '/',
                    active: currentRoute.value === 'index'
                },
                {
                    label: 'Dashboard',
                    icon: 'i-lucide-layout-dashboard',
                    to: route('dashboard'),
                    active: currentRoute.value === 'dashboard'
                }
            ],
            [
                {
                    label: 'Laravel Docs',
                    icon: 'i-lucide-book-open',
                    to: 'https://laravel.com/docs/12.x',
                    target: '_blank'
                }
            ]
        ]
    })

    const userMenuItems = computed<DropdownMenuItem[][]>(() => {
        const items: DropdownMenuItem[][] = [[
            {
                label: 'Settings',
                icon: 'i-lucide-settings',
                to: route('profile.edit')
            }
        ]]

        items.push([
            {
                label: 'Log out',
                icon: 'i-lucide-log-out',
                onSelect: () => {
                    router.post(route('logout'))
                }
            }
        ])

        return items
    })

    const user = computed(() => page.props.auth.user)

    return {
        currentRoute,
        currentPath,
        appName,
        subPageNavItems,
        navMenuItems,
        userMenuItems,
        user
    }
}
