export function stringOrNull(value: string): string | null {
    const stringValue = value.trim()

    return stringValue === '' ? null : stringValue
}
