<script setup lang="ts">
import { Head as IHead, Link, useForm } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/Auth.vue'

const props = defineProps<{
    status?: string
}>()

const forgotPasswordForm = useForm({
    email: '',
})

function submit(): void {
    forgotPasswordForm.post(route('password.email'))
}
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <IHead title="Forgot password" />

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
                icon="i-lucide-mail-check"
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
                    :error="forgotPasswordForm.errors?.email"
                >
                    <UInput
                        id="email"
                        v-model="forgotPasswordForm.email"
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
                    :loading="forgotPasswordForm.processing"
                    :disabled="forgotPasswordForm.processing"
                >
                    Email password reset link
                </UButton>
            </form>

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
