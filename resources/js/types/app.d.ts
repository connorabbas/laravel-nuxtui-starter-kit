export type FlashProps = Partial<Record<`${string}_alert` | `${string}_toast`, string | null>>

export interface AuthProps {
    user: App.Data.UserData | null
}

export interface ConfigProps {
    appName: string
    timezone: string
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    config: ConfigProps
    currentRouteName: string | null
    auth: AuthProps
    queryParams: Record<string, string | string[]>
}
