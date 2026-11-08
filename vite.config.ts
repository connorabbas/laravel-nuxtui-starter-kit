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
    const devServerUrl = env.VITE_DEV_SERVER_URL ? new URL(env.VITE_DEV_SERVER_URL) : undefined

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
                    dirs: [
                        'resources/js/components',
                        'resources/js/layouts',
                        'resources/js/pages',
                    ],
                },
                ui: {
                    colors: {
                        neutral: 'neutral',
                    },
                    alert: {
                        defaultVariants: {
                            variant: 'subtle',
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
            strictPort: true,
            host: devServerUrl ? '0.0.0.0' : 'localhost',
            origin: devServerUrl?.origin,
            allowedHosts: devServerUrl ? [devServerUrl.hostname] : undefined,
            cors: devServerUrl ? { origin: env.APP_URL } : undefined,
            watch: {
                usePolling: env.VITE_USE_POLLING === 'true',
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
