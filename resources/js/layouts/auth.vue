<template>
    <UApp>
        <div class="flex h-screen items-center justify-center px-4">
            <!-- TODO: router redirect to previous page -->
            <UButton
                icon="i-lucide-chevron-left"
                to="/"
                size="xl"
                color="neutral"
                variant="subtle"
                class="absolute top-8 left-8 z-10 rounded-full"
            />
            <UPageCard
                class="w-full max-w-md"
            >
                <slot />
            </UPageCard>
        </div>
    </UApp>
</template>
