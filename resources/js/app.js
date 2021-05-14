/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');
import router from "./routes";
import store from './store';
import { BootstrapVue } from 'bootstrap-vue'

Vue.config.productionTip = false;
Vue.use(BootstrapVue)

const app = new Vue({
    store,
    router
}).$mount('#app');