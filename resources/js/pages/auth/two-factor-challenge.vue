<script setup lang="ts">
import { Form, Head as IHead } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import AuthLayout from '@/layouts/auth.vue'

const usingRecoveryCode = ref(false)

const heading = computed(() => {
    return usingRecoveryCode.value ? 'Use a recovery code' : 'Two-factor challenge'
})

const description = computed(() => {
    return usingRecoveryCode.value ? 'Enter one of your recovery codes to continue.' : 'Enter the code from your authenticator application.'
})
</script>

<template>
    <AuthLayout>
        <div class="space-y-6">
            <IHead title="Two-factor challenge" />

            <div class="space-y-1 text-center">
                <h1 class="text-2xl font-semibold">
                    {{ heading }}
                </h1>
                <p class="text-muted text-sm">
                    {{ description }}
                </p>
            </div>

            <Form
                v-slot="{ errors, processing, clearErrors }"
                :action="route('two-factor.login.store')"
                method="post"
                reset-on-error
                class="space-y-5"
            >
                <UFormField
                    v-if="!usingRecoveryCode"
                    name="code"
                    label="Authentication code"
                    required
                    :error="errors.code"
                >
                    <UInput
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        placeholder="123456"
                        autofocus
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    v-else
                    name="recovery_code"
                    label="Recovery code"
                    required
                    :error="errors.recovery_code"
                >
                    <UInput
                        id="recovery_code"
                        name="recovery_code"
                        type="text"
                        autocomplete="one-time-code"
                        placeholder="xxxx-xxxx"
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
                    Continue
                </UButton>

                <UButton
                    type="button"
                    color="neutral"
                    variant="ghost"
                    block
                    @click="usingRecoveryCode = !usingRecoveryCode; clearErrors()"
                >
                    {{ usingRecoveryCode ? 'Use an authentication code' : 'Use a recovery code' }}
                </UButton>
            </Form>
        </div>
    </AuthLayout>
</template>
