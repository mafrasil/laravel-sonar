import { defineConfig } from "vite";
import { resolve } from "path";

export default defineConfig({
    build: {
        lib: {
            entry: resolve(__dirname, "resources/js/core.ts"),
            formats: ["iife"],
            fileName: () => "sonar.iife.js",
            name: "Sonar",
        },
        outDir: "dist",
        rollupOptions: {
            external: ["react"],
            output: {
                globals: {
                    react: "React",
                },
            },
        },
    },
});
