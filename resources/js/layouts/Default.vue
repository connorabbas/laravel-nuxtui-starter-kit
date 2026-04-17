<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import FlashMessages from '@/components/FlashMessages.vue'
import AppHead from '@/components/AppHead.vue'
import type { NavigationMenuItem } from '@nuxt/ui'

const props = defineProps<{
    title: string
    description?: string
}>()

const navMenuItems: NavigationMenuItem[] = [
    {
        label: 'Laravel Docs',
        icon: 'i-lucide-book-open',
        to: 'https://laravel.com/docs/12.x',
        target: '_blank'
    },
    {
        label: 'Nuxt UI Docs',
        icon: 'i-lucide-book-open',
        to: 'https://ui.nuxt.com/',
        target: '_blank'
    }
]
</script>

<template>
    <UApp>
        <AppHead
            :title="props.title"
            :description="props.description"
        />

        <UHeader :ui="{ right: 'flex sm:gap-3' }">
            <template #left>
                <Link
                    href="/"
                    aria-label="Application logo"
                >
                    <AppLogo class="h-6 w-auto shrink-0" />
                </Link>
            </template>

            <UNavigationMenu
                :items="navMenuItems"
                variant="link"
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

            <template #body>
                <UNavigationMenu
                    :items="navMenuItems"
                    orientation="vertical"
                    class="-mx-2.5"
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
