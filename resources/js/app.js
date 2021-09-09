require('./bootstrap');

require('moment');

import Vue from 'vue';

import { createInertiaApp } from '@inertiajs/inertia-vue'
import PortalVue from 'portal-vue';

// Multi Language Add
import pl from './Piaf/locales/pl.json'
import VueI18n from 'vue-i18n'

// BootstrapVue add
import BootstrapVue from 'bootstrap-vue'
// Notification Component Add
import Notifications from './Piaf/components/Common/Notification'
// Breadcrumb Component Add
import Breadcrumb from './Piaf/components/Common/Breadcrumb'
// RefreshButton Component Add
import RefreshButton from './Piaf/components/Common/RefreshButton'
// Colxx Component Add
import Colxx from './Piaf/components/Common/Colxx'
// Perfect Scrollbar Add
import vuePerfectScrollbar from 'vue-perfect-scrollbar'
// Vue Dynamic Forms
import VueDynamicForms from '@asigloo/vue-dynamic-forms';

// Router & Store add
import store from './Piaf/store'
//import router from './router';

Vue.mixin({
    methods: {
        route: window.route,
        error(field, errorBag = 'default') {
            if (!this.$page.props.errorBags.hasOwnProperty(errorBag)) {
                return null;
            }
            if (this.$page.props.errorBags[errorBag].hasOwnProperty(field)) {
                return this.$page.props.errorBags[errorBag][field][0];
            }
            return null;
        },
        apiError(field, errors) {
            if (!errors.hasOwnProperty(field)) {
                return null;
            } else {
                return errors[field][0]
            }
            return null;
        }
    }
})
Vue.use(BootstrapVue);
Vue.use(PortalVue);
Vue.use(VueI18n);
Vue.use(VueDynamicForms);

const i18n = new VueI18n({
    locale: 'pl', //getCurrentLanguage();
    fallbackLocale: 'pl',
    messages: { pl: pl }
});

Vue.component('piaf-breadcrumb', Breadcrumb);
Vue.component('b-refresh-button', RefreshButton);
Vue.component('b-colxx', Colxx);
Vue.component('vue-perfect-scrollbar', vuePerfectScrollbar);

createInertiaApp({
    resolve: name => require(`./Pages/${name}`),
    setup({ el, app, props }) {
        new Vue({
            store,
            i18n,
            render: h => h(app, props),
        }).$mount(el)
    },
})

export default {}
