import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { resolve } from 'node:path';
import { defineConfig, loadEnv } from 'vite';
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
                inertia: true,
                components: {
                    dirs: ['resources/js/components'],
                },
                ui: {
                    colors: {
                        primary: 'scarlet',
                        neutral: 'neutral',
                    },
                },
            }),
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, './resources/js'),
                'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
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
