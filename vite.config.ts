import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { google } from 'laravel-vite-plugin/fonts'
import path from 'path'
import { defineConfig, loadEnv } from 'vite'
import ui from '@nuxt/ui/vite'

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
    // https://vite.dev/config/#using-environment-variables-in-config
    const env = loadEnv(mode, process.cwd(), '')
    const devPort = env.VITE_APP_PORT ? Number(env.VITE_APP_PORT) : 5173
    const hostDomain = env.VITE_HOST_DOMAIN || 'localhost'

    return {
        plugins: [
            laravel({
                input: ['resources/js/app.ts'],
                ssr: 'resources/js/ssr.ts',
                refresh: true,
                fonts: [
                    google('Inter', {
                        alias: 'sans',
                        weights: [400, 500, 600, 700],
                        styles: ['normal', 'italic'],
                        subsets: ['latin'],
                        display: 'swap',
                        preload: [
                            { weight: 400 },
                            { weight: 700 },
                        ],
                        fallbacks: ['system-ui', 'sans-serif'],
                    }),
                ],
            }),
            vue({
                template: {
                    transformAssetUrls: {
                        base: null,
                        includeAbsolute: false,
                    },
                },
            }),
            ui({
                router: 'inertia',
                components: {
                    dirs: ['resources/js'],
                },
                ui: {
                    colors: {
                        //primary: 'laravel',
                        neutral: 'neutral',
                    },
                    alert: {
                        defaultVariants: {
                            variant: 'subtle',
                        },
                    },
                    card: {
                        defaultVariants: {
                            variant: 'subtle',
                        },
                    },
                    pageCard: {
                        defaultVariants: {
                            variant: 'subtle',
                        },
                    },
                    pageHeader: {
                        slots: {
                            root: 'relative border-b border-default py-6',
                            wrapper: 'flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2',
                            headline: 'mb-1.5 text-xs font-semibold text-primary flex items-center gap-1',
                            title: 'text-xl sm:text-2xl text-pretty font-bold text-highlighted',
                            description: 'text-sm text-pretty text-muted',
                            links: 'flex flex-wrap items-center gap-1',
                        },
                        variants: {
                            title: {
                                true: {
                                    description: 'mt-2',
                                },
                            },
                        },
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
            },
        },
        server: {
            port: devPort,
            host: true,
            hmr: {
                host: hostDomain,
            },
            cors: true,
            watch: {
                usePolling: true,
            },
        },
        preview: {
            port: devPort,
        },
        ssr: {
            noExternal: true, // bundle node server related files, so we don't need node_modules in production
        },
    }
})
