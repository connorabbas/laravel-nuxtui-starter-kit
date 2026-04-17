<script setup lang="ts">
import { useForm } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/Auth.vue'

const props = defineProps<{
    status?: string
}>()

const resendVerificationForm = useForm({})
const logoutForm = useForm({})

function resendVerificationEmail(): void {
    resendVerificationForm.post(route('verification.send'))
}

function submitLogout(): void {
    logoutForm.post(route('logout'))
}
</script>

<template>
    <AuthLayout
        title="Verify email"
        description="Check your inbox and click the verification link to continue."
    >
        <div class="space-y-6">
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
                icon="i-lucide-send"
                description="A new verification link has been sent to your email address."
            />

            <form @submit.prevent="resendVerificationEmail">
                <UButton
                    type="submit"
                    block
                    :loading="resendVerificationForm.processing"
                    :disabled="resendVerificationForm.processing"
                >
                    Resend verification email
                </UButton>
            </form>

            <form @submit.prevent="submitLogout">
                <UButton
                    type="submit"
                    color="neutral"
                    variant="soft"
                    block
                    :loading="logoutForm.processing"
                    :disabled="logoutForm.processing"
                >
                    Log out
                </UButton>
            </form>
        </div>
    </AuthLayout>
</template>
