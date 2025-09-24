/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import "./bootstrap";

import { createApp } from "vue";
import '../sass/app.scss';
import router from "./router";
import "./axios";
import store from "./vuex";
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component("app", require("./App.vue").default); // Replaced with createApp.component for Vue 3

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import QrisLineChart from './components/charts/QrisLineChart.vue';
import QrisHanaChart from './components/charts/QrisHanaChart.vue';
import BillerLineChart from './components/charts/BillerLineChart.vue';
import BillerHanaChart from './components/charts/BillerHanaChart.vue';
import DebitLineChart from './components/charts/DebitLineChart.vue';
import DebitHanaChart from './components/charts/DebitHanaChart.vue';
import ChartSlideshow from './components/charts/ChartSlideshow.vue';
import CombinedChart from './components/charts/CombinedChart.vue';
import App from "./App.vue";

const app = createApp(App); // Create Vue 3 app instance

app.component('qris-line-chart', QrisLineChart);
app.component('qris-hana-chart', QrisHanaChart);
app.component('biller-line-chart', BillerLineChart);
app.component('biller-hana-chart', BillerHanaChart);
app.component('debit-line-chart', DebitLineChart);
app.component('debit-hana-chart', DebitHanaChart);
app.component('chart-slideshow', ChartSlideshow);
app.component('combined-chart', CombinedChart);

app.use(router);
app.use(store);
app.use(Toast, {
    position: POSITION.BOTTOM_CENTER
});

app.mount("#app"); // Mount the app
