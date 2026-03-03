import type { Config } from 'ziggy-js'

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
    accounts_count?: number;
    accounts_sum_balance?: number | string;
    accounts_max_opened_at?: string | null;
    accounts?: Array<{
        id: number;
        name: string;
        provider: string | null;
        status: string;
        balance: number | string;
        opened_at: string | null;
    }>;
}

export interface AuthProps {
    user: User;
}

export interface FlashProps {
    success?: string | null;
    info?: string | null;
    warning?: string | null;
    error?: string | null;
    neutral?: string | null;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    ziggy: Config & { location: string };
    auth: AuthProps;
    flash: FlashProps;
    queryParams: Record<string, string | string[]>;
}

export interface ErrorResponsePayload {
    status: number
    error_title: string
    error_summary: string
    error_detail: string
    error_icon: string
    error_color: string
}
