import Vue from 'vue'
import './plugins/numeral'
import './plugins/apex-charts'
import './plugins/code-highlight'
import './plugins/milliseconds'
import vuetify from './plugins/vuetify'
import render from './plugins/inertia'

new Vue({
    vuetify,
    render
}).$mount('#app')