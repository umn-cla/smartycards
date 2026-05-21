import "@fontsource-variable/nunito/index.css";
import "@fontsource-variable/rokkitt/index.css";

import "./app.css";
import "katex/dist/katex.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { VueQueryPlugin } from "@tanstack/vue-query";
import * as katex from "katex";
window.katex = katex;
import "katex/contrib/mhchem/mhchem.js";

// disable attempts to play sounds
import { MathfieldElement } from "mathlive";
MathfieldElement.soundsDirectory = null;

// for accessibility
import VueAnnouncer from "@vue-a11y/announcer";

import App from "./App.vue";
import router from "./router";

const app = createApp(App)
  .use(createPinia())
  .use(VueAnnouncer)
  .use(router)
  .use(VueQueryPlugin);

app.mount("#app");
