<script setup lang="ts">
import AppLayout from '@/layouts/app/Index.vue'
</script>

<template>
    <AppLayout
        title="Dashboard"
        description="This is your authenticated application area."
        page-title="Dashboard"
    >
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
