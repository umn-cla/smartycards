import "@fontsource-variable/nunito";
import "@fontsource-variable/rokkitt";

import "./app.css";
import "katex/dist/katex.css";

import { createApp } from "vue";
import { createPinia } from "pinia";
import { VueQueryPlugin } from "@tanstack/vue-query";

// for formula rendering
import katex from "katex";
window.katex = katex;
import "katex/contrib/mhchem/mhchem.js";

import App from "./App.vue";
import router from "./router";

const app = createApp(App);

app.use(createPinia());
app.use(router);
app.use(VueQueryPlugin);

app.mount("#app");
