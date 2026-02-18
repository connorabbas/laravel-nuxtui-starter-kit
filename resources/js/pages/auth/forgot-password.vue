<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/auth.vue'

const props = defineProps<{
    status?: string
}>()
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <Head title="Forgot password" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Forgot your password?
                </h1>
                <p class="text-muted text-sm">
                    Enter your email and we will send you a reset link.
                </p>
            </div>

            <UAlert
                v-if="props.status"
                color="success"
                variant="subtle"
                icon="i-lucide-mail-check"
                :description="props.status"
            />

            <Form
                v-slot="{ errors, processing }"
                :action="route('password.email')"
                method="post"
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

                <UButton
                    type="submit"
                    block
                    :loading="processing"
                    :disabled="processing"
                >
                    Email password reset link
                </UButton>
            </Form>

            <p class="text-toned text-center text-sm">
                Remembered your password?
                <Link
                    :href="route('login')"
                    class="text-primary font-medium hover:underline"
                >
                    Back to login
                </Link>
            </p>
        </div>
    </AuthLayout>
</template>
