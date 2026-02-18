<script setup lang="ts">
import { Form, Head as IHead, Link } from '@inertiajs/vue3'

import AuthLayout from '@/layouts/auth.vue'
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <IHead title="Register" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    Create your account
                </h1>
                <p class="text-muted text-sm">
                    Sign up to start using your application.
                </p>
            </div>

            <Form
                v-slot="{ errors, processing }"
                :action="route('register.store')"
                method="post"
                :reset-on-success="['password', 'password_confirmation']"
                class="space-y-5"
            >
                <UFormField
                    name="name"
                    label="Name"
                    required
                    :error="errors.name"
                >
                    <UInput
                        id="name"
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
                    :error="errors.email"
                >
                    <UInput
                        id="email"
                        name="email"
                        type="email"
                        placeholder="email@example.com"
                        autocomplete="email"
                        class="w-full"
                    />
                </UFormField>

                <!-- TODO: https://ui.nuxt.com/docs/components/input#with-password-toggle -->
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
                        autocomplete="new-password"
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
                        placeholder="Confirm password"
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
                    Create account
                </UButton>
            </Form>

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
