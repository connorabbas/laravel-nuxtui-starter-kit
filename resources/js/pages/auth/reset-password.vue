<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/auth.vue'

defineOptions({ layout: AuthLayout })

defineProps<{
    token: string
    email: string
}>()
</script>

<template>
    <div class="space-y-6">
        <Head title="Reset password" />

        <div class="space-y-1 text-center">
            <h1 class="text-2xl font-semibold">
                Set a new password
            </h1>
            <p class="text-muted text-sm">
                Choose a secure password for your account.
            </p>
        </div>

        <Form
            v-slot="{ errors, processing }"
            :action="route('password.update')"
            method="post"
            :reset-on-success="['password', 'password_confirmation']"
            class="space-y-5"
        >
            <input
                type="hidden"
                name="token"
                :value="token"
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
                    :value="email"
                    autocomplete="email"
                    class="w-full"
                />
            </UFormField>

            <UFormField
                name="password"
                label="Password"
                required
                :error="errors.password"
            >
                <UInput
                    id="password"
                    name="password"
                    type="password"
                    placeholder="New password"
                    autocomplete="new-password"
                    autofocus
                    class="w-full"
                />
            </UFormField>

            <UFormField
                name="password_confirmation"
                label="Confirm password"
                required
                :error="errors.password_confirmation"
            >
                <UInput
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    placeholder="Confirm new password"
                    autocomplete="new-password"
                    class="w-full"
                />
            </UFormField>

            <UButton
                type="submit"
                block
                :loading="processing"
                :disabled="processing"
            >
                Reset password
            </UButton>
        </Form>
    </div>
</template>
