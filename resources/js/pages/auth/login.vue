<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/auth.vue'

defineOptions({ layout: AuthLayout })

defineProps<{
    status?: string
    canResetPassword: boolean
    canRegister: boolean
}>()
</script>

<template>
    <div class="space-y-6">
        <Head title="Log in" />

        <div class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">
                Welcome back
            </h1>
            <p class="text-muted text-sm">
                Sign in to your account to continue.
            </p>
        </div>

        <UAlert
            v-if="status"
            color="success"
            variant="subtle"
            icon="i-lucide-circle-check"
            :description="status"
        />

        <Form
            v-slot="{ errors, processing }"
            :action="route('login.store')"
            method="post"
            :reset-on-success="['password']"
            class="space-y-5"
        >
            <UFormField
                name="email"
                label="Email address"
                required
                :error="errors.email"
            >
                <UInput
                    id="email"
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
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-primary text-sm font-medium hover:underline"
                    >
                        Forgot password?
                    </Link>
                </div>

                <UFormField
                    name="password"
                    required
                    :error="errors.password"
                >
                    <UInput
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Password"
                        autocomplete="current-password"
                        class="w-full"
                    />
                </UFormField>
            </div>

            <div class="flex items-center gap-2">
                <UCheckbox
                    id="remember"
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
                :loading="processing"
                :disabled="processing"
            >
                Log in
            </UButton>
        </Form>

        <p
            v-if="canRegister"
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
</template>
