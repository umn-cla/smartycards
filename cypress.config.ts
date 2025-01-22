import { defineConfig } from "cypress";

export default defineConfig({
  chromeWebSecurity: false,
  defaultCommandTimeout: 5000,
  watchForFileChanges: true,
  // retries: 2,
  videosFolder: "tests/cypress/videos",
  screenshotsFolder: "tests/cypress/screenshots",
  fixturesFolder: "tests/cypress/fixture",
  video: true,
  viewportWidth: 1920,
  viewportHeight: 1080,
  e2e: {
    baseUrl: "https://localhost",
    specPattern: "tests/cypress/e2e/**/*.{cy,spec}.{js,jsx,ts,tsx}",
    supportFile: "tests/cypress/support/index.ts",
    experimentalRunAllSpecs: true,
  },
  // component: {
  //   specPattern: "@/components/**/*.{cy,spec}.{js,ts,jsx,tsx}",
  //   devServer: {
  //     framework: "vue",
  //     bundler: "vite",
  //   },
  // },
});
