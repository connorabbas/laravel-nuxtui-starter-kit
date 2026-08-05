export type FlashProps = Partial<Record<`${string}_alert` | `${string}_toast`, string | null>>

export interface AuthProps {
    user: App.Data.UserData | null;
}

export interface ConfigProps {
    appName: string
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: AuthProps;
    config: ConfigProps
    currentRouteName: string | null;
    queryParams: Record<string, string | string[]>;
}
