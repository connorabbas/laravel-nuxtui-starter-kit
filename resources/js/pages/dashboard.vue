<script setup lang="ts">
import AppLayout from '@/layouts/app/index.vue'
import { Head as InertiaHead } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })
</script>

<template>
    <InertiaHead title="Dashboard" />

    <UContainer class="w-full">
        <UPage>
            <UPageHeader
                title="Dashboard"
                description="This is your authenticated application area."
            />

            <UPageBody>
                <UPageCard
                    title="Welcome to the Laravel + NuxtUI Starter Kit"
                    description="You are logged in!"
                />
            </UPageBody>
        </UPage>
    </UContainer>
</template>
