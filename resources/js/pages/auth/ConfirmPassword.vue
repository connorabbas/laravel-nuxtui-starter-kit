<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import AuthLayout from '@/layouts/Auth.vue'

const showPassword = ref(false)

const confirmPasswordForm = useForm({
    password: '',
})

function submit(): void {
    confirmPasswordForm.post(route('password.confirm.store'), {
        onSuccess: () => {
            confirmPasswordForm.reset()
        },
        onError: (errors) => {
            if (errors.password) {
                confirmPasswordForm.reset('password')
            }
        },
    })
}
</script>

<template>
    <AuthLayout
        title="Confirm password"
        description="Confirm your password to continue to this secure area of the application."
    >
        <div class="space-y-6">
            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Confirm your password
                </h1>
                <p class="text-muted text-sm">
                    This is a secure area. Please confirm your password to continue.
                </p>
            </div>

            <form
                class="space-y-5"
                @submit.prevent="submit"
            >
                <UFormField
                    name="password"
                    label="Password"
                    required
                    :error="confirmPasswordForm.errors?.password"
                >
                    <UInput
                        id="password"
                        v-model="confirmPasswordForm.password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Password"
                        autocomplete="current-password"
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

                <UButton
                    type="submit"
                    block
                    :loading="confirmPasswordForm.processing"
                    :disabled="confirmPasswordForm.processing"
                >
                    Confirm password
                </UButton>
            </form>
        </div>
    </AuthLayout>
</template>
