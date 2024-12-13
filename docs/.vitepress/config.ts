import { defineConfig } from "vitepress";
import { fileURLToPath } from "url";

export default defineConfig({
  lang: "en-US",
  title: "SmartyCards Help",
  base: "/smartycards/",
  lastUpdated: true,
  appearance: false, // force light mode. UMN header looks bad in dark mode
  vite: {
    // building the theme will fail without this. Vitepress wants
    // cla-vue-template to be ssr compatible, but it isn't.
    ssr: {
      noExternal: ["@umn-latis/cla-vue-template"],
    },
    resolve: {
      alias: {
        "@": fileURLToPath(new URL("../../resources/client", import.meta.url)),
      },
    },
  },
  themeConfig: {
    sidebar: [
      {
        text: "Getting Started",
        items: [
          {
            text: "Introduction",
            link: "/",
          },
          {
            text: "Quick Start",
            link: "/quick-start",
          },
          {
            text: "Sharing Decks",
            link: "/sharing-decks",
          },
        ],
      },
      {
        text: "Activities",
        items: [
          {
            text: "Overview",
            link: "/activities",
          },
          {
            text: "Practice Flashcards",
            link: "/activities/practice-flashcards",
          },
          {
            text: "Quiz",
            link: "/activities/quiz",
          },
          {
            text: "Matching Game",
            link: "/activities/matching-game",
          },
        ],
      },
      {
        text: "Teaching",
        items: [
          {
            text: "Deck Summary Report",
            link: "/teaching/deck-summary-report",
          },
          {
            text: "Classroom Activities",
            link: "/teaching/classroom-activities",
          },
        ],
      },
      {
        text: "More Info",
        items: [
          {
            text: "Getting Help",
            link: "/more-info/getting-help",
          },
          {
            text: "Accessibility",
            link: "/more-info/accessibility",
          },
          {
            text: "About",
            link: "/more-info/about",
          },
        ],
      },
    ],
    socialLinks: [
      { icon: "github", link: "https://github.com/UMN-LATIS/ChimeIn2.0" },
    ],
    editLink: {
      pattern:
        "https://github.com/UMN-LATIS/ChimeIn2.0/edit/develop/docs/:path",
      text: "Edit this page on GitHub",
    },
    footer: {
      message: "Released under the MIT License.",
      copyright:
        "© 2024 Regents of the University of Minnesota. All rights reserved. The University of Minnesota is an equal opportunity educator and employer.CLA is committed to making its digital resources accessible. [Google](https://www.google.com)",
    },
    search: {
      provider: "local",
    },
  },
});
