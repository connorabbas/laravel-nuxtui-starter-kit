import type { DropdownMenuItem, NavigationMenuItem } from '@nuxt/ui'
import type { SharedData } from '@/types'
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useAppLayout() {
    const page = usePage<SharedData>()

    const currentPath = computed(() => page.url.split('?')[0])

    const currentRoute = computed(() => {
        // Access page.url to trigger re-computation on navigation.
        /* eslint-disable @typescript-eslint/no-unused-vars */
        const url = page.url
        /* eslint-enable @typescript-eslint/no-unused-vars */
        return route().current()
    })

    const subPageNavItems = computed<NavigationMenuItem[] | undefined>(() => {
        return undefined
        return [
            {
                label: 'Overview',
                icon: 'i-lucide-layout-grid',
                to: route('dashboard'),
                active: currentRoute.value === 'dashboard',
            },
            {
                label: 'Profile',
                icon: 'i-lucide-user-round',
                to: route('profile.edit'),
                active: currentRoute.value === 'profile.edit',
            },
            {
                label: 'Password',
                icon: 'i-lucide-key-round',
                to: route('user-password.edit'),
                active: currentRoute.value === 'user-password.edit',
            },
            {
                label: 'Two-Factor',
                icon: 'i-lucide-shield-check',
                to: route('two-factor.show'),
                active: currentRoute.value === 'two-factor.show',
            },
            {
                label: 'Appearance',
                icon: 'i-lucide-palette',
                to: route('appearance.edit'),
                active: currentRoute.value === 'appearance.edit',
            },
        ]
    })

    const appName = computed(() => page.props.name)

    const navMenuItems = computed<NavigationMenuItem[][]>(() => {
        return [
            [
                {
                    label: 'Home',
                    icon: 'i-lucide-house',
                    to: route('index'),
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
        const items: DropdownMenuItem[][] = []

        if (route().has('profile.edit')) {
            items.push([
                {
                    label: 'Settings',
                    icon: 'i-lucide-settings',
                    to: route('profile.edit')
                }
            ])
        }

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
