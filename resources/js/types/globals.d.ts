import { PageProps as InertiaPageProps } from '@inertiajs/core'
import type { SharedPageProps, FlashProps } from './'

// Extend ImportMeta interface for Vite...
declare global {
    interface ImportMetaEnv {
        readonly VITE_APP_NAME: string
    }
}

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, SharedPageProps { }
    export interface InertiaConfig {
        errorValueType: string
        flashDataType: FlashProps
        sharedPageProps: SharedPageProps
    }
}
