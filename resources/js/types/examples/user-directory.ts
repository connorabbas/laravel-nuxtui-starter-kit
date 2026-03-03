import type { FilterMatchMode } from '@/composables/usePaginatedData'

export interface UserDirectoryFilterDefinition {
    type: 'string' | 'number' | 'date' | 'boolean';
    modes: Array<{
        label: string;
        value: FilterMatchMode;
    }>;
}
