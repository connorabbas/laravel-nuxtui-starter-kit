export function formatDate(date: string, options: Intl.DateTimeFormatOptions = { dateStyle: 'medium' }): string {
    return new Intl.DateTimeFormat(undefined, options).format(new Date(date))
}
