<script setup lang="ts">
import { Head as IHead, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

import AuthLayout from '@/layouts/auth.vue'

const usingRecoveryCode = ref(false)
const authenticationCode = ref<number[]>([])

const challengeForm = useForm({
    code: '',
    recovery_code: '',
})

const heading = computed(() => {
    return usingRecoveryCode.value ? 'Use a recovery code' : 'Two-factor challenge'
})

const description = computed(() => {
    return usingRecoveryCode.value ? 'Enter one of your recovery codes to continue.' : 'Enter the code from your authenticator application.'
})

const authenticationCodeIsComplete = computed(() => authenticationCode.value.join('').length === 6)

function submit(): void {
    if (!usingRecoveryCode.value) {
        challengeForm.code = authenticationCode.value.map((digit) => String(digit)).join('')
    }

    challengeForm.post(route('two-factor.login.store'), {
        onError: () => {
            authenticationCode.value = []
            challengeForm.reset('code', 'recovery_code')
        },
    })
}

function toggleChallengeType(): void {
    usingRecoveryCode.value = !usingRecoveryCode.value
    authenticationCode.value = []
    challengeForm.reset('code', 'recovery_code')
    challengeForm.clearErrors()
}
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

            <form
                class="space-y-5"
                @submit.prevent="submit"
            >
                <UFormField
                    v-if="!usingRecoveryCode"
                    name="code"
                    required
                    :error="challengeForm.errors.code"
                >
                    <div class="flex justify-center">
                        <UPinInput
                            id="code"
                            v-model="authenticationCode"
                            :ui="{ root: 'w-auto justify-center' }"
                            :disabled="challengeForm.processing"
                            :highlight="Boolean(challengeForm.errors.code)"
                            :color="challengeForm.errors.code ? 'error' : 'primary'"
                            :length="6"
                            size="xl"
                            type="number"
                            autofocus
                            otp
                            required
                        />
                    </div>
                </UFormField>

                <UFormField
                    v-else
                    name="recovery_code"
                    label="Recovery code"
                    required
                    :error="challengeForm.errors.recovery_code"
                >
                    <UInput
                        id="recovery_code"
                        v-model="challengeForm.recovery_code"
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
                    :loading="challengeForm.processing"
                    :disabled="challengeForm.processing || (!usingRecoveryCode && !authenticationCodeIsComplete)"
                >
                    Continue
                </UButton>

                <UButton
                    type="button"
                    color="neutral"
                    variant="ghost"
                    block
                    @click="toggleChallengeType"
                >
                    {{ usingRecoveryCode ? 'Use an authentication code' : 'Use a recovery code' }}
                </UButton>
            </form>
        </div>
    </AuthLayout>
</template>
