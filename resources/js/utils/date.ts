export function formatDate(date: string, timeZone: string, options: Intl.DateTimeFormatOptions = { dateStyle: 'medium' }): string {
    return new Intl.DateTimeFormat('en-US', {
        timeZone,
        ...options,
    }).format(new Date(date))
}
