<script setup lang="ts">
import { Head as IHead, useForm } from '@inertiajs/vue3'
import { useClipboard } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import SettingsLayout from '@/layouts/settings.vue'

const props = defineProps<{
    status?: string
    twoFactorEnabled: boolean
    requiresConfirmation: boolean
    isConfirming: boolean
    qrCode: string | null
    setupKey: string | null
    recoveryCodes: string[]
}>()

const toast = useToast()

const setupModalOpen = ref(false)
const confirmationCode = ref<number[]>([])
const { copy, copied, isSupported: clipboardSupported } = useClipboard({ legacy: true })
const enableTwoFactorForm = useForm({})
const disableTwoFactorForm = useForm({})
const regenerateRecoveryCodesForm = useForm({})
const confirmForm = useForm<{ code: string }>({
    code: '',
})

const confirmationCodeIsComplete = computed(() => confirmationCode.value.join('').length === 6)
const confirmationCodeError = computed(() => {
    const error = confirmForm.errors.code

    if (Array.isArray(error)) {
        return error[0]
    }

    return error
})

const statusAlertDescription = computed<string | null>(() => {
    if (!props.status) {
        return null
    }

    if (props.status === 'two-factor-authentication-enabled') {
        if (!props.twoFactorEnabled && props.isConfirming) {
            return null
        }

        return 'Two-factor authentication has been enabled.'
    }

    if (props.status === 'two-factor-authentication-confirmed') {
        return props.twoFactorEnabled ? 'Two-factor authentication confirmed and enabled successfully.' : null
    }

    if (props.status === 'two-factor-authentication-disabled') {
        return !props.twoFactorEnabled ? 'Two-factor authentication has been disabled.' : null
    }

    return null
})

function submitConfirmationCode(): void {
    confirmForm.code = confirmationCode.value.map((digit) => String(digit)).join('')

    confirmForm.post(route('two-factor.confirm'), {
        preserveScroll: true,
        errorBag: 'confirmTwoFactorAuthentication',
        onSuccess: () => {
            confirmationCode.value = []
            toast.add({
                color: 'success',
                title: 'Success',
                description: 'Two-factor authentication has been setup for your account.',
                icon: 'i-lucide-circle-check'
            })
        },
    })
}

function resetConfirmState(): void {
    confirmationCode.value = []
    confirmForm.cancel()
    confirmForm.reset('code')
    confirmForm.clearErrors()
}

watch(setupModalOpen, (open) => {
    if (!open) {
        resetConfirmState()
    }
})

async function copySetupKey(): Promise<void> {
    if (!props.setupKey) {
        return
    }

    await copy(props.setupKey).then(() => {
        toast.add({
            color: 'success',
            title: 'Setup code copied to clipboard',
            icon: 'i-lucide-circle-check'
        })
    })
}

function enableTwoFactor(): void {
    enableTwoFactorForm.post(route('two-factor.enable'), {
        onSuccess: () => {
            setupModalOpen.value = true
        },
    })
}

function disableTwoFactor(): void {
    disableTwoFactorForm.delete(route('two-factor.disable'))
}

function regenerateRecoveryCodes(): void {
    regenerateRecoveryCodesForm.post(route('two-factor.regenerate-recovery-codes'))
}
</script>

<template>
    <SettingsLayout
        page-title="Two-Factor Authentication"
        title="Two-Factor Authentication"
        description="Add an additional authentication step to protect your account."
    >
        <IHead title="Settings - Two-Factor" />

        <UCard class="max-w-xl">
            <template #header>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold">
                        Two-factor status
                    </h2>
                    <UBadge
                        :color="props.twoFactorEnabled ? 'success' : 'error'"
                        variant="subtle"
                    >
                        {{ props.twoFactorEnabled ? 'Enabled' : 'Disabled' }}
                    </UBadge>
                </div>
            </template>

            <p class="text-muted text-sm">
                {{
                    props.twoFactorEnabled
                        ? 'Your account is protected with two-factor authentication.'
                        : props.isConfirming
                            ? 'Setup is in progress. Confirm your authenticator code to finish enabling two-factor authentication.'
                            : 'Enable two-factor authentication to require a one-time code at login.'
                }}
            </p>

            <UAlert
                v-if="statusAlertDescription"
                :description="statusAlertDescription"
                color="success"
                variant="subtle"
                class="mt-4"
            />

            <div class="mt-4 flex flex-wrap gap-2">
                <UButton
                    v-if="!props.twoFactorEnabled && !props.isConfirming"
                    type="button"
                    :loading="enableTwoFactorForm.processing"
                    :disabled="enableTwoFactorForm.processing"
                    @click="enableTwoFactor"
                >
                    Enable 2FA
                </UButton>

                <UButton
                    v-else-if="props.isConfirming"
                    color="neutral"
                    variant="subtle"
                    @click="setupModalOpen = true"
                >
                    Continue setup
                </UButton>

                <UButton
                    v-if="props.twoFactorEnabled"
                    type="button"
                    color="error"
                    variant="soft"
                    :loading="disableTwoFactorForm.processing"
                    :disabled="disableTwoFactorForm.processing"
                    @click="disableTwoFactor"
                >
                    Disable two-factor
                </UButton>
            </div>
        </UCard>

        <UModal
            v-if="props.isConfirming"
            v-model:open="setupModalOpen"
            title="Enable Two-Factor Authentication"
            description="To finish enabling two-factor authentication, scan the QR code or enter the setup code in your authenticator app"
        >
            <template #body>
                <div class="flex flex-col items-center space-y-5 text-center">
                    <div class="relative z-10 overflow-hidden border border-default p-3 rounded-lg">
                        <div
                            v-if="props.qrCode"
                            class="flex aspect-square size-full items-center justify-center"
                            v-html="props.qrCode"
                        />
                    </div>

                    <form
                        v-if="props.requiresConfirmation"
                        class="w-full max-w-sm space-y-3 text-center"
                        @submit.prevent="submitConfirmationCode"
                    >
                        <UFormField
                            name="code"
                            :error="confirmationCodeError"
                        >
                            <div class="flex justify-center">
                                <UPinInput
                                    id="code"
                                    v-model="confirmationCode"
                                    :ui="{ root: 'w-auto justify-center' }"
                                    :disabled="confirmForm.processing"
                                    :highlight="Boolean(confirmForm.errors.code)"
                                    :color="confirmForm.errors.code ? 'error' : 'primary'"
                                    :length="6"
                                    size="xl"
                                    type="number"
                                    autofocus
                                    otp
                                    required
                                />
                            </div>
                        </UFormField>

                        <UButton
                            type="submit"
                            class="w-full flex justify-center"
                            size="xl"
                            :loading="confirmForm.processing"
                            :disabled="confirmForm.processing || !confirmationCodeIsComplete"
                        >
                            Confirm two-factor
                        </UButton>
                    </form>

                    <div
                        v-if="props.setupKey"
                        class="w-full max-w-sm space-y-3 text-left"
                    >
                        <USeparator label="or, enter the code manually" />

                        <UFieldGroup
                            class="w-full"
                            size="xl"
                        >
                            <UInput
                                :value="props.setupKey"
                                readonly
                                class="w-full font-mono"
                            />

                            <UButton
                                type="button"
                                color="neutral"
                                variant="subtle"
                                :icon="copied ? 'i-lucide-check' : 'i-lucide-copy'"
                                :aria-label="copied ? 'Copied setup key' : 'Copy setup key'"
                                :disabled="!clipboardSupported"
                                @click="copySetupKey"
                            />
                        </UFieldGroup>
                    </div>
                </div>
            </template>
        </UModal>

        <UCard
            v-if="props.twoFactorEnabled && props.recoveryCodes.length > 0"
            class="max-w-xl"
        >
            <template #header>
                <h2 class="text-base font-semibold">
                    Recovery codes
                </h2>
            </template>

            <p class="text-muted text-sm">
                Save these recovery codes in a secure place. They can be used if you lose access to your authenticator
                app.
            </p>

            <div class="mt-4 grid gap-2 rounded-lg border border-default p-4 sm:grid-cols-2">
                <code
                    v-for="code in props.recoveryCodes"
                    :key="code"
                    class="text-sm"
                >{{ code }}</code>
            </div>

            <UButton
                type="button"
                color="neutral"
                variant="subtle"
                class="mt-4"
                :loading="regenerateRecoveryCodesForm.processing"
                :disabled="regenerateRecoveryCodesForm.processing"
                @click="regenerateRecoveryCodes"
            >
                Regenerate recovery codes
            </UButton>
        </UCard>
    </SettingsLayout>
</template>
