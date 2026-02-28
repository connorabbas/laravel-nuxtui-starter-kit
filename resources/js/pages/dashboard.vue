<script setup lang="ts">
import AppLayout from '@/layouts/app/index.vue'
import { Head as IHead } from '@inertiajs/vue3'
</script>

<template>
    <AppLayout page-title="Dashboard">
        <IHead title="Dashboard" />

        <UPage>
            <UPageHeader
                title="Dashboard"
                description="This is your authenticated application area."
            />

            <UPageBody>
                <UPageCard
                    title="Welcome to the Laravel + Nuxt UI Starter Kit"
                    description="You are logged in!"
                />
            </UPageBody>
        </UPage>
    </AppLayout>
</template>
