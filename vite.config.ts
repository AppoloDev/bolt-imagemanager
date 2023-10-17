import {defineConfig} from 'vite'

// https://vitejs.dev/config/
export default defineConfig({
    build: {
        assetsDir: "",
        emptyOutDir: true,
        outDir: "./build",
        rollupOptions: {
            output: {
                manualChunks: undefined,
                entryFileNames: `[name].js`,
                chunkFileNames: `[name].js`,
                assetFileNames: `[name].[ext]`
            },
            input: {
                imagemanager: './assets/imagemanager.js',
            },
        }
    },
})
