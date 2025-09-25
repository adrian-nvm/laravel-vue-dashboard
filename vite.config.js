
import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    return {
        plugins: [
            laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
            script: {
                babelParserPlugins: ['decorators'],
            },
        }),
    ],
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js',
            '@fortawesome': path.resolve(__dirname, 'node_modules/@fortawesome'),
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                includePaths: ['node_modules']
            }
        }
    },
    build: {
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (assetInfo.name.includes('webfonts')) {
                        return `webfonts/[name].[ext]`;
                    }
                    return 'assets/[name].[hash][extname]';
                },
            },
        },
    },
        server: {
            proxy: {
                '/api': {
                    target: env.APP_URL,
                    changeOrigin: true,
                    secure: false,
                },
            },
        },
    };
});
