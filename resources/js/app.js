require('./bootstrap');

import Vue from 'vue';
import VueRouter from 'vue-router';
import routes from './routes';
import Navbar from './components/partials/Navbar';
import Footer from './components/partials/Footer';

Vue.use(VueRouter);

import AxiosPlugin from 'vue-axios-cors';

Vue.use(AxiosPlugin);

const app = new Vue({
  el: '#app',
  router: new VueRouter(routes),
  components: {
    'assets-navbar': Navbar,
    'assets-footer': Footer
  }
});
