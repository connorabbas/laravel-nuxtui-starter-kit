<script setup lang="ts">
import { reactive, ref } from 'vue'
import Layout from '@/layouts/auth.vue'


defineOptions({ layout: Layout })

const submitting = ref(false)
const showPw = ref(false)
const state = reactive({
    email: undefined,
    password: undefined,
    rememberMe: true
})
</script>

<template>
    <UForm
        :state="state"
        class="space-y-6"
    >
        <div class="flex flex-col text-center">
            <div class="text-xl text-pretty font-semibold text-highlighted">Welcome back</div>
            <div class="mt-1 text-sm text-pretty text-muted">
                Don't have an account? <ULink
                    to="/sign-up"
                    class="text-primary hover:underline"
                >Sign up</ULink>.
            </div>
        </div>

        <UFormField
            label="Email"
            name="email"
            autocomplete="email"
            required
        >
            <UInput
                v-model="state.email"
                type="email"
                placeholder="Enter your email"
                class="w-full"
            />
        </UFormField>

        <UFormField
            label="Password"
            name="password"
            autocomplete="current-password"
            required
        >
            <template #hint>
                <ULink
                    to="/forgot-password"
                    class="text-primary hover:underline"
                >
                    Forgot your password?
                </ULink>
            </template>
            <UInput
                v-model="state.password"
                placeholder="Enter your password"
                class="w-full"
                :type="showPw ? 'text' : 'password'"
                :ui="{ trailing: 'pe-1' }"
            >
                <template #trailing>
                    <UButton
                        color="neutral"
                        variant="link"
                        size="sm"
                        :icon="showPw ? 'i-lucide-eye-off' : 'i-lucide-eye'"
                        :aria-label="showPw ? 'Hide password' : 'Show password'"
                        :aria-pressed="showPw"
                        aria-controls="password"
                        @click="showPw = !showPw"
                    />
                </template>
            </UInput>
        </UFormField>

        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
            <UFormField name="rememberMe">
                <UCheckbox
                    v-model="state.rememberMe"
                    label="Remember me"
                />
            </UFormField>
        </div>

        <UButton
            label="Log in"
            type="submit"
            class="w-full flex justify-center"
            :disabled="submitting"
            :loading="submitting"
        />
    </UForm>
</template>

<style>
/* Hide the password reveal button in Edge */
::-ms-reveal {
    display: none;
}
</style>
