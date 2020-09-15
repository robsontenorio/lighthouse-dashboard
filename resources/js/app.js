import Vue from 'vue'
import './plugins/moment'
import './plugins/mask'
import './plugins/apex-charts'
import './plugins/highcharts'
import vuetify from './plugins/vuetify'
import render from './plugins/inertia'

new Vue({
    vuetify,
    render
}).$mount('#app')
