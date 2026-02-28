<script setup lang="ts">
import { Head as IHead, Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import AuthLayout from '@/layouts/auth.vue'

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)

const registerForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit(): void {
    registerForm.post(route('register.store'), {
        onSuccess: () => {
            registerForm.reset('password', 'password_confirmation')
        },
        onError: (errors) => {
            if (errors.password || errors.password_confirmation) {
                registerForm.reset('password', 'password_confirmation')
            }
        },
    })
}
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <IHead title="Register" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Create an account
                </h1>
                <p class="text-muted text-sm">
                    Enter your details below to create your account
                </p>
            </div>

            <form
                class="space-y-5"
                @submit.prevent="submit"
            >
                <UFormField
                    name="name"
                    label="Name"
                    required
                    :error="registerForm.errors?.name"
                >
                    <UInput
                        id="name"
                        v-model="registerForm.name"
                        name="name"
                        type="text"
                        placeholder="Full name"
                        autocomplete="name"
                        autofocus
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    name="email"
                    label="Email address"
                    required
                    :error="registerForm.errors?.email"
                >
                    <UInput
                        id="email"
                        v-model="registerForm.email"
                        name="email"
                        type="email"
                        placeholder="email@example.com"
                        autocomplete="email"
                        class="w-full"
                    />
                </UFormField>

                <!-- Optionally add a strength indicator: https://ui.nuxt.com/docs/components/input#with-password-strength-indicator -->
                <UFormField
                    name="password"
                    label="Password"
                    required
                    :error="registerForm.errors?.password"
                >
                    <UInput
                        id="password"
                        v-model="registerForm.password"
                        name="password"
                        :type="showPassword ? 'text' : 'password'"
                        placeholder="Password"
                        autocomplete="new-password"
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
                    :error="registerForm.errors?.password_confirmation"
                >
                    <UInput
                        id="password_confirmation"
                        v-model="registerForm.password_confirmation"
                        name="password_confirmation"
                        :type="showPasswordConfirmation ? 'text' : 'password'"
                        placeholder="Confirm password"
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
                    :loading="registerForm.processing"
                    :disabled="registerForm.processing"
                >
                    Create account
                </UButton>
            </form>

            <p class="text-toned text-center text-sm">
                Already registered?
                <Link
                    :href="route('login')"
                    class="text-primary font-medium hover:underline"
                >
                    Log in
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>
