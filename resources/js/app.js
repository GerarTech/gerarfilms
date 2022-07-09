/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. **/
require('jquery');
require('./bootstrap');
require('pace');
require('perfect-scrollbar');

window.Vue = require('vue');

global.$ = global.jQuery = require('jquery');

import Vuex from 'vuex';
import VueProgressBar from 'vue-progressbar'
import Multiselect from 'vue-multiselect';
import VueNotification from '@mathieustan/vue-notification';
import VuejsDialog from 'vuejs-dialog';
import 'vuejs-dialog/dist/vuejs-dialog.min.css';
import Autocomplete from '@trevoreyre/autocomplete-vue'
import '@trevoreyre/autocomplete-vue/dist/style.css'
import Vue from 'vue'
import VueSwal from 'vue-swal'
import VueRouter from 'vue-router'
import vueCrypt from 'vue-crypt'
import VueModal from '@kouts/vue-modal';
import '@kouts/vue-modal/dist/vue-modal.css';
import { BootstrapVue } from 'bootstrap-vue'
import VModal from 'vue-js-modal'
import VuePaginate from 'vue-paginate';
import ProgressBar from 'vue-simple-progress'
Vue.component('progress-bar', ProgressBar);
import VueEllipseProgress from 'vue-ellipse-progress';
Vue.use(VueEllipseProgress);
import Spinner from 'vue-simple-spinner'
Vue.component('vue-simple-spinner', Spinner);
import VueCarousel from 'vue-carousel';
Vue.use(VueCarousel);
import { slider, slideritem } from 'vue-concise-slider'
Vue.use(slider);
import VueGlide from 'vue-glide-js'
import 'vue-glide-js/dist/vue-glide.css'
Vue.use(VueGlide)

const options = {
  color: '#bffaf3',
  failedColor: '#874b4b',
  thickness: '5px',
  transition: {
    speed: '0.2s',
    opacity: '0.6s',
    termination: 300
  },
  autoRevert: true,
  location: 'top',
  inverse: false
}

Vue.use(VueProgressBar, options)
Vue.use(VuePaginate);
Vue.use(VModal)
Vue.use(vueCrypt);
Vue.component('multiselect', Multiselect);
Vue.use(Vuex);
Vue.use(VueNotification);
Vue.use(VuejsDialog);
Vue.use(Autocomplete);
Vue.use(VueSwal);
Vue.use(VueRouter);
Vue.use(BootstrapVue)
Vue.component('Modal', VueModal);
Vue.component('pagination', require('laravel-vue-pagination'));


Vue.component('dashboardComponent', require('./components/DashboardComponent.vue').default);
Vue.component('usersComponent', require('./components/UsersComponent.vue').default);
Vue.component('moviesComponent', require('./components/MoviesComponent.vue').default);
Vue.component('streamingqualityComponent', require('./components/StreamingQualityComponent.vue').default);
Vue.component('genresComponent', require('./components/GenresComponent.vue').default);
Vue.component('seriesComponent', require('./components/SeriesComponent.vue').default);
Vue.component('streamingComponent', require('./components/StreamingComponent.vue').default);
Vue.component('notificationsComponent', require('./components/NotificationsComponent.vue').default);
Vue.component('settingsComponent', require('./components/SettingsComponent.vue').default);
Vue.component('accountComponent', require('./components/AccountComponent.vue').default);
Vue.component('reportsComponent', require('./components/ReportsComponent.vue').default);
Vue.component('animeComponent', require('./components/AnimeComponent.vue').default);
Vue.component('animevideosComponent', require('./components/AnimevideosComponent.vue').default);
Vue.component('adsComponent', require('./components/AdsComponent.vue').default);
Vue.component('upcomingComponent', require('./components/UpcomingComponent.vue').default);
Vue.component('plansComponent', require('./components/PlansComponent.vue').default);
Vue.component('categoriesComponent', require('./components/CategoriesComponent.vue').default);
Vue.component('suggestionsComponent', require('./components/SuggestionsComponent.vue').default);
Vue.component('castersComponent', require('./components/CastersComponent.vue').default);
Vue.component('featuredComponent', require('./components/FeaturedComponent.vue').default);
Vue.component('networksComponent', require('./components/NetworksComponent.vue').default);
Vue.component('headersComponent', require('./components/HeadersComponent.vue').default);
Vue.component('commentsComponent', require('./components/CommentsComponent.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

new Vue({
  el: '#app',
});

window.tranlate=require('./VueTranslation/Translation').default.translate;
Vue.prototype.translate=require('./VueTranslation/Translation').default.translate;


