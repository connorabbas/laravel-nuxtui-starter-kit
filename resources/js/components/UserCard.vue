<script setup lang="ts">
import { formatDate } from '@/utils/date'

defineProps<{
    user: App.Data.UserData
    timeZone: string
}>()
</script>

<template>
    <UPageCard
        :title="user.name"
        :description="user.email"
        variant="outline"
        class="min-w-0"
        :ui="{
            container: 'min-w-0',
            wrapper: 'min-w-0',
            body: 'min-w-0',
            title: 'break-words',
            description: 'break-all'
        }"
    >
        <div class="min-w-0 space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <UBadge
                    :label="user.emailVerifiedAt ? 'Verified' : 'Unverified'"
                    :color="user.emailVerifiedAt ? 'success' : 'warning'"
                    variant="subtle"
                />

                <UBadge
                    color="neutral"
                    variant="subtle"
                    :label="`Joined ${formatDate(user.createdAt, timeZone)}`"
                />
            </div>

            <p class="break-words text-sm text-muted">
                User #{{ user.id }} was last updated {{ formatDate(user.updatedAt, timeZone) }}.
            </p>
        </div>
    </UPageCard>
</template>
