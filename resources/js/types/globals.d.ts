import { PageProps as InertiaPageProps } from '@inertiajs/core'
import { AppPageProps } from './'

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps { }
    export interface InertiaConfig {
        errorValueType: string // set to string[] if passing all errors: https://inertiajs.com/docs/v3/the-basics/validation#multiple-errors-per-field
    }
}
