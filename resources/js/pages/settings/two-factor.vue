<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3'
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
</script>

<template>
    <SettingsLayout
        page-title="Two-Factor Authentication"
        title="Two-Factor Authentication"
        description="Add an additional authentication step to protect your account."
    >
        <Head title="Settings - Two-Factor" />

        <UCard>
            <template #header>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold">
                        Two-factor status
                    </h2>
                    <!-- TODO: not updating on change -->
                    <UBadge
                        :color="props.twoFactorEnabled ? 'success' : 'neutral'"
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
                        : 'Enable two-factor authentication to require a one-time code at login.'
                }}
            </p>

            <UAlert
                v-if="props.status"
                :description="props.status"
                color="success"
                variant="subtle"
                class="mt-4"
            />

            <div class="mt-4 flex flex-wrap gap-2">
                <Form
                    v-if="!props.twoFactorEnabled && !props.isConfirming"
                    v-slot="{ processing }"
                    :action="route('two-factor.enable')"
                    method="post"
                >
                    <UButton
                        type="submit"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Enable two-factor
                    </UButton>
                </Form>

                <Form
                    v-if="props.twoFactorEnabled || props.isConfirming"
                    v-slot="{ processing }"
                    :action="route('two-factor.disable')"
                    method="delete"
                >
                    <UButton
                        type="submit"
                        color="error"
                        variant="soft"
                        :loading="processing"
                        :disabled="processing"
                    >
                        Disable two-factor
                    </UButton>
                </Form>
            </div>
        </UCard>

        <UCard v-if="props.isConfirming || props.twoFactorEnabled">
            <template #header>
                <h2 class="text-base font-semibold">
                    Authenticator setup
                </h2>
            </template>

            <p class="text-muted text-sm">
                Scan this QR code with your authenticator app and use the generated code to confirm setup.
            </p>

            <div
                v-if="props.qrCode"
                class="mt-4 max-w-52 rounded-lg border border-default p-3"
                v-html="props.qrCode"
            />

            <p
                v-if="props.setupKey"
                class="mt-3 text-sm"
            >
                Setup key:
                <span class="font-mono">{{ props.setupKey }}</span>
            </p>

            <Form
                v-if="props.isConfirming && props.requiresConfirmation"
                v-slot="{ errors, processing }"
                :action="route('two-factor.confirm')"
                method="post"
                class="mt-4 max-w-sm space-y-3"
            >
                <UFormField
                    name="code"
                    label="Confirmation code"
                    required
                    :error="errors.code"
                >
                    <UInput
                        id="code"
                        name="code"
                        type="text"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        class="w-full"
                    />
                </UFormField>

                <UButton
                    type="submit"
                    :loading="processing"
                    :disabled="processing"
                >
                    Confirm two-factor
                </UButton>
            </Form>
        </UCard>

        <UCard v-if="props.twoFactorEnabled && props.recoveryCodes.length > 0">
            <template #header>
                <h2 class="text-base font-semibold">
                    Recovery codes
                </h2>
            </template>

            <p class="text-muted text-sm">
                Save these recovery codes in a secure place. They can be used if you lose access to your authenticator app.
            </p>

            <div class="mt-4 grid gap-2 rounded-lg border border-default p-4 sm:grid-cols-2">
                <code
                    v-for="code in props.recoveryCodes"
                    :key="code"
                    class="text-sm"
                >
                    {{ code }}
                </code>
            </div>

            <Form
                v-slot="{ processing }"
                :action="route('two-factor.regenerate-recovery-codes')"
                method="post"
                class="mt-4"
            >
                <UButton
                    type="submit"
                    color="neutral"
                    variant="outline"
                    :loading="processing"
                    :disabled="processing"
                >
                    Regenerate recovery codes
                </UButton>
            </Form>
        </UCard>
    </SettingsLayout>
</template>
