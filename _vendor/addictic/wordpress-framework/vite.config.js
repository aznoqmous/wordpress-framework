import { defineConfig, splitVendorChunkPlugin } from 'vite'
import liveReload from 'vite-plugin-live-reload'
import path from 'path'

import config from "./vite.json"

const dev = process.env.NODE_ENV === "development"
console.log(`Running vite in ${process.env.NODE_ENV} mode`)

const outDir =  `${__dirname}/public`

// https://vitejs.dev/config/
export default defineConfig({

    plugins: [
        liveReload([]),
        splitVendorChunkPlugin()
    ],

    // config
    root: 'assets',
    base: '/',
    build: {
        outDir,
        emptyOutDir: true,
        rollupOptions: {
            input: Object.fromEntries(Object.keys(config).map(key=> ([key, path.resolve(__dirname, config[key])]))),
            output: {
                entryFileNames: "[name].js",
                chunkFileNames: "[name].js",
                assetFileNames: "[name].[ext]",
            }
        }
    },

    server: {
        strictPort: true,
        port: 5133
    },
    resolve: {
        alias: {
            vue: 'vue/dist/vue.esm-bundler.js'
        }
    },
})