<script setup lang="ts">
import SettingsLayout from '@/layouts/Settings.vue'
</script>

<template>
    <SettingsLayout
        title="Appearance"
        description="Choose how the interface looks for your preference."
    >
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
