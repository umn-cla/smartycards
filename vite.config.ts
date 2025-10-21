import { fileURLToPath, URL } from "node:url";
import { defineConfig, loadEnv } from "vite";
import vue from "@vitejs/plugin-vue";
import laravel from "laravel-vite-plugin";

// https://vitejs.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd());

  return {
    plugins: [
      laravel({
        input: ["resources/client/app.ts"],
        refresh: true,
      }),
      vue({
        template: {
          compilerOptions: {
            isCustomElement: (tag) => ["math-field"].includes(tag),
          },
          transformAssetUrls: {
            // The Vue plugin will re-write asset URLs, when referenced
            // in Single File Components, to point to the Laravel web
            // server. Setting this to `null` allows the Laravel plugin
            // to instead re-write asset URLs to point to the Vite
            // server instead.
            base: null,

            // The Vue plugin will parse absolute URLs and treat them
            // as absolute paths to files on disk. Setting this to
            // `false` will leave absolute URLs un-touched so they can
            // reference assets in the public directory as expected.
            includeAbsolute: false,
          },
        },
      }),
    ],
    resolve: {
      alias: {
        "@": fileURLToPath(new URL("./resources/client", import.meta.url)),
      },
    },
    server: {
      // host: "0.0.0.0", // or "0.0.0.0"?
      host: new URL(env.VITE_CLIENT_BASE_URL).hostname,
      port: 5173,
      https: {
        cert: "./.cert/cert.pem",
        key: "./.cert/key.pem",
      },
      hmr: {
        host: new URL(env.VITE_CLIENT_BASE_URL).hostname,
        protocol: "wss",
      },
    },
  };
});
