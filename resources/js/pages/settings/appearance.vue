<script setup lang="ts">
import { Head as IHead } from '@inertiajs/vue3'
import SettingsLayout from '@/layouts/settings.vue'
</script>

<template>
    <SettingsLayout
        page-title="Appearance"
        title="Appearance"
        description="Choose how the interface looks for your preference."
    >
        <IHead title="Settings - Appearance" />

        <UCard class="max-w-xl">
            <template #header>
                <h2 class="text-base font-semibold">
                    Color mode
                </h2>
            </template>

            <div class="max-w-xs">
                <UColorModeSelect class="w-full" />
            </div>
        </UCard>
    </SettingsLayout>
</template>
