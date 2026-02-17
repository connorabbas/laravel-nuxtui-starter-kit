/**
 * @see https://github.com/nuxt/ui/issues/4202
 */

import type { App } from 'vue'
import { type Config, type RouteParams, type Router, route as ziggyRoute } from 'ziggy-js'

const USE_RELATIVE_ZIGGY_ROUTE_WORKAROUND = import.meta.env.VITE_ZIGGY_RELATIVE_LINKS_WORKAROUND !== 'false'

export type ZiggyRoute = typeof ziggyRoute

export function createZiggyRoute(ziggyConfig: Config): ZiggyRoute {
    function route(): Router
    function route(name: string, params?: RouteParams<string>, absolute?: boolean, config?: Config): string
    function route(name?: string, params?: RouteParams<string>, absolute?: boolean, config?: Config): Router | string {
        if (!name) {
            return ziggyRoute(undefined, undefined, absolute, config ?? ziggyConfig)
        }

        if (!USE_RELATIVE_ZIGGY_ROUTE_WORKAROUND) {
            return ziggyRoute(name, params, absolute, config ?? ziggyConfig)
        }

        return ziggyRoute(name, params, absolute ?? false, config ?? ziggyConfig)
    }

    return route as ZiggyRoute
}

export function installZiggyRoute(app: App, route: ZiggyRoute): void {
    app.config.globalProperties.route = route

    if (typeof window !== 'undefined') {
        ; (window as typeof window & { route: ZiggyRoute }).route = route
    }

    if (typeof window === 'undefined') {
        ; (global as typeof global & { route: ZiggyRoute }).route = route
    }
}
