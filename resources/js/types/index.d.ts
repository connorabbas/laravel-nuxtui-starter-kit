import type { PageProps } from '@inertiajs/core'
import type { Config } from 'ziggy-js'

export interface Auth {
    user: User | null
}

export interface ErrorResponsePayload {
    status: number
    error_title: string
    error_summary: string
    error_detail: string
    error_icon: string
    error_color: string
}

export interface SharedData extends PageProps {
    name: string
    quote: { message: string; author: string }
    auth: Auth
    ziggy: Config & { location: string }
    sidebarOpen: boolean
}

export interface User {
    id: number
    name: string
    email: string
    avatar?: string
    email_verified_at: string | null
    created_at: string
    updated_at: string
}
