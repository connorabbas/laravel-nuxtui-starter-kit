export interface AuthProps {
    user: App.Data.UserData | null;
}

export type FlashProps = Partial<Record<`${string}_alert` | `${string}_toast`, string | null>>

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    currentRouteName: string | null;
    auth: AuthProps;
    queryParams: Record<string, string | string[]>;
}
