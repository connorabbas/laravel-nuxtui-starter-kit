export function nullableString(value: string | number | null | undefined): string | null {
    if (value === null || value === undefined) {
        return null
    }

    const stringValue = String(value).trim()

    return stringValue === '' ? null : stringValue
}

export function nullableArray<TValue>(value: TValue[] | null | undefined): TValue[] | null {
    return value && value.length > 0 ? value : null
}
