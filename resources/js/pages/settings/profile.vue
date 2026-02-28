<script setup lang="ts">
import { Head as IHead, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

import SettingsLayout from '@/layouts/settings.vue'

const props = defineProps<{
    mustVerifyEmail: boolean
    status?: string
}>()

const page = usePage()
const user = computed(() => page.props.auth.user)
const deleteModalOpen = ref(false)
const showDeletePassword = ref(false)

// TODO: success toast on save
const profileUpdateForm = useForm({
    name: user.value?.name ?? '',
    email: user.value?.email ?? '',
})

const resendVerificationForm = useForm({})

const deleteAccountForm = useForm({
    password: '',
})

function updateProfile(): void {
    profileUpdateForm.patch(route('profile.update'), {
        onSuccess: () => {
            profileUpdateForm.defaults()
        },
    })
}

function resendVerificationEmail(): void {
    resendVerificationForm.post(route('verification.send'))
}

function deleteAccount(): void {
    deleteAccountForm.delete(route('profile.destroy'), {
        onSuccess: () => {
            deleteAccountForm.reset()
            deleteModalOpen.value = false
            showDeletePassword.value = false
        },
    })
}

watch(deleteModalOpen, (open) => {
    if (!open) {
        deleteAccountForm.reset()
        deleteAccountForm.clearErrors()
        showDeletePassword.value = false
    }
})
</script>

<template>
    <SettingsLayout
        page-title="Profile"
        title="Profile"
        description="Update your name, email address, and account details."
    >
        <IHead title="Settings - Profile" />

        <UCard class="max-w-xl">
            <template #header>
                <h2 class="text-base font-semibold">
                    Profile information
                </h2>
            </template>

            <form
                class="space-y-4"
                @submit.prevent="updateProfile"
            >
                <UFormField
                    name="name"
                    label="Name"
                    required
                    :error="profileUpdateForm.errors?.name"
                >
                    <UInput
                        id="name"
                        v-model="profileUpdateForm.name"
                        name="name"
                        type="text"
                        autocomplete="name"
                        class="w-full"
                    />
                </UFormField>

                <UFormField
                    name="email"
                    label="Email address"
                    required
                    :error="profileUpdateForm.errors?.email"
                >
                    <UInput
                        id="email"
                        v-model="profileUpdateForm.email"
                        name="email"
                        type="email"
                        autocomplete="email"
                        class="w-full"
                    />
                </UFormField>

                <div
                    v-if="props.mustVerifyEmail && user && !user.email_verified_at"
                    class="space-y-2"
                >
                    <p class="text-muted text-sm">
                        Your email address is unverified.
                    </p>
                    <UButton
                        type="button"
                        color="neutral"
                        variant="subtle"
                        size="sm"
                        :loading="resendVerificationForm.processing"
                        :disabled="resendVerificationForm.processing"
                        @click="resendVerificationEmail"
                    >
                        Resend verification email
                    </UButton>
                </div>

                <UAlert
                    v-if="props.status === 'verification-link-sent'"
                    color="success"
                    title="Verification link sent"
                    description="A new verification link has been sent to your email address."
                />

                <div class="flex items-center gap-3">
                    <UButton
                        type="submit"
                        :loading="profileUpdateForm.processing"
                        :disabled="profileUpdateForm.processing"
                    >
                        Save changes
                    </UButton>

                    <span
                        v-if="profileUpdateForm.recentlySuccessful"
                        class="text-muted text-sm"
                    >Saved.</span>
                </div>
            </form>
        </UCard>

        <UCard class="max-w-xl">
            <template #header>
                <h2 class="text-base font-semibold text-error">
                    Danger zone
                </h2>
            </template>

            <p class="text-muted text-sm">
                Deleting your account is irreversible. All related data will be permanently removed.
            </p>

            <div class="mt-4">
                <UModal
                    v-model:open="deleteModalOpen"
                    title="Delete account"
                    description="Enter your password to permanently delete your account."
                >
                    <UButton
                        color="error"
                        variant="soft"
                    >
                        Delete account
                    </UButton>

                    <template #body>
                        <form
                            class="space-y-4"
                            @submit.prevent="deleteAccount"
                        >
                            <UFormField
                                name="password"
                                label="Password"
                                required
                                :error="deleteAccountForm.errors?.password"
                            >
                                <UInput
                                    id="delete_password"
                                    v-model="deleteAccountForm.password"
                                    name="password"
                                    :type="showDeletePassword ? 'text' : 'password'"
                                    autocomplete="current-password"
                                    :ui="{ trailing: 'pe-1' }"
                                    class="w-full"
                                >
                                    <template #trailing>
                                        <UButton
                                            color="neutral"
                                            variant="link"
                                            size="sm"
                                            :icon="showDeletePassword ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                                            :aria-label="showDeletePassword ? 'Hide password' : 'Show password'"
                                            :aria-pressed="showDeletePassword"
                                            aria-controls="delete_password"
                                            @click="showDeletePassword = !showDeletePassword"
                                        />
                                    </template>
                                </UInput>
                            </UFormField>

                            <div class="flex justify-end gap-2">
                                <UButton
                                    type="button"
                                    color="neutral"
                                    variant="ghost"
                                    @click="deleteModalOpen = false"
                                >
                                    Cancel
                                </UButton>

                                <UButton
                                    type="submit"
                                    color="error"
                                    :loading="deleteAccountForm.processing"
                                    :disabled="deleteAccountForm.processing"
                                >
                                    Delete account
                                </UButton>
                            </div>
                        </form>
                    </template>
                </UModal>
            </div>
        </UCard>
    </SettingsLayout>
</template>
