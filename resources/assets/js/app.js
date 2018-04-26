import VueRouter from 'vue-router';
import router from './routes.js';
import Auth from './auth.js';
import Api from './api.js';
import * as VueGoogleMaps from 'vue2-google-maps'
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.auth = new Auth();
window.api = new Api();

Vue.use(VueRouter);

window.Event = new Vue;
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('vue-layout', require('./views/Layout.vue'));

Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyAhsnmVxkIzUDBYRKJUDUzY4tbeV4B9xCU',
    libraries: 'places,drawing,geometry',
  }
});

const app = new Vue({
    el: '#app',
    
    router
});
