<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/auth.vue'

const props = defineProps<{
    status?: string
}>()
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <Head title="Verify email" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Verify your email
                </h1>
                <p class="text-muted text-sm">
                    Check your inbox and click the verification link to continue.
                </p>
            </div>

            <UAlert
                v-if="props.status === 'verification-link-sent'"
                color="success"
                variant="subtle"
                icon="i-lucide-send"
                description="A new verification link has been sent to your email address."
            />

            <Form
                v-slot="{ processing }"
                :action="route('verification.send')"
                method="post"
            >
                <UButton
                    type="submit"
                    block
                    :loading="processing"
                    :disabled="processing"
                >
                    Resend verification email
                </UButton>
            </Form>

            <Form
                v-slot="{ processing }"
                :action="route('logout')"
                method="post"
            >
                <UButton
                    type="submit"
                    color="neutral"
                    variant="soft"
                    block
                    :loading="processing"
                    :disabled="processing"
                >
                    Log out
                </UButton>
            </Form>
        </div>
    </AuthLayout>
</template>
