
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('../bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

var VueTables = require('vue-tables-2');

Vue.use(VueTables.client, {
    compileTemplates: true,
    highlightMatches: true,
    pagination: {
    dropdown:true,
        chunk:5
    },
    filterByColumn: true,
    texts: {
        filter: "Search:"
    },
    datepickerOptions: {
        showDropdowns: true
    }
});

Vue.use(require('vue-resource'));
Vue.use(VueTables.server, {
    compileTemplates: false,
    highlightMatches: true,
    pagination: {
    dropdown:true,
        chunk:5
    },
    filterByColumn: false,
    texts: {
        filter: "Search:"
    },
    datepickerOptions: {
        showDropdowns: true
    }
});

const roadassist = new Vue({
    el: '#roadassist',    
});




