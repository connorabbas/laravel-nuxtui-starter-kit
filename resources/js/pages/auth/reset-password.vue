<script setup lang="ts">
import { Head as IHead, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import AuthLayout from '@/layouts/auth.vue'

const props = defineProps<{
    token: string
    email: string
}>()

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const resetPasswordForm = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = (): void => {
    resetPasswordForm.post(route('password.update'), {
        onSuccess: () => {
            resetPasswordForm.reset('password', 'password_confirmation')
        },
    })
}
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <IHead title="Reset password" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Set a new password
                </h1>
                <p class="text-muted text-sm">
                    Choose a secure password for your account.
                </p>
            </div>

            <form
                class="space-y-5"
                @submit.prevent="submit"
            >
                <UFormField
                    name="email"
                    label="Email address"
                    required
                    :error="resetPasswordForm.errors?.email"
                >
                    <UInput
                        id="email"
                        v-model="resetPasswordForm.email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    name="password"
                    label="Password"
                    required
                    :error="resetPasswordForm.errors?.password"
                >
                    <UInput
                        id="password"
                        v-model="resetPasswordForm.password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="New password"
                        autocomplete="new-password"
                        autofocus
                        :ui="{ trailing: 'pe-1' }"
                        class="w-full"
                    >
                        <template #trailing>
                            <UButton
                                color="neutral"
                                variant="link"
                                size="sm"
                                :icon="showPassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                :aria-pressed="showPassword"
                                aria-controls="password"
                                @click="showPassword = !showPassword"
                            />
                        </template>
                    </UInput>
                </UFormField>

                <UFormField
                    name="password_confirmation"
                    label="Confirm password"
                    required
                    :error="resetPasswordForm.errors?.password_confirmation"
                >
                    <UInput
                        id="password_confirmation"
                        v-model="resetPasswordForm.password_confirmation"
                        name="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        placeholder="Confirm new password"
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

                <UButton
                    type="submit"
                    block
                    :loading="resetPasswordForm.processing"
                    :disabled="resetPasswordForm.processing"
                >
                    Reset password
                </UButton>
            </form>
        </div>
    </AuthLayout>
</template>
