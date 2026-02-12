import type { DropdownMenuItem, NavigationMenuItem } from '@nuxt/ui'
import type { SharedData } from '@/types'
import { router, usePage } from '@inertiajs/vue3'
import { ref, computed } from 'vue'

export function useAppLayout() {
    // TODO: globally inferred type for page props
    const page = usePage<SharedData>()

    const currentRoute = computed(() => {
        const url = page.url

        return route().current() ?? url
    })

    const subPageNavItems = ref<NavigationMenuItem[] | undefined>([
        {
            label: 'Test 123 123',
            to: route('index', {}, false),
            active: currentRoute.value === 'index'
        },
        {
            label: 'Dashboard',
            to: route('dashboard', {}, false),
            active: currentRoute.value === 'dashboard'
        }
    ])

    const appName = computed(() => page.props.name)

    const navMenuItems = computed<NavigationMenuItem[][]>(() => {
        return [
            [
                {
                    label: 'Home',
                    icon: 'i-lucide-house',
                    to: route('index', {}, false),
                    active: currentRoute.value === 'index'
                },
                {
                    label: 'Dashboard',
                    icon: 'i-lucide-layout-dashboard',
                    to: route('dashboard', {}, false),
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
        appName,
        subPageNavItems,
        navMenuItems,
        userMenuItems,
        user
    }
}
