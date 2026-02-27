<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

import FlashMessages from '@/components/FlashMessages.vue'

const page = usePage()

const currentRoute = computed(() => {
    const url = page.url

    return route().current() ?? url
})
</script>

<template>
    <UApp>
        <UHeader>
            <template #left>
                <Link href="/">
                    <AppLogo class="h-6 w-auto shrink-0" />
                </Link>
            </template>

            <UNavigationMenu
                :items="[
                    {
                        label: 'Test Login',
                        to: route('login'),
                        active: currentRoute === 'login'
                    }
                ]"
                orientation="vertical"
                class="-mx-2.5"
            />

            <template #right>
                <UButton
                    icon="i-lucide-log-in"
                    color="neutral"
                    variant="ghost"
                    class="lg:hidden"
                    :to="route('login')"
                />

                <UButton
                    label="Sign in"
                    color="neutral"
                    variant="outline"
                    class="hidden lg:inline-flex"
                    :to="route('login')"
                />

                <UButton
                    label="Sign up"
                    color="neutral"
                    trailing-icon="i-lucide-arrow-right"
                    class="hidden lg:inline-flex"
                    :to="route('register')"
                />

                <UColorModeButton />

                <UButton
                    to="https://github.com/nuxt-ui-templates/starter-laravel"
                    target="_blank"
                    icon="simple-icons:github"
                    aria-label="GitHub"
                    color="neutral"
                    variant="ghost"
                />
            </template>
        </UHeader>

        <UMain>
            <UContainer class="pt-4">
                <FlashMessages />
            </UContainer>

            <slot />
        </UMain>

        <USeparator icon="simple-icons:laravel" />

        <UFooter>
            <template #left>
                <p class="text-muted text-sm">
                    Built with Nuxt UI • © {{ new Date().getFullYear() }}
                </p>
            </template>

            <template #right>
                <UButton
                    to="https://github.com/nuxt-ui-templates/starter-laravel"
                    target="_blank"
                    icon="simple-icons:github"
                    aria-label="GitHub"
                    color="neutral"
                    variant="ghost"
                />
            </template>
        </UFooter>
    </UApp>
</template>
