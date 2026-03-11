<script setup lang="ts">
import { Head as IHead, useForm } from '@inertiajs/vue3'
import { useToast } from '@nuxt/ui/composables'
import { nextTick, ref, useTemplateRef } from 'vue'

import SettingsLayout from '@/layouts/settings.vue'

const showCurrentPassword = ref(false)
const showNewPassword = ref(false)
const showPasswordConfirmation = ref(false)

const currentPasswordInput = useTemplateRef('currentPasswordInput')
const newPasswordInput = useTemplateRef('newPasswordInput')
const passwordConfirmationInput = useTemplateRef('passwordConfirmationInput')

const toast = useToast()

const updatePasswordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

function submit(): void {
    updatePasswordForm.put(route('settings.password.update'), {
        errorBag: 'updatePassword',
        onSuccess: () => {
            updatePasswordForm.reset('current_password', 'password', 'password_confirmation')
            toast.add({
                color: 'success',
                title: 'Success',
                description: 'Your password has been updated.',
                icon: 'i-lucide-circle-check'
            })
        },
        onError: async () => {
            if (updatePasswordForm.errors.password) {
                updatePasswordForm.reset('password', 'password_confirmation')
                await nextTick()
                newPasswordInput.value?.inputRef?.focus()
            }
            if (updatePasswordForm.errors.current_password) {
                updatePasswordForm.reset('current_password')
                await nextTick()
                currentPasswordInput.value?.inputRef?.focus()
            }
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
                        ref="currentPasswordInput"
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
                        ref="newPasswordInput"
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
                        ref="passwordConfirmationInput"
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
