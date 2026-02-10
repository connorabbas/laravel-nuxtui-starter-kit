<script setup lang="ts">
import { computed } from 'vue'

import { usePage, Link as ILink } from '@inertiajs/vue3'

const page = usePage()

// Ziggy route() issue: https://github.com/nuxt/ui/issues/4202

const currentRoute = computed(() => {
    // Access page.url to trigger re-computation on navigation.
    /* eslint-disable @typescript-eslint/no-unused-vars */
    const url = page.url
    /* eslint-enable @typescript-eslint/no-unused-vars */
    return route().current()
})
</script>

<template>
    <UApp>
        <UHeader>
            <template #left>
                <ILink href="/">
                    <AppLogo class="h-6 w-auto shrink-0" />
                </ILink>
            </template>

            <UNavigationMenu
                :items="[{
                    label: 'Test Login',
                    to: route('login', {}, false),
                    as: ILink,
                    active: currentRoute === 'login'
                }]"
                orientation="vertical"
                class="-mx-2.5"
            />

            <template #right>
                <UButton
                    icon="i-lucide-log-in"
                    color="neutral"
                    variant="ghost"
                    class="lg:hidden"
                    :to="route('login', {}, false)"
                />

                <UButton
                    label="Sign in"
                    color="neutral"
                    variant="outline"
                    class="hidden lg:inline-flex"
                    :to="route('login', {}, false)"
                />

                <UButton
                    label="Sign up"
                    color="neutral"
                    trailing-icon="i-lucide-arrow-right"
                    class="hidden lg:inline-flex"
                    :to="route('register', {}, false)"
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
            <slot />
        </UMain>

        <USeparator icon="simple-icons:laravel" />

        <UFooter>
            <template #left>
                <p class="text-muted text-sm">Built with Nuxt UI • © {{ new Date().getFullYear() }}</p>
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
