<script setup lang="ts">
import AppLayout from '@/layouts/app/index.vue'
import { Head as InertiaHead } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })
</script>

<template>
    <InertiaHead title="Dashboard" />

    <UContainer class="flex flex-col gap-4 sm:gap-6 w-full">
        <UPageHeader
            title="Dashboard"
            description="This is your authenticated application area."
        />
    </UContainer>
</template>
