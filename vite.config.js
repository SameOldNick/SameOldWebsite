import { defineConfig, normalizePath } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import { globalConst } from 'vite-plugin-global-const';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import createExternal from 'vite-plugin-external';

import path from 'node:path';

import { DateTime } from 'luxon';

export default defineConfig({
    plugins: [
        globalConst({
            BUILD_DATE: DateTime.now().toISOTime()
        }),
        viteStaticCopy({
            targets: [
                {
                    src: normalizePath(path.resolve(__dirname, './resources/images/*')),
                    dest: normalizePath(path.resolve(__dirname, './public/images'))
                }
            ]
        }),
        createExternal({
            externals: {
                $: 'jquery',
                jQuery: 'jquery',
                'window.jQuery': 'jquery',
            }
        }),
        laravel({
            input: [
                'resources/ts/main/index.ts',
                'resources/ts/admin/index.tsx',
                'resources/scss/main/all.scss',
                'resources/scss/admin/all.scss'
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '@images': '/resources/images',
            '@admin': '/resources/ts/admin',
            '@root': '/resources/ts',

        },
    },
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    admin: ['react', 'react-dom', 'reactstrap', 'chart.js'],
                    mdeditor: ['codemirror-ssr', 'react-xtermjs', 'highlight.js'],
                    main: ['jquery'],
                }
            }
        }
    }
});
