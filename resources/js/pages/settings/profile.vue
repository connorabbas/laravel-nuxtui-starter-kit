<script setup lang="ts">
import { Form, Head as IHead, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import SettingsLayout from '@/layouts/settings.vue'

const props = defineProps<{
    mustVerifyEmail: boolean
    status?: string
}>()

const page = usePage()
const user = computed(() => page.props.auth.user)
const deleteModalOpen = ref(false)
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

            <Form
                v-slot="{ errors, processing, recentlySuccessful }"
                :action="route('profile.update')"
                method="patch"
                class="space-y-4"
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
                        :value="user?.name"
                        autocomplete="name"
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
                        :value="user?.email"
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
                    <Form
                        v-slot="{ processing: resendProcessing }"
                        :action="route('verification.send')"
                        method="post"
                    >
                        <UButton
                            type="submit"
                            color="neutral"
                            variant="link"
                            class="p-0"
                            :loading="resendProcessing"
                        >
                            Resend verification email
                        </UButton>
                    </Form>
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
                        :loading="processing"
                        :disabled="processing"
                    >
                        Save changes
                    </UButton>

                    <span
                        v-if="recentlySuccessful"
                        class="text-muted text-sm"
                    >Saved.</span>
                </div>
            </Form>
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
                        <Form
                            v-slot="{ errors, processing }"
                            :action="route('profile.destroy')"
                            method="delete"
                            class="space-y-4"
                        >
                            <UFormField
                                name="password"
                                label="Password"
                                required
                                :error="errors.password"
                            >
                                <UInput
                                    id="delete_password"
                                    name="password"
                                    type="password"
                                    autocomplete="current-password"
                                    class="w-full"
                                />
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
                                    :loading="processing"
                                    :disabled="processing"
                                >
                                    Delete account
                                </UButton>
                            </div>
                        </Form>
                    </template>
                </UModal>
            </div>
        </UCard>
    </SettingsLayout>
</template>
