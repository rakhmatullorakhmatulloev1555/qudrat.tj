import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            // TresJS: компилятор Vue должен трактовать <Tres*> как кастомные
            // элементы, иначе они не резолвятся и 3D-сцена не рендерится.
            template: {
                compilerOptions: {
                    isCustomElement: (tag) => tag.startsWith('Tres') || tag === 'primitive',
                },
            },
        }),
    ],
    // TresJS: не допускаем дублирования @tresjs/core и three в бандле —
    // иначе cientos и core видят разные контексты (useTresContext падает).
    resolve: {
        dedupe: ['@tresjs/core', 'three'],
    },
    optimizeDeps: {
        include: ['@tresjs/core', '@tresjs/cientos', 'three', 'gsap'],
    },
    server: {
        host: '127.0.0.1',
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
