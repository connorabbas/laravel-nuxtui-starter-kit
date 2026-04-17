<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

import AppHead from '@/components/AppHead.vue'

const props = defineProps<{
    title: string
    detail: string
    status: number
    homepageRoute: string
}>()
</script>

<template>
    <div>
        <AppHead
            :title="props.title"
            :description="props.detail"
        />

        <UError
            :clear="false"
            :error="{
                statusCode: props.status,
                statusMessage: props.title,
                message: props.detail
            }"
        >
            <template #links>
                <Link :href="props.homepageRoute">
                    <UButton size="lg">
                        Back to home
                    </UButton>
                </Link>
            </template>
        </UError>
    </div>
</template>
