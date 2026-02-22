<script setup lang="ts">
import { Form, Head as IHead } from '@inertiajs/vue3'
import SettingsLayout from '@/layouts/settings.vue'
</script>

<template>
    <SettingsLayout
        page-title="Password"
        title="Password"
        description="Update your password to keep your account secure."
    >
        <IHead title="Settings - Password" />

        <UCard class="max-w-xl">
            <template #header>
                <h2 class="text-base font-semibold">
                    Update password
                </h2>
            </template>

            <Form
                v-slot="{ errors, processing, recentlySuccessful }"
                :action="route('settings.password.update')"
                error-bag="updatePassword"
                method="put"
                :reset-on-error="['password', 'password_confirmation', 'current_password']"
                :reset-on-success="['password', 'password_confirmation', 'current_password']"
                class="space-y-4"
            >
                <UFormField
                    name="current_password"
                    label="Current password"
                    required
                    :error="errors.current_password"
                >
                    <UInput
                        id="current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    name="password"
                    label="New password"
                    required
                    :error="errors.password"
                >
                    <UInput
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    name="password_confirmation"
                    label="Confirm new password"
                    required
                    :error="errors.password_confirmation"
                >
                    <UInput
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        class="w-full"
                    />
                </UFormField>

                <div class="flex items-center gap-3">
                    <UButton
                        type="submit"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Save password
                    </UButton>

                    <span
                        v-if="recentlySuccessful"
                        class="text-muted text-sm"
                    >Saved.</span>
                </div>
            </Form>
        </UCard>
    </SettingsLayout>
</template>
