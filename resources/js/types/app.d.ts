export interface AuthProps {
    user: App.Data.UserData | null;
}

export interface FlashProps {
    success?: string | null;
    info?: string | null;
    warning?: string | null;
    error?: string | null;
    neutral?: string | null;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    currentRouteName: string | null;
    auth: AuthProps;
    flash: FlashProps;
    queryParams: Record<string, string | string[]>;
}
