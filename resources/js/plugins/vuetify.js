import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'

Vue.use(Vuetify)

const opts = {
    themes: {
        light: {
            primary: '#58656f',
            secondary: '#424242',
            default: '#f5f5f5',
            // background: '#E8EAF6',
            background: '#f4f5f7',
            info: '#2196F3',
            warning: '#FB8C00',
            error: '#FF5252',
            success: '#4CAF50'
        }
    }
}

export default new Vuetify(opts)