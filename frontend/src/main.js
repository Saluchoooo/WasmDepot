import Vue from 'vue';
import App from './App.vue';
import router from './router';
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';

// Utilitza Vuetify
Vue.use(Vuetify);

const vuetify = new Vuetify();

Vue.config.productionTip = false;

// Crea una instància de Vue
new Vue({
  router,
  vuetify,
  render: h => h(App),
}).$mount('#app');
