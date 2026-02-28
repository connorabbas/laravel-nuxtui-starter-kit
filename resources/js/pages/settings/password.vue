<script setup lang="ts">
import { Head as IHead, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import SettingsLayout from '@/layouts/settings.vue'

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showPasswordConfirmation = ref(false)

const updatePasswordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const submit = (): void => {
    updatePasswordForm.put(route('settings.password.update'), {
        errorBag: 'updatePassword',
        onSuccess: () => {
            updatePasswordForm.reset('current_password', 'password', 'password_confirmation')
        },
        onError: () => {
            updatePasswordForm.reset('current_password', 'password', 'password_confirmation')
        },
    })
}
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

            <form
                class="space-y-4"
                @submit.prevent="submit"
            >
                <UFormField
                    name="current_password"
                    label="Current password"
                    required
                    :error="updatePasswordForm.errors?.current_password"
                >
                    <UInput
                        id="current_password"
                        v-model="updatePasswordForm.current_password"
                        name="current_password"
                        :type="showCurrentPassword ? 'text' : 'password'"
                        autocomplete="current-password"
                        :ui="{ trailing: 'pe-1' }"
                        class="w-full"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="showCurrentPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="showCurrentPassword ? 'Hide password' : 'Show password'"
                                :aria-pressed="showCurrentPassword"
                                aria-controls="current_password"
                                @click="showCurrentPassword = !showCurrentPassword"
                            />
                        </template>
                    </UInput>
                </UFormField>

                <UFormField
                    name="password"
                    label="New password"
                    required
                    :error="updatePasswordForm.errors?.password"
                >
                    <UInput
                        id="password"
                        v-model="updatePasswordForm.password"
                        name="password"
                        :type="showNewPassword ? 'text' : 'password'"
                        autocomplete="new-password"
                        :ui="{ trailing: 'pe-1' }"
                        class="w-full"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="showNewPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="showNewPassword ? 'Hide password' : 'Show password'"
                                :aria-pressed="showNewPassword"
                                aria-controls="password"
                                @click="showNewPassword = !showNewPassword"
                            />
                        </template>
                    </UInput>
                </UFormField>

                <UFormField
                    name="password_confirmation"
                    label="Confirm new password"
                    required
                    :error="updatePasswordForm.errors?.password_confirmation"
                >
                    <UInput
                        id="password_confirmation"
                        v-model="updatePasswordForm.password_confirmation"
                        name="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        autocomplete="new-password"
                        :ui="{ trailing: 'pe-1' }"
                        class="w-full"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="showPasswordConfirmation ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="showPasswordConfirmation ? 'Hide password' : 'Show password'"
                                :aria-pressed="showPasswordConfirmation"
                                aria-controls="password_confirmation"
                                @click="showPasswordConfirmation = !showPasswordConfirmation"
                            />
                        </template>
                    </UInput>
                </UFormField>

                <div class="flex items-center gap-3">
                    <UButton
                        type="submit"
                        :loading="updatePasswordForm.processing"
                        :disabled="updatePasswordForm.processing"
                    >
                        Save password
                    </UButton>

                    <span
                        v-if="updatePasswordForm.recentlySuccessful"
                        class="text-muted text-sm"
                    >Saved.</span>
                </div>
            </form>
        </UCard>
    </SettingsLayout>
</template>
