<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/app/index.vue'
import SettingsLayout from '@/layouts/settings.vue'

defineOptions({ layout: AppLayout })
</script>

<template>
    <div>
        <Head title="Settings - Appearance" />

        <SettingsLayout
            title="Appearance"
            description="Choose how the interface looks for your preference."
        >
            <UCard>
                <template #header>
                    <h2 class="text-base font-semibold">
                        Theme mode
                    </h2>
                </template>

                <div class="max-w-xs">
                    <UColorModeSelect class="w-full" />
                </div>
            </UCard>
        </SettingsLayout>
    </div>
</template>
