import { PageProps as InertiaPageProps } from '@inertiajs/core'
import { route as routeFn } from 'ziggy-js'
import { AppPageProps } from './'
declare global {
    const route: typeof routeFn
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps { }
    export interface InertiaConfig {
        errorValueType: string // set to string[] if passing all errors: https://inertiajs.com/docs/v3/the-basics/validation#multiple-errors-per-field
    }
}
