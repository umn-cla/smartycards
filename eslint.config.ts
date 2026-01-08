import js from "@eslint/js";
import globals from "globals";
import tseslint from "typescript-eslint";
import pluginVue from "eslint-plugin-vue";
import eslintConfigPrettier from "eslint-config-prettier";
import { includeIgnoreFile } from "@eslint/compat";
import { fileURLToPath } from "node:url";
import css from "@eslint/css";

const gitignorePath = fileURLToPath(new URL(".gitignore", import.meta.url));

export default [
  includeIgnoreFile(gitignorePath),
  {
    ignores: ["**/*.css", "public/vendor/**"],
  },
  {
    files: ["**/*.{js,mjs,cjs,ts,mts,cts,vue}"],
    ...js.configs.recommended,
    languageOptions: { globals: { ...globals.browser, ...globals.node } },
  },
  ...tseslint.configs.recommended,
  ...pluginVue.configs["flat/essential"],
  {
    files: ["**/*.vue"],
    languageOptions: {
      parserOptions: { parser: tseslint.parser },
    },
  },
  {
    files: ["tests/cypress/**/*.{js,ts}"],
    languageOptions: {
      globals: {
        cy: "readonly",
        Cypress: "readonly",
        assert: "readonly",
      },
    },
  },
  {
    files: ["**/*.css"],
    plugins: { css },
    language: "css/css",
    rules: css.configs.recommended.rules,
  },
  eslintConfigPrettier,
  {
    files: ["**/*.{js,mjs,cjs,ts,mts,cts,vue}"],
    rules: {
      "@typescript-eslint/no-unused-vars": [
        "warn",
        {
          argsIgnorePattern: "^_",
          varsIgnorePattern: "^_",
          caughtErrorsIgnorePattern: "^_",
          destructuredArrayIgnorePattern: "^_",
        },
      ],
      "vue/multi-word-component-names": "off",
      "no-unused-expressions": "off",
      "@typescript-eslint/no-unused-expressions": "off",
    },
  },
];
