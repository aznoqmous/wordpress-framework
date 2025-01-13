import { defineConfig, splitVendorChunkPlugin } from 'vite'
import liveReload from 'vite-plugin-live-reload'
import path from 'path'
import * as fs from "node:fs";

import config from "./vite.json"

const dev = process.env.NODE_ENV === "development"
console.log(`Running vite in ${process.env.NODE_ENV} mode`)

const publicDir = fs.existsSync(__dirname + '/web') ? "web" : "public"
const outDir =  `${__dirname}/${publicDir}/dist`

// https://vitejs.dev/config/
export default defineConfig({

    plugins: [
        liveReload([]),
        splitVendorChunkPlugin()
    ],

    // config
    root: 'assets',
    base: dev
        ? '/'
        : '/dist/',

    build: {
        outDir,
        emptyOutDir: true,
        manifest: true,
        rollupOptions: {
            input: Object.fromEntries(Object.keys(config).map(key=> ([key, path.resolve(__dirname, config[key])]))),
            output: {
                entryFileNames: `assets/[name].js`
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
    }
})