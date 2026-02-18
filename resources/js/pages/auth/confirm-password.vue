<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/auth.vue'
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <Head title="Confirm password" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Confirm your password
                </h1>
                <p class="text-muted text-sm">
                    This is a secure area. Please confirm your password to continue.
                </p>
            </div>

            <Form
                v-slot="{ errors, processing }"
                :action="route('password.confirm.store')"
                method="post"
                reset-on-success
                class="space-y-5"
            >
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
                        placeholder="Password"
                        autocomplete="current-password"
                        autofocus
                        class="w-full"
                    />
                </UFormField>

                <UButton
                    type="submit"
                    block
                    :loading="processing"
                    :disabled="processing"
                >
                    Confirm password
                </UButton>
            </Form>
        </div>
    </AuthLayout>
</template>
