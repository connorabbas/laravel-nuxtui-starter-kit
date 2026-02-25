<script setup lang="ts">
import { Head as IHead, Link as ILink } from '@inertiajs/vue3'

const props = defineProps<{
    title: string
    detail: string
    status: number
    homepageRoute: string
}>()
</script>

<template>
    <IHead title="Error" />

    <UError
        :clear="false"
        :error="{
            statusCode: props.status,
            statusMessage: props.title,
            message: props.detail
        }"
    >
        <template #links>
            <ILink :href="props.homepageRoute">
                <UButton size="lg">
                    Back to home
                </UButton>
            </ILink>
        </template>
    </UError>
</template>
