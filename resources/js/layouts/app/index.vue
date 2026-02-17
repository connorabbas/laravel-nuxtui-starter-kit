<script setup lang="ts">
import AppLayout from './sidebar.vue'
</script>

<template>
    <UApp>
        <AppLayout>
            <slot />
        </AppLayout>
    </UApp>
</template>
