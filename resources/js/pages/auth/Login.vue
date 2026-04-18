<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

import AuthLayout from '@/layouts/Auth.vue'
import { route } from '@/utils/route'

const props = defineProps<{
    status?: string
    canResetPassword: boolean
    canRegister: boolean
}>()

const showPassword = ref(false)

const loginForm = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit(): void {
    loginForm.post(route('login.store'), {
        onSuccess: () => {
            loginForm.reset('password')
        },
        onError: (errors) => {
            if (errors.password) {
                loginForm.reset('password')
            }
        },
    })
}
</script>

<template>
    <AuthLayout
        title="Log in"
        description="Sign in to your account to continue."
    >
        <div class="space-y-6">
            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Welcome back
                </h1>
                <p class="text-muted text-sm">
                    Sign in to your account to continue.
                </p>
            </div>

            <UAlert
                v-if="props.status"
                color="success"
                icon="i-lucide-circle-check"
                :description="props.status"
            />

            <form
                class="space-y-5"
                @submit.prevent="submit"
            >
                <UFormField
                    name="email"
                    label="Email address"
                    required
                    :error="loginForm.errors?.email"
                >
                    <UInput
                        id="email"
                        v-model="loginForm.email"
                        name="email"
                        type="email"
                        placeholder="email@example.com"
                        autocomplete="email"
                        autofocus
                        class="w-full"
                    />
                </UFormField>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label
                            for="password"
                            class="text-sm font-medium"
                        >Password</label>
                        <Link
                            v-if="props.canResetPassword"
                            :href="route('password.request')"
                            class="text-primary text-sm font-medium hover:underline"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <UFormField
                        name="password"
                        required
                        :error="loginForm.errors?.password"
                    >
                        <UInput
                            id="password"
                            v-model="loginForm.password"
                            name="password"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Password"
                            autocomplete="current-password"
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
                </div>

                <div class="flex items-center gap-2">
                    <UCheckbox
                        id="remember"
                        v-model="loginForm.remember"
                        name="remember"
                    />
                    <label
                        for="remember"
                        class="text-toned text-sm"
                    >Remember me</label>
                </div>

                <UButton
                    type="submit"
                    block
                    :loading="loginForm.processing"
                    :disabled="loginForm.processing"
                >
                    Log in
                </UButton>
            </form>

            <p
                v-if="props.canRegister"
                class="text-toned text-center text-sm"
            >
                Don't have an account?
                <Link
                    :href="route('register')"
                    class="text-primary font-medium hover:underline"
                >
                    Create one
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>
