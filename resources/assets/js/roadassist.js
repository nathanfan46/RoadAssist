
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

// var VueRouter = require('vue-router');
// Vue.use(VueRouter);

var store = {
	debug: true,
	state: {
		message: 'Hello!',
		user: {
			FirstName: '',
			LastName: '',
			Phone: '',
			Email: ''
		}
	},
	setMessageAction (newValue) {
		this.debug && console.log('setMessageAction triggered with', newValue)
		this.state.message = newValue
	},
	clearMessageAction () {
		this.debug && console.log('clearMessageAction triggered')
		this.state.message = 'clearMessageAction triggered'
	}
}

import Roadassist from './components/Roadassist.vue';

Vue.component('service-menu', require('./components/Services.vue'));

Vue.component('request-form', require('./components/Request.vue'));

Vue.component('tracking-map', require('./components/Tracking.vue'));


var Services = Vue.extend({
  template: '<service-menu></service-menu>'
});

var Request = Vue.extend({
  template: '<request-form></request-form>'
});

var Tracking = Vue.extend({
  template: '<tracking-map></tracking-map>'
});


const router = new VueRouter({
	mode: 'history',
	routes: [
		{ path: '/', component: Services },
		{ path: '/roadassist', component: Services },
		{ path: '/roadassist/services', component: Services },
		{ path: '/roadassist/request/:code', component: Request },
		{ path: '/roadassist/request', component: Request },
		{ path: '/roadassist/tracking/:hashid', component: Tracking },
		{ path: '/roadassist/tracking', component: Tracking },
		{ path: '*', redirect: '/roadassist' }
	]
});




// var RoadAssist = Vue.extend({});

// router.start(RoadAssist, '#roadassist');

// const roadassist = new Vue({router}).$mount('#roadassist');
const roadassist = new Vue({
    el: '#roadassist', 
    router: router,
    data: {
    	privateState: {},
    	sharedState: store.state
    },
    render: h => h(Roadassist)
});




